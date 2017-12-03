<?php

// ------------------------------------------------------------
// USER / ANGGOTA
// ------------------------------------------------------------

/**
 * @param string $username
 *
 * @return bool|string
 */
function validasi_anggota_username($username)
{
    if (!is_string($username)) {
        return false;
    }
    $username = trim($username);
    if ($username === '') {
        return false;
    }

    // replace unwanted characters
    $username = preg_replace('/[^a-z0-9\-\_]/i', '', $username);
    if ($username && !preg_match('/[a-z]/i', $username)) {
        return false;
    }

    return $username !== '' ? strtolower($username) : false;
}

/**
 * @return float|int
 */
function database_get_jumlah_anggota()
{
    $data = database_execute("SELECT COUNT(*) AS hitung FROM anggota");
    if (!empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}

/**
 * @return float|int
 */
function database_get_jumlah_admin()
{
    $data = database_execute("SELECT COUNT(*) AS hitung FROM anggota WHERE is_admin=TRUE");
    if (!empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}
/**
 * @return float|int
 */
function database_get_jumlah_non_admin()
{
    $data = database_execute("SELECT COUNT(*) AS hitung FROM anggota WHERE is_admin!=TRUE");
    if (!empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}

/**
 * @param string $username
 * @param array $data
 *
 * @return ADORecordSet_pdo|bool|string string nama kolom apabila gagal / salah
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
        if ($key === 'user_name') {
            // apabila tidak valid
            $value = validasi_anggota_username($value);
            if (!$value) {
                return 'user_name';
            }
        }

        if ($key === 'password') {
            $value = sha1((string)$value);
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
 * @param int $id
 * @param array $data
 *
 * @return ADORecordSet_pdo|bool|string string nama kolom apabila gagal / salah
 */
function database_update_anggota_by_id($id, array $data)
{
    if (!is_numeric($id)) {
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
        if ($key === 'user_name') {
            // apabila tidak valid
            $value = validasi_anggota_username($value);
            if (!$value) {
                return 'user_name';
            }
        }

        if ($key === 'password') {
            $value = sha1((string)$value);
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
    $values[] = abs($id);
    return database_execute("UPDATE anggota {$set} WHERE id=?", $values);
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
 * @param int $id
 *
 * @return bool|array
 */
function database_get_anggota_by_id($id)
{
    if (!is_numeric($id)) {
        return false;
    }
    $record = database_execute("SELECT * FROM anggota WHERE id=?", [abs($id)]);
    if ($record->fields) {
        $password = $record->fields['password'];
        if (empty($password)) {
            $password = microtime();
        }
        if (!is_sha1($password)) {
            $password = sha1($password);
            $record->fields['password'] = $password;
            database_update_anggota_by_id($id, ["password" => $password]);
        }
        return $record->fields;
    }

    return false;
}

/**
 * @param array $data
 *
 * @return ADORecordSet_pdo|string string nama kolom apabila gagal
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

    if (!isset($values['user_name'])) {
        return 'user_name';
    }
    if (!isset($values['email'])) {
        return 'email';
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
 * @param string $id
 *
 * @return ADORecordSet_pdo|bool|int
 */
function delete_anggota_by_id($id)
{
    if (!is_numeric($id)) {
        return false;
    }

    // apabila login dan mencoba menghapus akun sendiri
    // jangan ijinkan
    $detail = get_current_user_detail();
    if ($detail && abs($detail['id']) === abs($id)) {
        return 0;
    }

    return database_execute("DELETE FROM anggota WHERE id=?", [$id]);
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
    return database_execute("SELECT * FROM anggota WHERE true ORDER BY id LIMIT {$limit} OFFSET {$offset}");
}

/**
 * @param string $username
 * @param int $offset
 * @param int $limit
 *
 * @return array|bool
 */
function cari_anggota_by_username($username = '', $offset = 0, $limit = 100)
{
    if (!is_string($username)) {
        return false;
    }

    $limit = !is_numeric($limit) || is_int(abs($limit)) ? 100 : abs($limit);
    $offset = !is_numeric($offset) || !is_int(abs($offset)) ? 0 : abs($offset);
    $username = trim(strtolower($username));
    if ($username == '') {
        $loop = create_query_loop_anggota($offset, $limit);
    } else {
        $loop = database_execute(
            "SELECT * FROM anggota
            WHERE user_name LIKE ? 
            ORDER BY LOCATE(?, user_name)
            DESC
            LIMIT {$limit}
            OFFSET {$offset}
        ",
            [
                "%{$username}%",
                $username,
            ]
        );
    }
    $return = [];
    if (is_object($loop) && !empty($loop->fields)) {
        foreach ((array) $loop->fields as $key => $value) {
            $return[$value['id']] = $value;
        }
    }
    return $return;
}