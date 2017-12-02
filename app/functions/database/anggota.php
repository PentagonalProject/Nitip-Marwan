<?php

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
        'is_admin'
    ];
    $values = [];
    $set = '';
    foreach ($data as $key => $value) {
        if (!in_array($key, $fields)) {
            continue;
        }
        if ($key === 'is_admin') {
            $value = (bool) $value;
        }
        $values[] = $value;
        $set = " SET {$key}=?, ";
    }

    if (empty($values)) {
        return false;
    }

    $set = trim($set, ', ');
    $values[] = $username;
    return database_execute("UPDATE anggota {$set} WHERE username=?", $values);
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

/**
 * @param array $data
 *
 * @return ADORecordSet_pdo
 */
function database_create_anggota(array $data)
{
    $fields = [
        'user_name',
        'password',
        'nama_awal',
        'nama_akhir',
        'email',
        'is_admin'
    ];

    $values = [];
    foreach ($data as $key => $value) {
        if (!in_array($key, $fields)) {
            continue;
        }
        if ($key === 'is_admin') {
            $value = (bool) $value;
        }
        if ($key === 'password') {
            $value = sha1($value);
        }

        $values[$key] = $value;
    }

    $columns     = implode(', ', array_keys($values));
    $valueValue  = rtrim(str_repeat('?, ', count($values)), ', ');
    return database_execute(
        "INSERT INTO anggota ({$columns}) VALUES ({$valueValue})", array_values($values)
    );
}

/**
 * @param string $username
 *
 * @return ADORecordSet_pdo|bool|int
 */
function delete_anggota($username)
{
    if (!is_string($username)) {
        return false;
    }

    // apabila login dan mencoba menghapus akun sendiri
    // jangan ijinkan
    $currentUsername = get_current_user_name();
    if ($currentUsername && strtolower($currentUsername) == strtolower($username)) {
        return 0;
    }

    return database_execute("DELETE FROM anggota WHERE user_name=?", [$username]);
}

/**
 * @param int $offset
 * @param int $limit
 *
 * @return ADORecordSet_pdo
 */
function create_query_loop_anggota($offset = 0, $limit = 100)
{
    $offset = abs($offset);
    $limit  = abs($limit);
    return database_execute("SELECT * FROM anggota WHERE true ORDER BY id ASC LIMIT {$limit} OFFSET {$offset}");
}
