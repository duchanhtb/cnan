<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class CMysqlDB {

    var $conn_id;
    var $conn_id_read;
    var $query_id;
    var $record;
    var $object;
    var $db;
    var $port;
    var $listQuery = array();

    /**
     * @Desc constructor function
     * 
     */
    function CMysqlDB($db_info) {
        $this->db = $db_info;
        if (empty($db_info['dbPort']))
            $this->port = 3306;
        else
            $this->port = $db_info['dbPort'];
    }

    /**
     * @Desc connection to write
     * @param connection id
     */
    function connectWrite() {
        //connect to WRITE database	
        $this->conn_id = @mysql_connect($this->db['dbHost'] . ":" . $this->port, $this->db['dbUser'], $this->db['dbPass']);
        if (!$this->conn_id)
            $this->sqlError("Connection Error");

        if (!@mysql_select_db($this->db['dbName'], $this->conn_id))
            $this->sqlError("Database Error");
        @mysql_query("SET NAMES utf8", $this->conn_id);
        return $this->conn_id;
    }

    /**
     * @Desc constructor function
     * @return connection id
     */
    function connectRead() {
        //connect to READ database	
        $this->conn_id_read = @mysql_connect($this->db['dbHostRead'] . ":" . $this->port, $this->db['dbUser'], $this->db['dbPass']);
        if (!$this->conn_id_read)
            $this->sqlError("Connection Error");

        if (!@mysql_select_db($this->db['dbName'], $this->conn_id_read))
            $this->sqlError("Database Error");
        @mysql_query("SET NAMES utf8", $this->conn_id_read);
        return $this->conn_id_read;
    }

    /**
     * @Desc close function
     * @return boolean
     */
    function close() {
        if ($this->conn_id) {
            mysql_close($this->conn_id);
        }

        if ($this->conn_id_read) {
            mysql_close($this->conn_id_read);
        }
        return true;
    }

    /**
     * @Desc query function
     * @param $query_string: sql query
     * @param $priority: priority
     * @param $cache: 
     * @return query id
     */
    function query($query_string, $priority = 0, $cache = 0) {
        if (SHOW_QUERY_INFO == "on") {
            $start_time = microtime(true);
        }

        if (strpos(strtolower($query_string), 'select') === 0 && $priority == 0) {
            if (!$this->conn_id_read)
                $this->connectRead();
            $this->query_id = @mysql_query($query_string, $this->conn_id_read);
        }
        else {
            if (!$this->conn_id)
                $this->connectWrite();
            $this->query_id = @mysql_query($query_string, $this->conn_id);
        }

        if (!$this->query_id) {
            $this->sqlError("Query Error", $query_string);
        }

        if (SHOW_QUERY_INFO == "on") {
            $end_time = microtime(true);
            $totaltime = ($end_time - $start_time);
            if ($cache == 0)
                $this->listQuery[] = $query_string . " (" . round($totaltime, 4) . "s) " . ($priority == 0 ? "" : "[high priority]");
        }

        return $this->query_id;
    }

    /**
     * @Desc update query
     * @return boolean
     */
    function updateQuery($p_sAction = 'Update', $p_sTblName, $p_aValuesPairs, $p_sWhereClause = "") {
        $sFields = "";
        $sValues = "";
        $sPairs = "";
        if (!empty($p_sWhereClause))
            $sWhereClause = "where " . $p_sWhereClause;
        else
            $sWhereClause = "";

        foreach ($p_aValuesPairs as $sField => $sValue) {
            if ($p_sAction == "Insert") {
                if (!empty($sFields))
                    $sFields .= ", ";

                $sFields .= '`' . $sField . '`';

                if (!empty($sValues))
                    $sValues .= ", ";
                $sValues .= "'" . $sValue . "'";
            }

            elseif ($p_sAction == "Update") {
                if (!empty($sPairs))
                    $sPairs .= ", ";
                $sPairs .= "`{$sField}` = '" . $sValue . "'";
            }
        }

        if ($p_sAction == "Insert") {
            $sFields = "( " . $sFields . " )";
            $sValues = "( " . $sValues . " )";
            $sQuery = "insert into {$p_sTblName}{$sFields} values{$sValues} {$sWhereClause};";
        } elseif ($p_sAction == "Update") {
            $sQuery = "update {$p_sTblName} set {$sPairs} {$sWhereClause};";
        }

        return $this->query($sQuery);
    }

    /**
     * 	Returns an array that corresponds to the fetched row
     */
    function fetchArray($query_id = -1) {
        if ($query_id != -1)
            $this->query_id = $query_id;
        $this->record = @mysql_fetch_assoc($this->query_id);

        return $this->record;
    }

    /**
     * 	Returns an object with properties that correspond to the fetched row
     */
    function fetchObject($query_id = -1) {
        if ($query_id != -1)
            $this->query_id = $query_id;
        $this->object = @mysql_fetch_object($this->query_id);

        return $this->object;
    }

    /**
     * 	Returns an object containing field information
     */
    function fetchField() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        if ($aPar[0] != -1)
            $this->query_id = $aPar[0];

        switch ($iParNum) {
            case 1:
                return @mysql_fetch_field($this->query_id);
                break;
            case 2:
                return @mysql_fetch_field($this->query_id, $aPar[1]);
                break;
        }
    }

    /**
     * 	Returns first array that corresponds to the first fetched row
     */
    function queryFirst($query_string) {
        $this->query($query_string);
        $returnarray = $this->fetchArray($this->query_id);
        $this->freeResult($this->query_id);
        return $returnarray;
    }

    /**
     * 	Returns the number of rows in a result set on success, or FALSE on failure
     */
    function numRows($query_id = -1) {
        if ($query_id != -1)
            $this->query_id = $query_id;

        return @mysql_num_rows($this->query_id);
    }

    /**
     * 	Returns the number of affected rows by the last INSERT, UPDATE or DELETE query on success, and -1 if the last query failed.
     */
    function affectedRows($conn_id = -1) {
        if ($conn_id != -1)
            $this->conn_id = $conn_id;

        return @mysql_affected_rows($this->conn_id);
    }

    /**
     * 	Returns the number of fields in the result set resource on success, or FALSE on failure
     */
    function numFields($query_id = -1) {
        if ($query_id != -1)
            $this->query_id = $query_id;

        return @mysql_num_fields($this->query_id);
    }

    /**
     * 	Free all memory associated with the result identifier
     */
    function freeResult($query_id) {
        return @mysql_free_result($query_id);
    }

    /**
     * 	Returns the ID generated for an AUTO_INCREMENT column by the previous INSERT query
     */
    function getInsertId($conn_id = -1) {
        if ($conn_id == -1)
            $conn_id = $this->conn_id;

        return @mysql_insert_id($conn_id);
    }

    /**
     * 	Returns the field flags array of the specified field
     */
    function getFieldFlags($query_id = -1, $fieldOffset = 0) {
        if ($query_id != -1)
            $this->query_id = $query_id;

        $sFlags = mysql_field_flags($this->query_id, $fieldOffset);

        return explode(" ", $sFlags);
    }

    /**
     * 	Returns the length of the specified field
     */
    function getFieldLen($query_id = -1, $fieldOffset = 0) {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        if ($query_id != -1)
            $this->query_id = $query_id;

        return mysql_field_len($this->query_id, $fieldOffset);
    }

    /**
     * Return the name of the specified field 
     */
    function getFieldName($query_id = -1, $fieldOffset = 0) {
        if ($query_id != -1)
            $this->query_id = $query_id;

        return mysql_field_name($this->query_id, $fieldOffset);
    }

    /**
     * Return the type of the specified field 
     */
    function getFieldType($query_id = -1, $fieldOffset = 0) {
        if ($query_id != -1)
            $this->query_id = $query_id;

        return mysql_field_type($this->query_id, $fieldOffset);
    }

    /**
     * Return fields information about the given table name 
     */
    function getFieldsList() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        switch ($iParNum) {
            case 1:
                $rs = mysql_list_fields($this->db['dbName'], $aPar[0], $this->conn_id);
                break;
            case 2:
                $rs = mysql_list_fields($aPar[0], $aPar[1], $this->conn_id);
                break;
            case 3;
                $rs = mysql_list_fields($aPar[0], $aPar[1], $aPar[2]);
                break;
        }
        $aReturn = array();

        while ($rc = mysql_fetch_array($rs)) {
            $aReturn[] = $rc;
        }

        return $aReturn;
    }

    /**
     * Return tables information about the given database name. If the database name is not specified, the current database is used
     */
    function getTablesList() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        switch ($iParNum) {
            case 0:
                $rs = mysql_list_tables($this->db['dbName'], $this->conn_id);
                break;
            case 1:
                $rs = mysql_list_tables($aPar[0], $this->conn_id);
                break;
            case 2:
                $rs = mysql_list_tables($aPar[0], $aPar[1]);
                break;
        }
        $aReturn = array();

        while ($rc = mysql_fetch_array($rs)) {
            $aReturn[] = $rc;
        }

        return $aReturn;
    }

    /**
     * Return  information about the given database name. If the database name is not specified, the current database is used
     */
    function count($tableName, $fieldName, $condition = false) {
        if ($condition) {
            $condition = ' where 1 ' . $condition;
        }
        $rs = $this->query('select count(' . $fieldName . ') as total from ' . $tableName . $condition);
        $data = $this->fetchArray($rs);
        return $data['total'];
    }

    /**
     * Return  information about the given database name. If the database name is not specified, the current database is used
     */
    function exists($query) {
        $this->query($query);
        if ($this->numRows())
            return $this->fetchArray();
        return false;
    }

    function fetchAll($query_id = -1) {
        if ($query_id != -1)
            $this->query_id = $query_id;

        $i = 0;
        $array = array();
        while ($this->record = @mysql_fetch_assoc($this->query_id)) {
            $array[$i] = $this->record;
            $i++;
        }
        return $array;
    }

    /**
     * Show the error
     */
    function sqlError($message, $query = "") {
        $sqlerror = mysql_errno() . ": " . mysql_error();
        print "<!-- " . $sqlerror . " -->";
        exit;
    }

    /**
     * select function
     */
    function select($tbl, $cond = '') {
        if (is_numeric($cond))
            return $this->exists($tbl, $cond);
        elseif ($cond == '')
            return $this->exists('select * from `' . $tbl . '` limit 0,1 order by id desc');
        else
            return $this->exists('select * from `' . $tbl . '` where ' . $cond . ' limit 0,1');
    }

    /**
     * Show the error
     */
    function PMA_DBI_try_query($query, $link = null, $options = 0) {
        if (empty($link)) {
            if (isset($GLOBALS['userlink'])) {
                $link = $GLOBALS['userlink'];
            } else {
                return FALSE;
            }
        }
        if (defined('PMA_MYSQL_INT_VERSION') && PMA_MYSQL_INT_VERSION < 40100) {
            $query = PMA_convert_charset($query);
        }
        if ($options == ($options | PMA_DBI_QUERY_STORE)) {
            return @mysql_query($query, $link);
        } elseif ($options == ($options | PMA_DBI_QUERY_UNBUFFERED)) {
            return @mysql_unbuffered_query($query, $link);
        } else {
            return @mysql_query($query, $link);
        }
    }
}