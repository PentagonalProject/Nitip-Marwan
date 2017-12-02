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

// ------------------------------------------------------------
// USER / ANGGOTA
// ------------------------------------------------------------

/**
 * @param string $username
 * @param array $data
 *
 * @return ADORecordSet_pdo|bool
 */
function database_update_anggota($username, array $data)
{
    if (!is_string($username)) {
        return false;
    }

    $fields = [
        'user_name',
        'password',
        'nama_awal',
        'nama_akhir',
        'email',
    ];
    $value = [];
    $set = '';
    foreach ($data as $key => $value) {
        if (!in_array($key, $fields)) {
            continue;
        }
        $value[] = $value;
        $set = " SET {$key}=?, ";
    }

    if (empty($value)) {
        return false;
    }
    $set = trim($set, ', ');
    $value[] = $username;
    return database_execute("UPDATE anggota {$set} WHERE username=?", $value);
}

/**
 * @param string $username
 *
 * @return bool|array
 */
function database_get_anggota($username)
{
    if (!is_string($username)) {
        return false;
    }
    $record = database_execute("SELECT * FROM anggota WHERE user_name=?", [$username]);
    if ($record->fields) {
        $password = $record->fields['password'];
        if (empty($password)) {
            $password = microtime();
        }
        if (!is_sha1($password)) {
            $password = sha1($password);
            $record->fields['password'] = $password;
            database_update_anggota($username, ["password" => $password]);
        }
        return $record->fields;
    }

    return false;
}
