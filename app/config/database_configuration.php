<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package app
 */

define('MYSQL',     'mysql');     # MySQL
define('SQLITE',    'sqlite');    # SQLite
define('PGSQL',     'pgsql');     # PostgreSQL
define('SQLSRV',    'sqlsrv');    # Microsoft SQL Server

/**
 * Global database configuration class extended by the PewDatabase class.
 *
 * Available fields are:
 *     engine: any of the database engine constants defined in this file.
 *     file: (SQLite) database file, path relative to ROOT
 *     host: (MySQL) IP or DNS name of the database server
 *     user: (MySQL) username with database privileges
 *     pass: (MySQL) password for the user
 *     data: (MySQL) database schema name
 *
 * Version 0.2 includes settings to connect to SQLite databases.
 * Version 0.3 includes constants for Postgres and MSSQL, but they're untested.
 * Version 0.4 changes from interface to class to enable multiple configs and
 *             facilitate some other operations.
 * 
 * @version 0.4 11-aug-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package app
 */
class DatabaseConfiguration
{
    /**
     * @var array Associative array containing database configurations.
     */
    public $config = array
    (
        'default' => array
        (
            'engine' => SQLITE,
            'file' => 'app/config/db.sqlite3'
        ),
        
        'mysql' => array
        (
            'engine' => MYSQL,
            'host' => 'localhost',
            'user' => 'username',
            'pass' => 'password',
            'name' => 'database name'
        ),
    );
}