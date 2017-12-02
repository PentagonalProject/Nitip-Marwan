<?php
/**
 * @return ADODB_pdo
 */
function &database()
{
    static $db;
    if (isset($db)) {
        return $db;
    }

    /**
     * @var ADODB_pdo $db
     */
    $db = ADONewConnection('pdo');
    if (defined('DB_DEBUG') && DB_DEBUG) {
        $db->debug = true;
    }
    $dsn = 'mysql:host='. DB_HOST;
    $connect = $db->Connect($dsn, DB_USER, DB_PASS, DB_NAME);
    if (!$connect) {
        throw new \RuntimeException(
            $db->_errorMsg,
            $db->_errorCode
        );
    }
    $db->SetFetchMode(\PDO::FETCH_ASSOC);
    return $db;
}

/**
 * @param string $sql
 * @param bool $inputArr
 *
 * @return ADORecordSet_pdo
 */
function database_execute($sql, $inputArr = false)
{
    /**
     * @var ADORecordSet_pdo $ADORecordSet_pdo
     */
    $ADORecordSet_pdo = database()->Execute($sql, $inputArr);
    return $ADORecordSet_pdo;
}
