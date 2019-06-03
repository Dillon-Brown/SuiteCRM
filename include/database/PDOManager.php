<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2019 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

use PDO;

class PDOManager extends DBManager
{

    /**
     * Quote string in DB-specific manner
     * @param string $string
     * @return string
     */
    public function quote($string)
    {
        // TODO: Implement quote() method.
    }

    public function quoteIdentifier($string)
    {
        // TODO: Implement quoteIdentifier() method.
    }

    /**
     * Use when you need to convert a database string to a different value; this function does it in a
     * database-backend aware way
     * Supported conversions:
     *      today        return current date
     *      left        Take substring from the left
     *      date_format    Format date as string, supports %Y-%m-%d, %Y-%m, %Y
     *      time_format Format time as string
     *      date        Convert date string to datetime value
     *      time        Convert time string to datetime value
     *      datetime    Convert datetime string to datetime value
     *      ifnull        If var is null, use default value
     *      concat        Concatenate strings
     *      quarter        Quarter number of the date
     *      length        Length of string
     *      month        Month number of the date
     *      add_date    Add specified interval to a date
     *      add_time    Add time interval to a date
     *      text2char   Convert text field to varchar
     *
     * @param string $string database string to convert
     * @param string $type type of conversion to do
     * @param array $additional_parameters optional, additional parameters to pass to the db function
     * @return string
     */
    public function convert($string, $type, array $additional_parameters = array())
    {
        // TODO: Implement convert() method.
    }

    /**
     * Converts from Database data to app data
     *
     * Supported types
     * - date
     * - time
     * - datetime
     * - datetimecombo
     * - timestamp
     *
     * @param string $string database string to convert
     * @param string $type type of conversion to do
     * @return string
     */
    public function fromConvert($string, $type)
    {
        // TODO: Implement fromConvert() method.
    }

    /**
     * Parses and runs queries
     *
     * @param string $sql SQL Statement to execute
     * @param bool $dieOnError True if we want to call die if the query returns errors
     * @param string $msg Message to log if error occurs
     * @param bool $suppress Flag to suppress all error output unless in debug logging mode.
     * @param bool $keepResult Keep query result in the object?
     * @return resource|bool result set or success/failure bool
     */
    public function query($sql, $dieOnError = false, $msg = '', $suppress = false, $keepResult = false)
    {
        // TODO: Implement query() method.
    }

    /**
     * Runs a limit query: one where we specify where to start getting records and how many to get
     *
     * @param string $sql SELECT query
     * @param int $start Starting row
     * @param int $count How many rows
     * @param boolean $dieOnError True if we want to call die if the query returns errors
     * @param string $msg Message to log if error occurs
     * @param bool $execute Execute or return SQL?
     * @return resource query result
     */
    public function limitQuery($sql, $start, $count, $dieOnError = false, $msg = '', $execute = true)
    {
        // TODO: Implement limitQuery() method.
    }

    /**
     * Free Database result
     * @param resource $dbResult
     */
    protected function freeDbResult($dbResult)
    {
        // TODO: Implement freeDbResult() method.
    }

    /**
     * Rename column in the DB
     * @param string $tablename
     * @param string $column
     * @param string $newname
     */
    public function renameColumnSQL($tablename, $column, $newname)
    {
        // TODO: Implement renameColumnSQL() method.
    }

    /**
     * Returns definitions of all indies for passed table.
     *
     * return will is a multi-dimensional array that
     * categorizes the index definition by types, unique, primary and index.
     * <code>
     * <?php
     * array(                                                              O
     *       'index1'=> array (
     *           'name'   => 'index1',
     *           'type'   => 'primary',
     *           'fields' => array('field1','field2')
     *           )
     *       )
     * ?>
     * </code>
     * This format is similar to how indicies are defined in vardef file.
     *
     * @param string $tablename
     * @return array
     */
    public function get_indices($tablename)
    {
        // TODO: Implement get_indices() method.
    }

    /**
     * Returns definitions of all indies for passed table.
     *
     * return will is a multi-dimensional array that
     * categorizes the index definition by types, unique, primary and index.
     * <code>
     * <?php
     * array(
     *       'field1'=> array (
     *           'name'   => 'field1',
     *           'type'   => 'varchar',
     *           'len' => '200'
     *           )
     *       )
     * ?>
     * </code>
     * This format is similar to how indicies are defined in vardef file.
     *
     * @param string $tablename
     * @return array
     */
    public function get_columns($tablename)
    {
        // TODO: Implement get_columns() method.
    }

    /**
     * Generates alter constraint statement given a table name and vardef definition.
     *
     * Supports both adding and droping a constraint.
     *
     * @param string $table tablename
     * @param array $definition field definition
     * @param bool $drop true if we are dropping the constraint, false if we are adding it
     * @return string SQL statement
     */
    public function add_drop_constraint($table, $definition, $drop = false)
    {
        // TODO: Implement add_drop_constraint() method.
    }

    /**
     * Returns the description of fields based on the result
     *
     * @param resource $result
     * @param boolean $make_lower_case
     * @return array field array
     */
    public function getFieldsArray($result, $make_lower_case = false)
    {
        // TODO: Implement getFieldsArray() method.
    }

    /**
     * Returns an array of tables for this database
     *
     * @return    array|false    an array of with table names, false if no tables found
     */
    public function getTablesArray()
    {
        // TODO: Implement getTablesArray() method.
    }

    /**
     * Return's the version of the database
     *
     * @return string
     */
    public function version()
    {
        // TODO: Implement version() method.
    }

    /**
     * Checks if a table with the name $tableName exists
     * and returns true if it does or false otherwise
     *
     * @param string $tableName
     * @return bool
     */
    public function tableExists($tableName)
    {
        // TODO: Implement tableExists() method.
    }

    /**
     * Fetches the next row in the query result into an associative array
     *
     * @param resource $result
     * @return array    returns false if there are no more rows available to fetch
     */
    public function fetchRow($result)
    {
        // TODO: Implement fetchRow() method.
    }

    /**
     * Connects to the database backend
     *
     * Takes in the database settings and opens a database connection based on those
     * will open either a persistent or non-persistent connection.
     * If a persistent connection is desired but not available it will defualt to non-persistent
     *
     * configOptions must include
     * db_host_name - server ip
     * db_user_name - database user name
     * db_password - database password
     *
     * @param array $configOptions
     * @param boolean $dieOnError
     */
    public function connect(array $configOptions = null, $dieOnError = false)
    {
        // TODO: Implement connect() method.
    }

    /**
     * Generates sql for create table statement for a bean.
     *
     * @param string $tablename
     * @param array $fieldDefs
     * @param array $indices
     * @return string SQL Create Table statement
     */
    public function createTableSQLParams($tablename, $fieldDefs, $indices)
    {
        // TODO: Implement createTableSQLParams() method.
    }

    /**
     * Generates the SQL for changing columns
     *
     * @param string $tablename
     * @param array $fieldDefs
     * @param string $action
     * @param bool $ignoreRequired Optional, true if we should ignor this being a required field
     * @return string|array
     */
    protected function changeColumnSQL($tablename, $fieldDefs, $action, $ignoreRequired = false)
    {
        // TODO: Implement changeColumnSQL() method.
    }

    /**
     * Disconnects from the database
     *
     * Also handles any cleanup needed
     */
    public function disconnect()
    {
        // TODO: Implement disconnect() method.
    }

    /**
     * Get last database error
     * This function should return last error as reported by DB driver
     * and should return false if no error condition happened
     * @return string|false Error message or false if no error happened
     */
    public function lastDbError()
    {
        // TODO: Implement lastDbError() method.
    }

    /**
     * Check if this query is valid
     * Validates only SELECT queries
     * @param string $query
     * @return bool
     */
    public function validateQuery($query)
    {
        // TODO: Implement validateQuery() method.
    }

    /**
     * Check if this driver can be used
     * @return bool
     */
    public function valid()
    {
        // TODO: Implement valid() method.
    }

    /**
     * Check if certain database exists
     * @param string $dbname
     */
    public function dbExists($dbname)
    {
        // TODO: Implement dbExists() method.
    }

    /**
     * Get tables like expression
     * @param string $like Expression describing tables
     * @return array
     */
    public function tablesLike($like)
    {
        // TODO: Implement tablesLike() method.
    }

    /**
     * Create a database
     * @param string $dbname
     */
    public function createDatabase($dbname)
    {
        // TODO: Implement createDatabase() method.
    }

    /**
     * Drop a database
     * @param string $dbname
     */
    public function dropDatabase($dbname)
    {
        // TODO: Implement dropDatabase() method.
    }

    /**
     * Get database configuration information (DB-dependent)
     * @return array|null
     */
    public function getDbInfo()
    {
        // TODO: Implement getDbInfo() method.
    }

    /**
     * Check if certain DB user exists
     * @param string $username
     */
    public function userExists($username)
    {
        // TODO: Implement userExists() method.
    }

    /**
     * Create DB user
     * @param string $database_name
     * @param string $host_name
     * @param string $user
     * @param string $password
     */
    public function createDbUser($database_name, $host_name, $user, $password)
    {
        // TODO: Implement createDbUser() method.
    }

    /**
     * Check if the database supports fulltext indexing
     * Note that database driver can be capable of supporting FT (see supports('fulltext))
     * but particular instance can still have it disabled
     * @return bool
     */
    public function full_text_indexing_installed()
    {
        // TODO: Implement full_text_indexing_installed() method.
    }

    /**
     * Generate fulltext query from set of terms
     * @param string $field Field to search against
     * @param array $terms Search terms that may be or not be in the result
     * @param array $must_terms Search terms that have to be in the result
     * @param array $exclude_terms Search terms that have to be not in the result
     */
    public function getFulltextQuery($field, $terms, $must_terms = array(), $exclude_terms = array())
    {
        // TODO: Implement getFulltextQuery() method.
    }

    /**
     * Get install configuration for this DB
     * @return array
     */
    public function installConfig()
    {
        // TODO: Implement installConfig() method.
    }

    /**
     * Returns a DB specific FROM clause which can be used to select against functions.
     * Note that depending on the database that this may also be an empty string.
     * @return string
     */
    public function getFromDummyTable()
    {
        // TODO: Implement getFromDummyTable() method.
    }

    /**
     * Returns a DB specific piece of SQL which will generate GUID (UUID)
     * This string can be used in dynamic SQL to do multiple inserts with a single query.
     * I.e. generate a unique Sugar id in a sub select of an insert statement.
     * @return string
     */
    public function getGuidSQL()
    {
        // TODO: Implement getGuidSQL() method.
    }
}
