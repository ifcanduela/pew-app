<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/**
 * The basic model class, with database description and access methods.
 *
 * @version 0.11 22-sep-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Model
{
    /**
     * Database abstraction instance.
     *
     * @var PewDatabase
     * @access public
     */
    public $db = null;

    /**
     * Database table for the subject of the model.
     *
     * @var string
     * @access protected
     */
    protected $table = null;

    /**
     * Name of the primary key fields of the table the Model manages.
     *
     * @var string
     * @access protected
     */
    protected $primary_key = null;

    /**
     * Miscellaneous table metadata.
     *
     * Holds table name, primary key name, column names, primary text column
     * name (either 'name' or 'title') and values.
     *
     * @var array
     * @access protected
     */
    protected $_table_data = array();

    /**
     * Current row data.
     *
     * Holds an index for each table column. It's accessed by the __set and __get
     * magic methods.
     *
     * @var array
     * @access protected
     */
    protected $_row_data = array();

    /**
     * Related models.
     *
     * Holds an index for each related models.
     *
     * @var array
     * @access protected
     */
    protected $_related_models = array();

    /**
     * Name of the main field of the model (usually 'name', 'label' or 'title').
     *
     * @var string
     * @access protected
     * @deprecated since 0.7
     */
    protected $title = 'name';

    /**
     * Whether to query the related tables or not.
     *
     * @var boolean
     * @access protected
     */
    protected $_find_related = false;

    /**
     * Child tables, an associative array of 'table_name' =>
     * 'foreign_key' elements.
     *
     * @var array
     * @access protected
     */
    protected $has_many = array();

    /**
     * Parent tables, an associative array of 'table_name' =>
     * 'foreign_key' elements.
     *
     * @var array
     * @access protected
     */
    protected $belongs_to = array();

    /**
     * Fields to retrieve in SELECT statements.
     *
     * @var string
     * @access protected
     */
    protected $_fields = '*';

    /**
     * Conditions for the queries.
     *
     * @var string
     * @access protected
     */
    protected $_where = array();

    /**
     * Sorting order for the query results.
     *
     * @var string
     * @access protected
     */
    protected $_order_by = null;

    /**
     * Sorting order for the query results.
     *
     * @var string
     * @access protected
     */
    protected $_limit = null;

    /**
     * Grouping of fields for the query results.
     *
     * @var string
     * @access protected
     */
    protected $_group_by = null;

    /**
     * Conditions for the query result groups.
     *
     * @var string
     * @access protected
     */
    protected $_having = array();

    /**
     * The constructor builds the model!.
     *
     * @param string $table Name of the table
     * @return array An indexed array with all fetched rows, in associative arrays
     * @access public
     * @todo Test trigger_error and pew_exit
     */
    public function __construct($table = null)
    {
        # get the Database class instance
        $this->db = Pew::get_database();

        if (get_class($this) === 'Model') {
            # if this is an instance of the Model class, get the
            # table from the $table parameter
            if (!is_string($table)) {
                throw new Exception('Model constructor error: Models must be assigned to a table.');
            }
            $this->table = $table;
        } elseif (!$this->table) {
            # else, if $table is not set in the Model class file,
            # guess the table name
            $this->table = str_replace('_model', '', class_name_to_file_name(get_class($this)));
        }

        # some metadata about the table
        $this->_table_data['name'] = $this->table;
        $this->_table_data['primary_key'] = $this->db->get_pk($this->table);
        $this->_table_data['columns'] = $this->db->get_cols($this->table);

        if (!$this->primary_key) {
            $this->primary_key = $this->_table_data['primary_key'];
        }
    }

    /**
     * Instances related models.
     *
     * @param string $table_name Table of the related model
     * @return boolean false if the table does not exist, true otherwise
     * @access protected
     * @todo Return false upon error? Throw exception?
     */
    protected function add_related_model($table_name)
    {
        if (!isset($this->_related_models[$table_name]) || !is_object($this->_related_models[$table_name])) {
            if ($this->db->table_exists($table_name)) {

                $model_class_name = file_name_to_class_name($table_name . '_model');
                $this->_related_models[$table_name] = Pew::get_model($model_class_name);
    
                if (!$this->_related_models[$table_name]) {
                    $this->_related_models[$table_name] = new Model($table_name);
                }
            } else {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Adds a has-many relationship to the model.
     *
     * @param string $table The related table
     * @param string $foreign_key The foreign key in the related table
     * @return Model The model object
     */
    public function add_child($table, $foreign_key)
    {
        $this->has_many += array($table => $foreign_key);

        return $this;
    }

    /**
     * Adds a belongs-to relationship to the model.
     *
     * @param type $table The related table
     * @param type $foreign_key The foreign key in this model's table
     * @return Model The model object
     */
    public function add_parent($table, $foreign_key)
    {
        $this->belongs_to += array($table => $foreign_key);

        return $this;
    }

    /**
     * removes a has-many relationship from the model.
     *
     * @param string $table The related table
     * @return Model The model object
     */
    public function remove_child($table)
    {
        if (array_key_exists($table, $this->has_many)) {
            unset($this->has_many[$table]);
        }

        return $this;
    }

    /**
     * Removes a belongs-to relationship from the model.
     *
     * @param type $table The related table
     * @return Model The model object
     */
    public function remove_parent($table)
    {
        if (array_key_exists($table,  $this->belongs_to)) {
            unset( $this->belongs_to[$table]);
        }

        return $this;
    }

    /**
     * Getter for table fields and related tables.
     *
     * @param string $field Field name to retrieve
     * @return mixed Field value if field exists, false otherwise
     * @access public
     */
    public function __get($field)
    {
        if (in_array($field, $this->_table_data['columns']) && isset($this->_row_data[$field])) {
            return $this->_row_data[$field];
        } elseif ($this->add_related_model($field)) {
            
            return $this->_related_models[$field];
        } else {
            return false;
        }
    }

    /**
     * Setter for table fields and related tables.
     *
     * @param string $field Field name to set
     * @param mixed $value Field value to set
     * @return mixed Field value if field exists, false otherwise
     * @access public
     */
    public function __set($field, $value)
    {
        if (array_key_exists($field, $this->_table_data['columns'])) {
            $this->_row_data[$field] = $value;
            return true;
        } elseif (array_key_exists($field, $this->has_many)
               || array_key_exists($field, $this->belongs_to)) {
            $this->_related_models[$field] = $value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Find by field name.
     *
     * This magic method manages find_by_<field name> and
     * find_all_by_<field_name> method calls.
     *
     * @param string $field Method to be called
     * @param array $arguments Argumments passed to the method
     * @return The return value of the method called
     */
    public function __call($field, $arguments)
    {
        $results = preg_match('/(find|find_all)_by_(.*)/', $field, $matches);

        if ($results) {
            $value = $arguments[0];
            list(, $method, $field) = $matches;
            return $this->$method(array($field => $value));
        } else {
            return trigger_error('Method does not exist or is not available: ' . $field, E_USER_ERROR);
        }
    }

    /**
     * Simple transitional function to run a query directly.
     *
     * This function interacts directly with the PDO abstraction layer of the
     * PewDatabase object. It invokes PDO::query() to run SELECT statements and
     * returns all rows, or invokes PDO::exec() for INSERT, UPDATE and DELETE
     * and return an integer with the number of affected rows.
     *
     * @param string $query The query to run
     * @return mixed The result from the query, either an array or an integer
     * @access public
     */
    public function query($query)
    {
        $query = $this->db->pdo->quote(trim($query));

        if (strtoupper(substr($query, 0, 7)) == 'SELECT ') {
            # query is a SELECT, so try to return an array
            $ret = $this->db->pdo->query($query)->fechAll() or die($this->db->pdo->errorInfo());
        } else {
            # just run the query
            $ret = $this->db->pdo->exec($query) or die($this->db->pdo->errorInfo());
        }

        return $ret;
    }

    /**
     * Retrieve a single item from the model table using its primary key.
     *
     * Since 0.6, this function accepts an associative array as parameter,
     * which enables custom conditions other than PK = $id.
     *
     * @param int $id Value to match to the primary key of the model table, or
     *                an associative array with field name/ field value pairs.
     * @return array An associative array with the row fields, or false
     * @access public
     */
    public function find($id)
    {
        # if $id is not numeric, use it as a conditions array
        if (is_array($id)) {
            $result = $this->db->where($id)->single($this->table, $this->_fields);
        } else {
            $result = $this->db->where(array($this->primary_key => $id))->single($this->table, $this->_fields);
        }

        if ($result) {
            $id = $result[$this->_table_data['primary_key']];

            # map the fields to the $_table_data['data'] array
            foreach ($result as $key => $value) {
                $this->_table_data['data'][$key] = $value;
            }

            if ($this->_find_related) {

                # search for child and parent tables
                foreach ($this->has_many as $alias => $r_value) {
                    if (is_array($r_value)) {
                        list($table, $foreign_key) = $r_value;
                    } else {
                        $table = $alias;
                        $foreign_key = $r_value;
                    }
                    $result[$alias] = $this
                        ->$table
                        ->find_all(array($foreign_key => $id));
                }

                foreach ($this->belongs_to as $alias => $r_value) {
                    if (is_array($r_value)) {
                        list($table, $foreign_key) = $r_value;
                    } else {
                        $table = $alias;
                        $foreign_key = $r_value;
                    }
                    $result[$alias] = $this
                        ->$table
                        ->find($result[$foreign_key]);
                }
                # reset the find_children behavior for later calls
                $this->_find_related = false;
            }
        } else {
            # if there was no result, return false
            $this->_table_data['data'] = null;
            $result = false;
        }

        return $result;
    }

    /**
     * Retrieve all items from a table.
     *
     * @param array $where An associative array with field name/field value
     *                   pairs for the WHERE clause.
     * @return array An indexed array with all fetched rows, in associative
     *               arrays, or false if nothing was returned
     * @access public
     */
    public function find_all($where = null)
    {
        # if conditions are provided, overwrite the previous model conditions
        if (is_array($where)) {
            $this->_where = $where;
        }

        # query the database
        $result = $this->db
                    ->where($this->_where)
                    ->group_by($this->_group_by)
                    ->having($this->_having)
                    ->limit($this->_limit)
                    ->order_by($this->_order_by)
                    ->select($this->table, $this->_fields);

        if ($result) {
            if ($this->_find_related) {
                # search child and parent tables
                foreach ($result as $key => $value) {
                    $id = $value[$this->primary_key];

                    foreach ($this->has_many as $alias => $r_value) {
                        if (is_array($r_value)) {
                            list($table, $foreign_key) = $r_value;
                        } else {
                            $table = $alias;
                            $foreign_key = $r_value;
                        }
                        # prepare the find_all call
                        $this->$table->where(array($foreign_key => $value[$this->_table_data['primary_key']]));
                        # use the associated model to find related items
                        $result[$key][$alias] = $this->_related_models[$table]->find_all();
                    }

                    foreach ($this->belongs_to as $alias => $r_value) {
                        if (is_array($r_value)) {
                            list($table, $foreign_key) = $r_value;
                        } else {
                            $table = $alias;
                            $foreign_key = $r_value;
                        }
                        # use the associated model to find related items
                        $result[$key][$alias] = $this->$table->find($value[$foreign_key]);
                    }
                }
                # reset the fin_children behavior for later calls
                $this->_find_related = false;
            }
        } else {
            # return null if there was no result
            $result = false;
        }

        return $result;
    }

    /**
     * Count the rows that fit the criteria.
     *
     * @param array $where An associative array with field name/field value
     *                   pairs for the WHERE clause.
     * @return int
     * @access public
     */
    public function count($where = null)
    {
        # if conditions are provided, overwrite the previous model conditions
        if (is_array($where)) {
            $this->_where = $where;
        }

        # query the database
        $result = $this->db
                    ->fields('count(*)')
                    ->where($this->_where)
                    ->group_by($this->_group_by)
                    ->having($this->_having)
                    ->limit(1)
                    ->cell($this->table);

        return $result;
    }


    /**
     * Saves a row to the table.
     *
     * If the $primary_key field is set, it performs an UPDATE. If not, it
     * INSERTs the data.
     *
     * @param array $data An associative array with database fields and values
     * @return mixed The saved item on success, false otherwise
     * @access public
     */
    public function save($data)
    {
        $record = array();
        
        foreach ($data as $key => $value) {
            if (in_array($key, $this->_table_data['columns'])) {
                $record[$key] = $value;
            }
        }
        
        if (isset($record[$this->primary_key])) {
            # if $id is set, preform an UPDATE
            $result = $this->db->set($record)->where(array($this->primary_key => $record[$this->primary_key]))->update($this->table);
            $result = $this->db->where(array($this->primary_key => $record[$this->primary_key]))->single($this->table);
        } else {
            # if $id is not set, perform an INSERT
            $result = $this->db->values($record)->insert($this->table);
            $result = $this->db->where(array($this->primary_key => $result))->single($this->table);
        }

        return $result;
    }

    /**
     * Deletes one or more rows from the table.
     *
     * If the $primary_key field is set, it deletes the corresponding row. If
     * not, the $where field is used to delete conditionally. If $id is boolean
     * true, it clears the full table.
     *
     * @param mixed $id The value of the PK field of the row to delete, null to
     *                  use the model's $where conditions, or boolean true to
     *                  delete every record in the table
     * @return bool True on success, false other wise
     * @access public
     */
    public function delete($id = null)
    {
        if (is_array($id)) {
            # use the $id as an array of conditions
            return $this->db->where($id)->delete($this->table);
        } elseif ($id === true) {
            # this deletes everything in $this->table
            return $this->db->delete($this->table);
        } elseif (!is_null($id) && !is_bool($id)) {
            # delete the item as received, ignoring previous conditions
            return $this->db->where(array($this->primary_key => $id))->limit(1)->delete($this->table);
        } else {
            # delete everything that matches the conditions
            return $this->db->where($this->_where)->delete($this->table);
        }
    }

    /**
     * Enables or disables the recursive find functionality.
     *
     * If the $status argument is not true or false, this method just returns
     * the status.
     *
     * @param bool $status Status
     * @return bool The status of the functionality
     * @access public
     */
    public function find_related($status = null)
    {
        if (is_bool($status)) {
            $this->_find_related = $status;
        }

        return $this->_find_related;
    }

    /**
     * Returns the primary key value created in the last INSERT statement.
     *
     * @return mixed The primaary key value of the last inserted row
     * @access public
     */
    public function last_insert_id()
    {
        return $this->db->pdo->LastInsertId();
    }

    /**
     * State which fields to retrieve with find() and find_all().
     *
     * @param string $fields A comma-separated list of table columns
     * @return Model a reference to the same object, for method chaining
     * @access public
     */
    public function select($fields)
    {
        $this->_where = $conditions;
        $this->db->where($conditions);

        return $this;
    }

    /**
     * State the conditions of the records to fetch with find() and find_all().
     *
     * @param array $conditions Field and value pairs
     * @return Model a reference to the same object, for method chaining
     * @access public
     */
    public function where($conditions)
    {
        $this->_where = $conditions;
        $this->db->where($conditions);

        return $this;
    }

    /**
     * Specify the maximum amount of records to retrieve, and an optional
     * starting offset.
     *
     * @param int $count Number of items to return
     * @param int $start First item to return
     * @return Model a reference to the same object, for method chaining
     * @access public
     */
    public function limit($count, $start = 0)
    {
        if (is_numeric($count)) {
            if (isset($start) && is_numeric($start)) {
                $this->_limit = "$start, $count";
            } else {
                $this->_limit = $count;
            }
        }
        $this->db->limit($this->_limit);

        return $this;
    }

    /**
     * Set the record sorting for results.
     *
     * @param mixed $order_by Order-by SQL clauses[multiple]
     * @return Model a reference to the same object, for method chaining
     * @access public
     */
    public function order_by()
    {
        $clauses = func_get_args();
        $this->_order_by= join(', ', $clauses);
        $this->db->order_by($this->_order_by);

        return $this;
    }

    /**
     * This function is a shortcut to enable method chaining with the Group By
     * SQL clause.
     *
     * @param string $groups Grouping column names
     * @return Model a reference to the same object, for method chaining
     * @access public
     * @todo: Make this work
     */
    public function group_by($groups)
    {
        $this->_group_by= $groups;
        $this->db->group_by($this->_group_by);

        return $this;
    }

    /**
     * This function is a shortcut to enable method chaining with the Having SQL
     * clause.
     *
     * @param string $conditions SQL conditions for the groups
     * @return Model a reference to the same object, for method chaining
     * @access public
     * @todo: Make this work
     */
    public function having($conditions)
    {
        $this->_having= $conditions;
        $this->db->having($this->_having);

        return $this;
    }
}
