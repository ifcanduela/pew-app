<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

require APP . 'config/database_configuration.php';

/**
 * Utility class for MySQLi interaction.
 *
 * A child class extending MySQLi, with some methods that perform standard CRUD
 * operations. Connect using persistence if PHP version is greater than 5.3.
 * 
 * @uses DatabaseConfiguration
 * @version 0.8 9-mar-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 * @deprecated Use PewDatabase instead.
 */
class Database extends mysqli implements DatabaseConfiguration
{
    /**
     * The singleton instance.
     *
     * @var Database
     * @access private
     * @static
     */
    private static $instance;
    
    /**
     * Private class constructor.
     *
     * The constructor is private to prevent instantiation from outside the
     * class
     *
     * @return void
     * @access private
     */
    private function __construct()
    {
        if (strnatcmp(phpversion(),'5.3.0') >= 0) {
            $host = 'p:' . self::host;
        } else {
            $host = self::host;
        }
        parent::__construct($host, self::user, self::pass, self::name);
    }
    
    /**
     * Private __clone magic method.
     * 
     * The __clone magic function should not be used
     *
     * @return void
     * @access private
     */
    private function __clone() {die('Cloning Databases is a bad habit.');}
    
    /**
     * Instance a Database singleton.
     * 
     * @return Database A database instance
     * @access public
     * @static
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
     * Retrieves data from the database.
     * 
     * @param string $query The SQL query to be executed
     * @return array An indexed array with the results of the query, or false
     * @access public
     */
    public function select($query)
    {
        $set = $this->query($query) or die("ERROR $this->error in query:<br> <pre>$query</pre>");
        
        if ($this->affected_rows > 0) {
            while ($row = $set->fetch_Assoc()) {
                $result[] = $row;
            }
        } else {
            $result = false;
        }
        
        $set->close();
        return $result;
    }
    
    /**
     * Retrieves a single row from the database.
     *
     * @param string $query The SQL query to be executed
     * @return array An assocciative array with the row fields, or false
     * @access public
     */
    public function single($query)
    {
        $set = $this->query($query) or die("ERROR $this->error in query:<br> <pre>$query</pre>");
        
        if ($this->affected_rows > 0) {
            $result = $set->fetch_assoc() or die("ERROR $this->error");
        } else {
            $result = false;
        }
        
        $set->close();
        return $result;
    }
    
    /**
     * Inserts data into the database.
     * 
     * @param string $table The table to insert data into
     * @param array $data An associative array with field => value information
     * @return integer The primary key value of the inserted data
     * @access public
     */
    public function insert($table, $data)
    {
        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = is_numeric($value) ? $value : '"' . $this->escape_string($value) . '"';
        }
        
        $fields = join(',', $fields);
        $values = join(',', $values);
        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        
        $this->query($sql) or die("ERROR $this->error in query:<br> <pre>$sql</pre>");
        return $this->insert_id;
    }
    
    /**
     * Updates data in the database.
     * 
     * @param string $table The table to update data in
     * @param array $data An associative array with field => value information
     * @param string $where The conditions for the update operation
     * @return boolean true
     * @access public
     */
    public function update($table, $data, $where = '1=1')
    {
        foreach ($data as $field => $value) {
            $assignments[] = $field . '=' . (is_numeric($value) ? $value : '"' . $this->escape_string($value) . '"');
        }

        $values = join(',', $assignments);
        $sql = "UPDATE $table SET $values WHERE $where";

        $this->query($sql) or die("ERROR $this->error in query:<br> <pre>$sql</pre>");
        return true;
    }
    
    /**
     * Deletes data from the database.
     * 
     * @param string $table The table to delete data from
     * @param string $where The conditions for the delete operation
     * @return boolean true
     * @access public
     */
    public function delete($table, $where = '1=1')
    {
        $sql = "DELETE FROM $table WHERE $where";

        $this->query($sql) or die("ERROR $this->error");
        return true;
    }
    
    /**
     * Counts rows in a table.
     * 
     * @param string $table The table to count
     * @param string $where An optional condition the counted rows must satisy
     * @return int Number of rows
     * @access public
     */
    public function count($table, $where = '1=1')
    {
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";

        $result = $this->single($sql) or die("ERROR $this->error");
        return $result['count'];
    }
}
