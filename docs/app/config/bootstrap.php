<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * Use the custom_hash function to encrypt your passwords however you like
 *
 * Don't use transitory data to hash a password, i.e. the time of the day or
 * a random value.
 *
 * @param array $data Array with username and password fields used for login
 * @return string The hashed data
 * @version 0.1 16-apr-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 */
//function custom_hash($data)
//{
//    return md5($data['username'] . md5($data['password']));
//}

/**
 * Use the sqlite_init function to bootstrap a SQLite file database.
 *
 * This function is called when the first model is loaded, if the database file
 * is empty (filesize is zero) and this function is available (of course).
 *
 * My favorite way of doing this is loading a CSV file for each table and
 * inserting each row via prepared statement. The implementation below is
 * provided as an example.
 *
 * @param PDO $db PDO database handle
 * @return bool False on error
 * @version 0.1 03-aug-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 */
//function sqlite_init(PDO $db)
//{
//    $tables = array(
//        "CREATE TABLE table1 (field1, field2, field3)",
//        "CREATE TABLE table2 (field1, field2, field3)",
//    );
//    
//    ob_start();
//    
//    echo "CREATING DATABASE..." . PHP_EOL;
//    
//    foreach ($tables as $table ) {
//        $db->exec($table);
//        
//        if ($db->errorCode() !== '00000') {
//            pr($db->errorInfo());
//            trigger_error("Error in \n\t$table\n");
//        }
//    }
//
//    echo "DATABASE CREATED..." . PHP_EOL;
//    
//    $bootstrap_log = ob_get_contents();
//    ob_end_clean();
//    
//    file_put_contents('bootstrap.log.txt', $bootstrap_log);
//
//    return true;
//}
