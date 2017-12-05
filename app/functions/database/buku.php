<?php
/**
 * @return int
 */
function database_get_jumlah_buku()
{
    $data = database_execute("SELECT COUNT(*) AS hitung FROM buku");
    if ($data && !empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}

/**
 * @return int
 */
function database_get_jumlah_pinjaman()
{
    if (is_admin()) {
        $data = database_execute("
            SELECT COUNT(*) AS hitung FROM buku
            WHERE 
              anggota_id_pinjam IS NOT NULL
              OR anggota_id_pinjam <> 0
        ");
    } else {
        $user = get_current_user_detail();
        $id = $user['id'];
        $data = database_execute("
            SELECT COUNT(*) AS hitung FROM buku
            WHERE 
              anggota_id_pinjam=?
        ", [$id]);
    }

    if ($data && !empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}

/**
 * @param int $id
 *
 * @return float|int
 */
function database_get_jumlah_pinjaman_by_user_id($id)
{
    $user = database_get_anggota_by_id($id);
    if ($user) {
        return 0;
    }
    $id = $user['id'];
    $data = database_execute("
        SELECT COUNT(*) AS hitung FROM buku
        WHERE 
          anggota_id_pinjam=?
    ", [$id]);

    if ($data && !empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}

/**
 * @param int $username
 *
 * @return float|int
 */
function database_get_jumlah_pinjaman_by_username($username)
{
    $user = database_get_anggota($username);
    if ($user) {
        return 0;
    }
    $id = $user['id'];
    $data = database_execute("
        SELECT COUNT(*) AS hitung FROM buku
        WHERE 
          anggota_id_pinjam=?
    ", [$id]);

    if ($data && !empty($data->fields['hitung'])) {
        return abs($data->fields['hitung']);
    }
    return 0;
}

/**
 * @param int $offset
 * @param int $limit
 * @param bool $isDesc
 *
 * @return ADORecordSet_pdo
 */
function create_query_loop_buku($offset = 0, $limit = 100, $isDesc = true)
{
    $offset = abs($offset);
    $limit  = abs($limit);
    $isDesc = $isDesc ? " DESC " : "ASC";
    return database_execute("SELECT * FROM buku WHERE true ORDER BY id {$isDesc} LIMIT {$limit} OFFSET {$offset}");
}

/**
 * @param int $offset
 * @param int $limit
 * @param bool $isDesc
 *
 * @return ADORecordSet_pdo
 */
function create_query_loop_buku_pinjaman($offset = 0, $limit = 100, $isDesc = true)
{
    $offset = abs($offset);
    $limit  = abs($limit);
    $isDesc = $isDesc ? " DESC " : "ASC";
    return database_execute("SELECT * FROM buku WHERE anggota_id_pinjam IS NOT NULL AND anggota_id_pinjam > 0 ORDER BY id {$isDesc} LIMIT {$limit} OFFSET {$offset}");
}

/**
 * @param int $user_id
 * @param int $offset
 * @param int $limit
 * @param bool $isDesc
 *
 * @return ADORecordSet_pdo|false
 */
function create_query_loop_buku_pinjaman_by_user_id($user_id, $offset = 0, $limit = 100, $isDesc = true)
{
    $user = database_get_anggota_by_id($user_id);
    if (!$user) {
        return false;
    }

    $offset = abs($offset);
    $limit  = abs($limit);
    $isDesc = $isDesc ? " DESC " : "ASC";
    return database_execute(
        "SELECT * FROM buku WHERE anggota_id_pinjam =? ORDER BY id {$isDesc} LIMIT {$limit} OFFSET {$offset}",
        [
            $user_id
        ]
    );
}

/**
 * @param int $username
 * @param int $offset
 * @param int $limit
 * @param bool $isDesc
 *
 * @return ADORecordSet_pdo|false
 */
function create_query_loop_buku_pinjaman_by_username($username, $offset = 0, $limit = 100, $isDesc = true)
{
    $user = database_get_anggota($username);
    if (!$user) {
        return false;
    }

    $offset = abs($offset);
    $limit  = abs($limit);
    $isDesc = $isDesc ? " DESC " : "ASC";
    return database_execute(
        "SELECT * FROM buku WHERE anggota_id_pinjam =? ORDER BY id {$isDesc} LIMIT {$limit} OFFSET {$offset}",
        [
            $user['id']
        ]
    );
}

/**
 * @param string $nama
 * @param int $limit
 * @return array|bool
 */
function cari_buku_by_judul($nama, $offset = 0,$limit = 100)
{
    if (!is_string($nama)) {
        return false;
    }

    $limit = !is_numeric($limit) || is_int(abs($limit)) ? 100 : abs($limit);
    $offset = !is_numeric($limit) || is_int(abs($limit)) ? 0 : abs($limit);
    $nama = trim(strtolower($nama));
    if ($nama == '') {
        $loop =create_query_loop_buku($offset, $limit);
    } else {
        $loop = database_execute(
            "SELECT * FROM buku
            WHERE judul LIKE ? 
            ORDER BY LOCATE(?, judul)
            DESC
            LIMIT {$limit}
            OFFSET {$offset}
        ",
            [
                "%{$nama}%",
                $nama,
            ]
        );
    }
    $return = [];
    if (is_object($loop) && !empty($loop->fields)) {
        foreach ($loop as $key => $value) {
            $return[] = $value;
        }
    }
    return $return;
}

/**
 * @param string $nama_pengarang
 * @param int $offset
 * @param int $limit
 *
 * @return array|bool
 */
function cari_buku_by_pengarang($nama_pengarang = '', $offset = 0, $limit = 100)
{
    if (!is_string($nama_pengarang)) {
        return false;
    }
    $offset = !is_numeric($offset) || !is_int(abs($offset)) ? 0 : abs($offset);
    $limit = !is_numeric($limit) || is_int(abs($limit)) ? 100 : abs($limit);
    $nama_pengarang = trim(strtolower($nama_pengarang));
    if ($nama_pengarang == '') {
        $loop = create_query_loop_buku($offset, $limit);
    } else {
        $loop = database_execute(
            "SELECT * FROM buku
            WHERE pengarang LIKE ? 
            ORDER BY LOCATE(?, pengarang)
            DESC
            LIMIT {$limit}
            OFFSET {$offset}
        ",
            [
                "%{$nama_pengarang}%",
                $nama_pengarang,
            ]
        );
    }

    $return = [];
    if (is_object($loop) && !empty($loop->fields)) {
        foreach ($loop as $key => $value) {
            $return[] = $value;
        }
    }
    return $return;
}
/**
 * @param string $nama_penerbit
 * @param int $offset
 * @param int $limit
 *
 * @return array|bool
 */
function cari_buku_by_penerbit($nama_penerbit = '', $offset = 0, $limit = 100)
{
    if (!is_string($nama_penerbit)) {
        return false;
    }
    $offset = !is_numeric($offset) || !is_int(abs($offset)) ? 0 : abs($offset);
    $limit = !is_numeric($limit) || is_int(abs($limit)) ? 100 : abs($limit);
    $nama_penerbit = trim(strtolower($nama_penerbit));
    if ($nama_penerbit == '') {
        $loop = create_query_loop_buku($offset, $limit);
    } else {
        $loop = database_execute(
            "SELECT * FROM buku
            WHERE penerbit LIKE ? 
            ORDER BY LOCATE(?, penerbit)
            DESC
            LIMIT {$limit}
            OFFSET {$offset}
        ",
            [
                "%{$nama_penerbit}%",
                $nama_penerbit,
            ]
        );
    }

    $return = [];
    if (is_object($loop) && !empty($loop->fields)) {
        foreach ($loop as $key => $value) {
            $return[] = $value;
        }
    }
    return $return;
}

/**
 * @param string $nama_pengarang
 * @param int $offset
 * @param int $limit
 *
 * @return array|bool
 */
function cari_pengarang_dari_buku($nama_pengarang = '', $offset = 0, $limit = 100)
{
    if (!is_string($nama_pengarang)) {
        return false;
    }

    $offset = !is_numeric($offset) || !is_int(abs($offset)) ? 0 : abs($offset);
    $limit = !is_numeric($limit) || is_int(abs($limit)) ? 100 : abs($limit);
    $nama_pengarang = trim(strtolower($nama_pengarang));
    if ($nama_pengarang == '') {
        $loop = database_execute("SELECT pengarang FROM buku WHERE true GROUP BY pengarang ORDER BY id LIMIT {$limit} OFFSET {$offset}");
    } else {
        $loop = database_execute(
            "SELECT pengarang FROM buku
                WHERE pengarang LIKE ?
                GROUP BY pengarang 
                ORDER BY LOCATE(?, pengarang)
                DESC
                LIMIT {$limit}
                OFFSET {$offset}
            ",
            [
                "%{$nama_pengarang}%",
                $nama_pengarang,
            ]
        );
    }

    $return = [];
    if (is_object($loop) && !empty($loop->fields)) {
        foreach ($loop as $key => $value) {
            $return[] = $value['pengarang'];
        }
    }
    return $return;
}

/**
 * @param string $nama_penerbit
 * @param int $offset
 * @param int $limit
 *
 * @return array|bool
 */
function cari_penerbit_dari_buku($nama_penerbit = '', $offset = 0, $limit = 100)
{
    if (!is_string($nama_penerbit)) {
        return false;
    }

    $offset = !is_numeric($offset) || !is_int(abs($offset)) ? 0 : abs($offset);
    $limit = !is_numeric($limit) || is_int(abs($limit)) ? 100 : abs($limit);
    $nama_penerbit = trim(strtolower($nama_penerbit));
    if ($nama_penerbit == '') {
        $loop = database_execute("SELECT penerbit FROM buku WHERE true GROUP BY penerbit ORDER BY id LIMIT {$limit} OFFSET {$offset}");
    } else {
        $loop = database_execute(
            "SELECT penerbit FROM buku
                WHERE penerbit LIKE ?
                GROUP BY penerbit
                ORDER BY LOCATE(?, penerbit)
                DESC
                LIMIT {$limit}
                OFFSET {$offset}
            ",
            [
                "%{$nama_penerbit}%",
                $nama_penerbit,
            ]
        );
    }

    $return = [];
    if (is_object($loop) && !empty($loop->fields)) {
        foreach ($loop as $key => $value) {
            $return[] = $value['penerbit'];
        }
    }

    return $return;
}

/**
 * @param array $data
 *
 * @return ADORecordSet_pdo|string string nama kolom apabila gagal
 */
function database_create_buku(array $data)
{
    $fields = [
        'judul',
        'pengarang',
        'penerbit',
        'tahun',
        'keterangan',
        'path_gambar',
    ];

    $values = [];
    foreach ($data as $key => $value) {
        if (!in_array($key, $fields)) {
            continue;
        }
        if ($key === 'tahun') {
            $value = (int) $value;
        }
        $values[$key] = $value;
    }

    if (!isset($values['judul'])) {
        return 'judul';
    }
    if (!isset($values['pengarang'])) {
        return 'pengarang';
    }
    if (!isset($values['penerbit'])) {
        return 'penerbit';
    }
    if (!isset($values['tahun'])) {
        return 'tahun';
    }

    $columns     = implode(', ', array_keys($values));
    $valueValue  = rtrim(str_repeat('?, ', count($values)), ', ');
    return database_execute(
        "INSERT INTO buku ({$columns}) VALUES ({$valueValue})", array_values($values)
    );
}

/**
 * @param int $buku_id
 * @param array $data
 *
 * @return ADORecordSet_pdo|string string nama kolom apabila gagal
 */
function database_update_buku($buku_id, array $data)
{
    if (!database_get_buku_by_id($buku_id)) {
        return false;
    }

    $fields = [
        'judul',
        'pengarang',
        'penerbit',
        'tahun',
        'keterangan',
        'path_gambar',
        'anggota_id_pinjam',
        'tanggal_pinjam',
    ];

    $values = [];
    $set = '';
    foreach ($data as $key => $value) {
        if (!in_array($key, $fields)) {
            continue;
        }
        if ($key === 'tahun') {
            $value = (int) $value;
        }
        $values[$key] = $value;
        $set .= "{$key}=?, ";
    }

    if (!isset($values['judul'])) {
        return 'judul';
    }
    if (!isset($values['pengarang'])) {
        return 'pengarang';
    }
    if (!isset($values['penerbit'])) {
        return 'penerbit';
    }
    if (!isset($values['tahun'])) {
        return 'tahun';
    }

    $set = 'SET '.trim($set, ', ');
    $values[] = abs($buku_id);
    return database_execute("UPDATE buku {$set} WHERE id=?", array_values($values));
}

/**
 * @param int $id
 *
 * @return bool|array
 */
function database_get_buku_by_id($id)
{
    if (!is_numeric($id)) {
        return false;
    }
    $record = database_execute("SELECT * FROM buku WHERE id=?", [abs($id)]);
    if ($record->fields) {
        return $record->fields;
    }

    return false;
}

/**
 * @param int $id
 *
 * @return ADORecordSet_pdo|bool
 */
function database_get_pinjam_buku_by_id($id)
{
    if (!is_numeric($id)) {
        return false;
    }
    $record = database_execute("SELECT * FROM buku WHERE anggota_id_pinjam=?", [abs($id)]);
    return $record;
}

/**
 * @param string $username
 *
 * @return ADORecordSet_pdo|bool
 */
function database_get_pinjam_buku_by_username($username)
{
    $user = database_get_anggota($username);
    if (!$user) {
        return false;
    }

    $record = database_execute("SELECT * FROM buku WHERE anggota_id_pinjam=?", [abs($user['id'])]);
    if (! $record || isset($record->fields) || !is_array($record->fields)
        || empty($record->fields)
    ) {
        return false;
    }

    return $record;
}


/**
 * @param string $id
 *
 * @return ADORecordSet_pdo|bool|
 */
function delete_buku_by_id($id)
{
    if (!is_numeric($id)) {
        return false;
    }

    $return = database_execute("DELETE FROM buku WHERE id=?", [$id]);
    return $return;
}
