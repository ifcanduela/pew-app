<?php

namespace pew;

/**
 * The basic model class, with database description and access methods.
 *
 * @package pew
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Model
{
    /**
     * Database abstraction instance.
     *
     * @var PewDatabase
     */
    public $db = null;

    /**
     * Database table for the subject of the model.
     *
     * @var string
     */
    protected $table = null;

    /**
     * Name of the primary key fields of the table the Model manages.
     *
     * @var string
     */
    protected $primary_key = null;

    /**
     * Miscellaneous table metadata.
     *
     * Holds table name, primary key name, column names, primary text column
     * name (either 'name' or 'title') and values.
     *
     * @var array
     */
    protected $_table_data = array();

    /**
     * Current row data.
     *
     * Holds an index for each table column. It's accessed by the __set and __get
     * magic methods.
     *
     * @var array
     */
    protected $_row_data = array();

    /**
     * Related child models.
     *
     * Holds an index for each related child model (has-many relationship).
     *
     * @var array
     */
    protected $_related_children = array();

    /**
     * Related parent models.
     *
     * Holds an index for each related parent model (belongs-to relationship).
     *
     * @var array
     */
    protected $_related_parents = array();

    /**
     * Whether to query the related tables or not.
     *
     * @var boolean
     */
    protected $_find_related = false;

    /**
     * An associative array of child tables.
     *
     * The simplest way of defining a relationship is as follows:
     *
     *     <code>public $has_many = array('comments' => 'user_id');</code>
     * 
     * This field can also be used with aliases using the following format:
     *
     *     <code>public $has_many = array('user_comments' => array('comments' => 'user_id'));</code>
     *
     * @var array
     */
    protected $has_many = array();

    /**
     * An associative array of parent tables.
     *
     * The simplest way of defining a relationship is as follows:
     *
     *     <code>public $belongs_to = array('users' => 'user_id');</code>
     * 
     * This field can also be used with aliases using the following format:
     *
     *     <code>public $belongs_to = array('owner' => array('users' => 'user_id'));</code>
     *
     * @var array
     */
    protected $belongs_to = array();

    /**
     * Whether or not the related models have been initialised.
     */
    protected $_initialised = false;

    /**
     * Fields to retrieve in SELECT statements.
     *
     * @var string
     */
    protected $_fields = '*';
    protected $fields = '*';

    /**
     * Conditions for the queries.
     *
     * @var string
     */
    protected $_where = array();
    protected $where = array();

    /**
     * Sorting order for the query results.
     *
     * @var string
     */
    protected $_order_by = null;
    protected $order_by = null;

    /**
     * Sorting order for the query results.
     *
     * @var string
     */
    protected $_limit = null;
    protected $limit = null;

    /**
     * Grouping of fields for the query results.
     *
     * @var string
     */
    protected $_group_by = null;
    protected $group_by = null;

    /**
     * Conditions for the query result groups.
     *
     * @var string
     */
    protected $_having = array();
    protected $having = array();

    /**
     * The constructor builds the model!.
     *
     * @param string $table Name of the table
     * @return array An indexed array with all fetched rows, in associative arrays
     */
    public function __construct($db, $table = null)
    {
        # get the Database class instance
        $this->db = $db;

        if (!is_null($table)) {
            $this->table = $table;
        } elseif (class_base_name(get_class($this)) === 'Model') {
            # if this is an instance of the Model class, get the
            # table from the $table parameter
            throw new Exception('Model constructor error: Models must be assigned to a table.');
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

        foreach ($this->belongs_to as $alias => $fk) {
            $this->add_related_model('parent', $alias, $fk);
        }

        foreach ($this->has_many as $alias => $fk) {
            $this->add_related_model('child', $alias, $fk);
        }
    }

    /**
     * Get or set the table name for the model.
     * 
     * @param string $table Table name
     * @return string Table name
     */
    public function table($table = null)
    {
        if (!is_null($table)) {
            $this->table = $table;
        }

        return $this->table;
    }

    /**
     * Configures related models.
     *
     * @param string $relationship_type Either 'child' or 'parent'
     * @param string $alias Name of the relationship
     * @param string|array $fk The name of the FK or a array with [table_name, FK_name]
     * @param string $table_name Table of the related model
     * @return boolean false if the table does not exist, true otherwise
     */
    protected function add_related_model($relationship_type, $alias, $fk)
    {
        $table = $alias;

        if (is_array($fk)) {
            list($table, $foreign_key) = $fk;
        } else {
            $foreign_key = $fk;
        }

        $model = null;

        if ($this->db->table_exists($table)) {
            switch ($relationship_type) {
                case 'child':
                        $this->_related_children[$alias] = compact('table', 'foreign_key', 'alias', 'model');
                    break;
                case 'parent':
                        $this->_related_parents[$alias] = compact('table', 'foreign_key', 'alias', 'model');
                    break;
                default:
                    throw new InvalidArgumentException();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds a has-many relationship to the model.
     *
     * @param type $alias The related table name or an alias if $foreign_key is an array
     * @param string|array $foreign_key The foreign key in this model's table,  or an 
     *     array with [table_name, FK_name]
     * @return Model The model object ($this)
     */
    public function add_child($table, $foreign_key)
    {
        $this->add_related_model('child', $table, $foreign_key);

        return $this;
    }

    /**
     * Adds a belongs-to relationship to the model.
     *
     * @param type $alias The related table name or an alias if $foreign_key is an array
     * @param string|array $foreign_key The foreign key in this model's table,  or an 
     *     array with [table_name, FK_name]
     * @return Model The model object ($this)
     */
    public function add_parent($table, $foreign_key)
    {
        $this->add_related_model('parent', $table, $foreign_key);

        return $this;
    }

    /**
     * Removes a has-many relationship from the model.
     *
     * @param string $table The related table
     * @return Model The model object
     */
    public function remove_child($table)
    {
        if (array_key_exists($table, $this->_related_children)) {
            unset($this->_related_children[$table]);
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
        if (array_key_exists($table,  $this->_related_parents)) {
            unset($this->_related_parents[$table]);
        }

        return $this;
    }

    /**
     * Getter for related tables.
     *
     * @param string $field Field name to retrieve
     * @return mixed Field value if field exists, false otherwise
     */
    public function __get($related_model_alias)
    {
        $model_info = null;

        if (array_key_exists($related_model_alias, $this->_related_parents)) {
            $model_info =& $this->_related_parents[$related_model_alias];
        }

        if (array_key_exists($related_model_alias, $this->_related_children)) {
            $model_info =& $this->_related_children[$related_model_alias];
        }

        if ($model_info) {
            if (is_null($model_info['model'])) {
                $model_info['model'] = Pew::model($model_info['table']);
            }
            
            return $model_info['model'];
        } else {
            return null;
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
     */
    public function query($query)
    {
        $query = trim($query);

        if (strtoupper(substr($query, 0, 7)) == 'SELECT ') {
            # query is a SELECT, so try to return an array
            $ret = $this->db->pdo->query($query)->fetchAll() or die($this->db->pdo->errorInfo());
        } else {
            # just run the query
            $ret = $this->db->pdo->exec($query);
            if ($ret === false) {
                die($this->db->pdo->errorCode());
            }
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
     */
    public function find($id)
    {
        # if $id is not numeric, use it as a conditions array
        if (is_array($id)) {
            $this->where($id);
        } else {
            $this->where(array($this->primary_key => $id));
        }

        #query the database
        $result = $this->db
                        ->where($this->where())
                        ->group_by($this->group_by())
                        ->having($this->having())
                        ->limit($this->limit())
                        ->order_by($this->order_by())
                        ->single($this->table, $this->_fields);

        $this->reset();

        if ($result) {
            $id = $result[$this->_table_data['primary_key']];

            # map the fields to the $_table_data['data'] array
            foreach ($result as $key => $value) {
                $this->_table_data['data'][$key] = $value;
            }

            if ($this->_find_related) {
                # disable the find_related behavior for later calls
                $this->_find_related = false;

                # search for child tables
                foreach ($this->_related_children as $alias => $child) {
                    # use the associated model to find related items
                    $result[$alias] = $this->$alias->find_all(array($child['foreign_key'] => $id));
                }

                # search for parent tables
                foreach ($this->_related_parents as $alias => $parent) {
                    # use the associated model to find related items
                    $result[$alias] = $this->$alias->find($result[$parent['foreign_key']]);
                }
                
                # re-enable the find_related behavior for later calls
                $this->_find_related = true;
            }
        } else {
            # if there was no result, return false
            $this->_table_data['data'] = null;
            $result = false;
        }

        if (method_exists($this, 'after_find')) {
            $result = current($this->after_find(array($result)));
        }

        return $result;
    }

    /**
     * Retrieve all items from a table.
     *
     * @param array $where An associative array with WHERE conditions.
     * @return array An array with the resulting records
     */
    public function find_all($where = null)
    {
        # if conditions are provided, overwrite the previous model conditions
        if (is_array($where)) {
            $this->where($where);
        }

        # query the database
        $result = $this->db
                    ->where($this->where())
                    ->group_by($this->group_by())
                    ->having($this->having())
                    ->limit($this->limit())
                    ->order_by($this->order_by())
                    ->select($this->table, $this->_fields);

        $this->reset();

        if ($result) {
            if ($this->_find_related) {
                # disable the find_related behavior for later calls
                $this->_find_related = false;

                # search child and parent tables
                foreach ($result as $key => $value) {
                    $id = $value[$this->primary_key];

                    foreach ($this->_related_children as $alias => $child) {
                        # prepare the find_all call
                        $this->$alias->where(array($child['foreign_key'] => $value[$this->_table_data['primary_key']]));
                        # use the associated model to find related items
                        $result[$key][$alias] = $this->$alias->find_all();
                    }

                    foreach ($this->_related_parents as $alias => $parent) {
                        # use the associated model to find related items
                        $result[$key][$parent['alias']] = $this->$alias->find($value[$parent['foreign_key']]);
                    }
                }

                # re-enable the find_related behavior for later calls
                $this->_find_related = true;
            }
        } else {
            # return empty array if there was no result
            $result = array();
        }

        if (method_exists($this, 'after_find')) {
            $result = $this->after_find($result);
        }

        return $result;
    }

    /**
     * Count the rows that fit the criteria.
     *
     * @param array $where An associative array with field name/field value
     *                   pairs for the WHERE clause.
     * @return int
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
     */
    public function save($data)
    {
        $record = array();

        if (method_exists($this, 'before_save')) {
            $data = $this->before_save($data);
        }

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

        if (method_exists($this, 'after_save')) {
            $result = $this->after_save($result);
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
     */
    public function where($conditions = null)
    {
        if (!is_null($conditions)) {
            $this->_where = $conditions;
            $this->db->where($conditions);

            return $this;
        } else {
            if (isset($this->_where)) {
                return $this->_where;
            } else {
                return $this->where;
            }
        }
    }

    /**
     * Specify the maximum amount of records to retrieve, and an optional
     * starting offset.
     *
     * @param int $count Number of items to return
     * @param int $start First item to return
     * @return Model a reference to the same object, for method chaining
     */
    public function limit($count = null, $start = 0)
    {
        if (is_numeric($count)) {
            if (isset($start) && is_numeric($start)) {
                $this->_limit = "$start, $count";
            } else {
                $this->_limit = $count;
            }

            return $this;
        } else {
            if (isset($this->_limit)) {
                return $this->_limit;
            } else {
                return $this->limit;
            }
        }

    }

    /**
     * Set the record sorting for results.
     *
     * @param mixed $order_by Order-by SQL clauses[multiple]
     * @return Model a reference to the same object, for method chaining
     */
    public function order_by($order_by = null)
    {
        if (!is_null($order_by)) {
            $this->_order_by = $order_by;
            return $this;
        } else {
            if (isset($this->_order_by)) {
                return $this->_order_by;
            } else {
                return $this->order_by;
            }
        }
    }

    /**
     * This function is a shortcut to enable method chaining with the Group By
     * SQL clause.
     *
     * @param string $groups Grouping column names
     * @return Model a reference to the same object, for method chaining
     * @todo: Make this work
     */
    public function group_by($group_by = null)
    {
        if (!is_null($group_by)) {
            $this->_group_by= $group_by;
            return $this;
        } else {
            if (isset($this->_group_by)) {
                return $this->_group_by;
            } else {
                $this->group_by;
            }
        }
    }

    /**
     * This function is a shortcut to enable method chaining with the Having SQL
     * clause.
     *
     * @param string $conditions SQL conditions for the groups
     * @return Model a reference to the same object, for method chaining
     * @todo: Make this work
     */
    public function having($having = null)
    {
        if (!is_null($having)) {
            $this->_having= $having;
            return $this;
        } else {
            if (isset($this->_having)) {
                return $this->_having;
            } else {
                $this->having;
            }
        }
    }

    public function begin()
    {
        return $this->db->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->db->pdo->commit();
    }

    public function rollback()
    {
        return $this->db->pdo->rollback();
    }

    protected function reset()
    {
        $this->_order_by = null;
        $this->_group_by = null;
        $this->_having = null;
        $this->_where = null;
        $this->_limit = null;
        $this->_fields = '*';
    }
}
