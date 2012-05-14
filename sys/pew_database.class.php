<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/**
 * The PewDatabase class encapsulates database access.
 *
 * PewDatabase implements PHP Data Objects (PDO) to provide a homogeneous
 * interface for multiple Relational Database Management Systems. Currently
 * available are SQLite and MySQL. Configuration is defined via the
 * DatabaseConfiguration class (found in /app/config) or an array passed into
 * the constructor.
 *
 * The methods contained within this class are aimed to simplify basic database
 * operations, such as simple selects, inserts and updates.
 *
 * The PDO object is public to facilitate complex queries. This class can be
 * used as a singleton by setting the constructor to protected and calling the
 * instance() static method. However, that is not recommended.
 *
 * One of the ways of using this class is the following:
 *
 *      $pdb = new PewDatabase();
 *      $my_cat = $pdb->where(array('name' => 'Cuddles'))->single('cats');
 * 
 * @version 0.12 14-may-2012
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 * @todo Decouple this class from DatabaseConfiguration
 */
class PewDatabase
{
    /**
     * Database singleton.
     * 
     * @var PewDatabase
     * @access protected
     * @static
     * @deprecated since 0.9
     */
    protected static $instance;
    
    /**
     * PHP Data Object for database access.
     *
     * The PDO object is public for advanced operations, such as
     * transactions, commints or rollbacks.
     * 
     * @var PDO
     * @access public
     */
    public $pdo = null;

    /**
     * Connection flag.
     *
     * Indicates whether the PDO object is connected to the database or not.
     * 
     * @var bool
     * @access protected
     */
    protected $_is_connected = false;
    
    /**
     * Configuration parameters.
     *
     * @var array
     * @access protected
     */
    protected $_config = false;

    /**
     * Last query run
     * 
     * @var string
     * @access public
     */
    public $last_query = null;
    
    /**
     * A string containing a list of tables for SQL statements.
     *
     * @var string
     * @access protected
     */
    protected $from = null;
    
    /**
     * A string containing a list of fields for SELECT or INSERT statements.
     *
     * @var string
     * @access protected
     */
    protected $fields = '*';
    
    /**
     * A string containing a SQL-formatted WHERE clause.
     *
     * @var array
     * @access protected
     */
    protected $where = null;

    /**
     * A string containing a SQL-formatted LIMIT clause.
     *
     * @var string
     * @access protected
     */
    protected $limit = null;

    /**
     * A string containing a SQL-formatted GROUP BY clause.
     *
     * @var string
     * @access protected
     */
    protected $group_by = null;

    /**
     * A string containing a SQL-formatted HAVING clause.
     *
     * @var array
     * @access protected
     */
    protected $having = null;
    
    /**
     * A string containing a SQL-formatted ORDER BY clause.
     *
     * @var string
     * @access protected
     */
    protected $order_by = null;
    
    /**
     * A string containing an SQL-formatted VALUES clause.
     *
     * @var array
     * @access protected
     */
    protected $values = null;
    
    /**
     * A string containing an SQL-formatted SET clause.
     *
     * @var array
     * @access protected
     */
    protected $set = null;
    
    /**
     * An automatically-populated array of tag/value pairs for use in prepared
     * statements.
     *
     * @var array
     * @access protected
     */
    protected $tags = array();
    
    /**
     * Number of tagged parameters in a prepared statement.
     *
     * @var int
     * @access protected
     * @static
     */
    protected static $tag_count = 0;
     
    
    /**
     * An array of tag/value pairs for use in prepared statements with WHERE.
     * 
     * @var array
     * @access protected
     */
    protected $where_tags = array();
    
    /**
     * An array of tag/value pairs for use in prepared statements with SET.
     * 
     * @var array
     * @access protected
     */
    protected $set_tags = array();
    
    /**
     * An array of tag/value pairs for use in prepared statements with INSERT.
     * 
     * @var array
     * @access protected
     */
    protected $insert_tags = array();
    
    /**
     * An array of tag/value pairs for use in prepared statements with HAVING.
     * 
     * @var array
     * @access protected
     */
    protected $having_tags = array();
    
    const MYSQL  = 'mysql';
    const SQLITE = 'sqlite';
    
    /**
     * Build the connection string and connect to the selected database engine.
     *
     * Connects to the specified database engine and sets PDO error mode to
     * ERRMODE_EXCEPTION.
     * 
     * @param array $config Alternate configuration settings
     * @access public
     * @throws InvalidArgumentException If the DB engine is not selected
     */
    public function __construct($config = null)
    {
        if (isset($config)) {
            $this->config['use'] = $config;
        } elseif (defined('USEDB') && USEDB) {
            $use = !is_string(USEDB) ? 'default' : USEDB;
            $this->config['use'] = $this->config[$use];
        }
        
        if (!isset($this->config['use']['engine'])) {
            throw new InvalidArgumentException('Database engine was not selected');
        }
        
        self::$instance = $this;
        
        $this->connect();
        
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Connects to the configured database provider.
     *
     * @returns bool True if the connection was successful, false otherwise
     * @access protected
     */
    protected function connect()
    {
        if (!$this->_is_connected) {
            extract($this->config['use']);
            
            try {
                switch ($engine) {
                    case self::SQLITE:
                        $this->pdo = new PDO($engine . ':' . $file);
                        if ($file !== ':memory:' && filesize($file) == 0 && function_exists('sqlite_init')) {
                            sqlite_init($this->pdo);
                        }
                    break;
                    
                    case self::MYSQL:
                    default:
                        $this->pdo = new PDO(
                            $engine . ':dbname=' . $name . ';host=' . $host,
                            $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'")
                        );
                    break;
                }
                
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $this->_is_connected = true;
            } catch (PDOException $e) {
                $this->_is_connected = false;
                throw new Exception('PDO connection failed: ' . $e->getMessage());
            }
         }
        
        return $this->_is_connected;
    }
    
    /**
     * Private to prevent multiple instances of the data access object.
     *
     * @return void
     * @access protected
     */
    protected function __clone() {die('Cloning Databases is a bad habit.');}
    
    /**
     * Obtains a singleton instance of the Database.
     *
     * Call this static function to begin using the class.
     *
     * @return PewDatabase The instance of the PewDatabase class
     * @access public
     * @static
     * @deprecated since 0.9
     */
    public static function instance()
    {
        if (!self::$instance) {
            $classname = __CLASS__;
            return self::$instance = new $classname;
        }
        
        return self::$instance;
    }
    
    /**
     * Sets the FROM field for subsequent queries.
     *
     * @param string $from The list of tables against which to perform the query
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function from($from)
    {
        $this->from = $from;
        return $this;
    }
    
    /**
     * Sets the INTO field for INSERT queries.
     *
     * This function is an alias for PewDatabase::from()
     *
     * @param string $from The list of tables against which to perform the query
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function into($into)
    {
        $this->from = $into;
        return $this;
    }

    /**
     * Sets the fields to return in SELECT queries.
     *
     * @param string $fields A SQl-formatted field list
     * @return PewDataabse The PewDatabase object
     * @access public
     */
    public function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Sets the WHERE field and its values for prepared statements.
     *
     * @param string $from The list of tables against which to perform the query
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function where($conditions)
    {
        list($this->where_tags, $this->where) = $this->build_tags($conditions, 'w_');
        
        return $this;
    }
    
    /**
     * Sets the GROUP BY field and their values for an INSERT prepared
     * statement.
     *
     * Don't add 'GROUP BY' to the $order_by parameter.
     *
     * @param string $group_by A SQL-formatted list of grouping fields
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function group_by($group_by)
    {
        if ($group_by = trim($group_by)) {
            $this->group_by = " GROUP BY $group_by ";
        }
        
        return $this;
    }
    
    /**
     * Sets the HAVING field and its values for prepared statements.
     *
     * @param string $conditions An array of field/value pairs
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function having($conditions)
    {
        list($this->having_tags, $this->having) = $this->build_tags($conditions, 'h_', ' HAVING ');
        
        return $this;
    }
    
    /**
     * Sets the ORDER BY field and their values for an INSERT prepared
     * statement.
     *
     * Don't add 'ORDER BY' to the $order_by parameter.
     *
     * @param string $order_by A SQL-formatted list of sorting fields
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function order_by($order_by)
    {
        if ($order_by = trim($order_by)) {
            $this->order_by = " ORDER BY $order_by ";
        }
        
        return $this;
    }
    
    /**
     * Sets the LIMIT clause for a prepared statement.
     *
     * E.G.: Use "1" to return one row or "4,1" to return the fourth row.
     *
     * @param string $limit Either "row_count", or "offset, row_count"
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function limit($limit)
    {
        if ($limit = trim($limit)) {
            $this->limit = " LIMIT $limit ";
        }
        
        return $this;
    }

    /**
     * Sets the SET field and its values for UPDATE prepared statements.
     *
     * @param string $set An array of field/value pairs
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function set($set)
    {
        list($this->set_tags, $this->set) = $this->build_tags($set, 's_', ' SET ', ', ');
        
        return $this;
    }
    
    /**
     * Sets the INTO and VALUES fields and their values for an INSERT prepared
     * statement.
     *
     * @param string $values An array of field/value pairs
     * @return PewDatabase The PewDatabase object
     * @access public
     */
    public function values($values)
    {
        list($this->insert_tags) = $this->build_tags($values, 'i_');
        $this->fields = join(', ', array_keys($values));
        $this->values = ' VALUES (' . join(', ', array_keys($this->insert_tags)) . ') ';
        return $this;
    }
    
    /**
     * Finds the Primary Key fields of a table.
     *
     * The most common return is a string with the name of the primary key
     * column (for example, "id"). If the primary key is composite, this
     * method will return all primary keys in a comma-separated string, except
     * if the second parameter is specified as true, in which case the return
     * will be an array.
     *
     * @param string $table_name Name of the table in the database
     * @param bool $as_array Return multiple keys as an array (default is true)
     * @return mixed A comma-separated string with the primary key fields or an
     *               array if $as_array is true
     * @access public
     */
    public function get_pk($table, $as_array = false)
    {
        if (!$this->connect()) {
            throw new PDOException;
        }
        
        $pk = array();
        
        switch ($this->config['use']['engine']) {
            case self::SQLITE:
                $sql = "PRAGMA table_info({$table})";
                $primary_key_index = 'pk';
                $primary_key_value = 1;
                $table_name_index = 'name';
            break; 
            
            case self::MYSQL:
            default:
                $sql = "SHOW COLUMNS FROM {$table}";
                $primary_key_index = 'Key';
                $primary_key_value = 'PRI';
                $table_name_index = 'Field';
            break;
        }
        
        # Get all columns from a selected table
        $r = $this->pdo->query($sql)->fetchAll();
                
        # Search all columns for the Primary Key flag
        foreach ($r as $col) {
            if (($col[$primary_key_index] == $primary_key_value)) {
                # Add this column to the primary keys list
                $pk[] = $col[$table_name_index];
            }
        }
        
        # if the return value is preferred as string
        if (!$as_array) {
            $pk = join(',', $pk);
        }
        
        return $pk;
    }

    /**
     * Get a list of the table fields.
     *
     * @param string $table Table name
     * @return array List of the table fields
     * @access public
     */
    public function get_cols($table)
    {
        if (!$this->connect()) {
            throw new PDOException;
        }
        
        $cols = array();
        
        switch ($this->config['use']['engine']) {
            case self::SQLITE:
                $sql = "PRAGMA table_info({$table})";
                $table_name_index = 'name';
                break;
            case self::MYSQL:
            default:
                $sql = "SHOW COLUMNS FROM {$table}";
                $table_name_index = 'Field';
                break;
        }
        
        # Get all columns from a selected table
        $r = $this->pdo->query($sql)->fetchAll();
        
        # Add column names to $cols array
        foreach ($r as $col) {
            $cols[] = $col[$table_name_index];
        }
        
        return $cols;
    }

    /**
     * Find out if the database contains a table.
     *
     * @param string $table Table name
     * @return boolean True if the table exists, false otherwise
     * @access public
     */
    public function table_exists($table)
    {
        $exists = false;
        
        try {
            $this->pdo->prepare("SELECT 1 FROM $table");
            $exists = true;
        } catch (PDOException $e) {
            $exists = false;
        }
        
        return $exists;
    }

    /**
     * Builds lists for PDO prepared statements.
     *
     * This function returns a string for the WHERE and HAVING and SET clauses,
     * and an array of :field_tag => field_value pairs for the binding of
     * parameters to tags in PDO prepared statements.
     * 
     * The IN and BETWEEN operators are not yet supported.
     *
     * @param array $conditions An array with the conditions
     * @param string $prefix A string to prepend to the tags after ':' and the
     *                       name of the field
     * @param string $clause Which clause to prepare the string for, either
     *                       'WHERE' (by default), 'HAVING' or 'SET'
     * @param string $separator A string to insert between the pairs, usually
     *                          and by default it's ' AND ', but should be ', '
     *                          if $clause is 'SET'
     * @return array An array with tag/value pairs in the index 0 and a string
     *               for use with the selected clause in the index 1
     * @access protected
     */
    protected function build_tags($conditions, $prefix = '', $clause = 'WHERE', $separator = ' AND ')
    {
        # When no conditions are given, provide a neutral set of data
        if (count($conditions) == 0) {
            return array(array(), '');
        }
        
        $where = '';
        $atoms = array();
        $tags = array();
        
        if (count($conditions) > 0) {
            foreach ($conditions as $k => $v) {
                if (is_numeric($k) && is_string($v)) {
                    # If the key is numeric, the value is a string with the
                    # condition; There is nothing else to do
                    $atoms[] = $v;
                } else {
                    # If the key is a table field, use PDO parameters
                    ++self::$tag_count;
                    # Build a tag as :PREFIX_fieldname_TAGCOUNT
                    $tag = str_replace('.', '_', $k);
                    $tag = ':' . $prefix . $tag . '_' . self::$tag_count;
                    
                    if (is_array($v)) {
                        # The comparison operator is provided
                        if (strtoupper($v[0]) == 'IN') {
                            # Tags are not supported for IN lists
                            $atoms[] = "$k IN ({$v[1]})";
                        } elseif (strtoupper($v[0]) == 'BETWEEN') {
                            # For BETWEEN, two tags must be used:
                            # :PREFIX_fieldname_TAGCOUNT_a and
                            # :PREFIX_fieldname_TAGCOUNT_b
                            $atoms[] = "$k BETWEEN {$tag}_a AND {$tag}_b";
                            $tags[$tag.'_a'] = $v[1];
                            $tags[$tag.'_b'] = $v[2];
                        } else {
                            $atoms[] = "$k {$v[0]} $tag";
                            $tags[$tag] = $v[1];  
                        }
                    } else {
                        # The comparison operator defaults to '='
                        $atoms[] = "$k = $tag";
                        $tags[$tag] = $v;
                    }
                }
            }
            
            $where_string = " $clause " . join($separator, $atoms);
        }
        
        return array($tags, $where_string, 'tags' => $tags, 'clause' => $where_string);
    }

    /**
     * Runs a prepared statement.
     * 
     * @param string $query The SQL query to run
     * @return PDOStatement The resulting PDO Statement object
     * @throws PDOException In case of preparation or execution error
     **/
    protected function run_query($query)
    {
        if (!$this->connect()) {
            throw new PDOException;
        }
        
        # Try to prepare the statement
        if (!$stm = $this->pdo->prepare($query)) {
            throw new PDOException("Query could not be prepared: $query");
        }
        
        # Execute the prepared statement
        if (!$stm->execute($this->tags) || $stm->errorCode() !== '00000') {
            throw new PDOException("Query could not be executed: $query");
        }
        
        # Everything's OK, return the complete statement
        return $stm;
    }

    /**
     * Selects the first column from the first row in a query.
     *
     * @param string $table_name The table name
     * @param array $conditions Conditions
     * @return int Number of rows deleted
     * @throws Exception Exception thrown if no table is set
     */
    public function cell($table = null, $fields = null)
    {
        if (isset($table)) {
            $this->from = $table;
        } else {
            if (!isset($this->from)) {
                throw new InvalidArgumentException("No table provided for method PewDatabase::cell()");
            }
        }
        
        if (isset($fields)) {
            $this->fields = $fields;
        }
        
        if (!isset($this->limit)) {
            $this->limit(1);
        }
        
        $query = $this->get_query('SELECT');
        
        $stm = $this->run_query($query);
        
        $this->reset();
        return $stm->fetchColumn();
    }

    /**
     * Selects a single row in a table.
     *
     * @param string $table_name The table name
     * @param array $conditions Conditions
     * @return int Number of rows deleted
     * @throws Exception Exception thrown if no table is set
     */
    public function single($table = null, $fields = null)
    {
        if (isset($table)) {
            $this->from = $table;
        } else {
            if (!isset($this->from)) {
                throw new InvalidArgumentException("No table provided for method PewDatabase::single()");
            }
        }
        
        if (isset($fields)) {
            $this->fields = $fields;
        }
        
        if (!isset($this->limit)) {
            $this->limit(1);
        }
        
        $query = $this->get_query('SELECT');
        
        $stm = $this->run_query($query);
        
        $this->reset();
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Selects rows from a table.
     *
     * @param string $table_name The table name
     * @param array $conditions Conditions
     * @return array Indexed array with the resulting rows
     * @throws InvalidArgumentException If no table is set
     */
    public function select($table = null, $fields = null)
    {
        if (isset($table)) {
            $this->from = $table;
        } else {
            if (!isset($this->from)) {
                throw new InvalidArgumentException("No table provided for method PewDatabase::select()");
            }
        }
        
        if (isset($fields)) {
            $this->fields = $fields;
        }
        
        $query = $this->get_query('select', $this->from);
        
        $stm = $this->run_query($query);
        
        $this->reset();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserts a row in a table.
     *
     * @param string $table_name The table name
     * @param array $data Values to modify
     * @param array $conditions Conditions
     * @return int Primary key value of the last inserted element
     * @throws InvalidArgumentException If no table is set
     */
    public function insert($table = null)
    {
        if (isset($table)) {
            $this->from = $table;
        } else {
            if (!isset($this->from)) {
                throw new InvalidArgumentException("No table provided for method PewDatabase::insert()");
            }
        }
        
        $query = $this->get_query('INSERT', $this->from);
        
        $stm = $this->run_query($query);
        
        $this->reset();
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Updates rows in a table.
     *
     * @param string $table_name The table name
     * @param array $data Values to modify
     * @param array $conditions Conditions
     * @return int Number of rows affected
     * @throws InvalidArgumentException If no table is set
     */
    public function update($table = null)
    {
        if (isset($table)) {
            $this->from = $table;
        } else {
            if (!isset($this->from)) {
                throw new InvalidArgumentException("No table provided for method PewDatabase::update()");
            }
        }
        
        $query = $this->get_query('UPDATE', $this->from);
        $stm = $this->run_query($query);
        $this->reset();
        
        return $stm->rowCount();
    }
    
    /**
     * Deletes rows in a table.
     *
     * @param string $table_name The table name
     * @param array $conditions
     * @return int Number of rows deleted
     * @throws InvalidArgumentException If no table is set
     */
    public function delete($table = null)
    {
        if (isset($table)) {
            $this->from = $table;
        } else {
            if (!isset($this->from)) {
                throw new InvalidArgumentException("No table provided for method PewDatabase::delete()");
            }
        }
        
        $query = $this->get_query('DELETE', $this->from);
        $stm = $this->run_query($query);
        $this->reset();
        
        return $stm->rowCount();
    }
    
    /**
     * Builds a Select, Update, Insert or Delete query.
     *
     * This method updates the last_query property.
     *
     * @param string $type One of SELECT, UPDATE, INSERT or DELETE
     * @param string $table A table name list that overrides that of
     *                      PewDatabase::from() and PewDatabase::into()
     * @return string The sql statement
     * @access public
     */
    public function get_query($type, $table = null)
    {
        $sql = '';
        
        if (!isset($table)) {
            $table = $this->from;
        }
        
        switch (strtoupper($type)) {
            case 'SELECT':
                $sql = "SELECT $this->fields FROM $table $this->where $this->group_by $this->having $this->order_by $this->limit";
                $this->tags = array_merge($this->where_tags, $this->having_tags);
                break;
            case 'UPDATE':
                $sql = "UPDATE $table $this->set $this->where";
                $this->tags = array_merge($this->set_tags, $this->where_tags);
                break;
            case 'INSERT':
                $sql = "INSERT INTO $table ($this->fields) $this->values";
                $this->tags = $this->insert_tags;
                break;
            case 'DELETE':
                $sql = "DELETE FROM $table $this->where";
                $this->tags = $this->where_tags;
                break;
        }
        
        $sql = trim(preg_replace('/\s+/', ' ', $sql));
        
        $this->last_query = $sql;
        
        return $sql;
    }
    
    /**
     * Resets the data in the SQL clauses.
     *
     * @return PewDatabase Returns the PewDatabase object
     * @access public
     */
    public function reset()
    {
        $this->from =       $this->where =       $this->order_by =
        $this->group_by =   $this->having =      $this->limit =
        $this->where =      $this->set =         null;
        
        $this->tags =       $this->where_tags =  $this->having_tags =
        $this->set_tags =   $this->insert_tags = array();
        
        $this->fields = '*';
        
        self::$tag_count = 0;
        
        return $this;
    }
}

/**
 * $pdb = PewDatabase::instance();
 *
 * $pdb = new PewDatabase();
 *
 * $eighties_movies = $pdb->where(array('year' => array('between', 1980, 1990)))->select('movies', 'title, year, director');
 *
 * $how_many_kubrick_movies = $pdb->fields('count(*)')->where(array('director_name' => 'Stanley Kubrick'))->cell('movies');
 *
 * $new_id = $pdb->values(array('title' => 'The Dark Knight Rises', 'director' => 'Christopher Nolan'))->insert('movies');
 *
 * $modified_studios = $pdb->set(array('country' => 51))->where(array('country' => null))->update('studios');
 *
 * $all_movies = $pdb->select('movies');
 *
 * $all_black_and_white_movies = $pdb->pdo->query("SELECT id, name, year FROM movies WHERE color = FALSE");
 * 
 */
