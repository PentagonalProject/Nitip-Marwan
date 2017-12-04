<?php
class RouterAnggota
{
    const MESSAGE_PROFILE_SIMPAN_SUKSES = '<div class="alert alert-success alert-dismissable">Akun Berhasil Diubah<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
    const MESSAGE_PROFILE_SIMPAN_GAGAL  = '<div class="alert alert-danger alert-dismissable">Gagal Menimpan Data<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';

    public static function ApiCari($params)
    {
        if (!is_login()) {
            if (!headers_sent()) {
                header('Content-Type: application/json;charset=utf-8', 401);
            }

            echo json_encode([
                "code" => 401,
                "error" => "Tidak Punya Hak Akses",
            ],JSON_PRETTY_PRINT);
            exit(0);
        }

        if (!headers_sent()) {
            header('Content-Type: application/json;charset=utf-8', 200);
        }
        $username = !isset($params['username'])
            ? ''
            : $params['username'];
        $limit = get('limit', 100);
        $offset = get('offset', 0);
        $limit = is_numeric($limit) ? intval(abs($limit)) : 100;
        $limit = $limit > 0 ? $limit : 100;
        $offset = is_numeric($offset) ? intval(abs($offset)) : 0;
        $offset = $limit > 0 ? $offset : 0;

        $pencarian = cari_anggota_by_username($username, $offset, $limit);
        $pencarian = is_array($pencarian) ? $pencarian : [];
        $value = [
            "code" => 200,
            "total" => count($pencarian),
            "data" => $pencarian
        ];
        if (get('array') == 'true') {
            $value = $value['data'];
        }
        echo json_encode($value,JSON_PRETTY_PRINT);
        exit(0);
    }

    public static function List($params)
    {
        check_redirect_admin_router();
        muat_layout('anggota', $params);
    }

    public static function Baru($params)
    {
        check_redirect_admin_router();
        if (get_method() === 'POST') {
            $data = post('user');
            if (!is_array($data)) {
                redirect(get_base_url_index("anggota/baru?status=error"));
                return;
            }
            if (is_array($data)) {
                $session =& get_all_sessions();
                $session['anggota_baru'] = $data;
                unset($session['anggota_baru']['password']);
                if (empty($data['user_name'])
                    || !is_string($data['user_name'])
                    || !($username = validasi_anggota_username($data['user_name']))
                ) {
                    $user = !is_string($data['user_name']) || trim($data['user_name']) === ''
                        ? ''
                        : '<span class="label label-info">'.htmlentities(trim($data['user_name'])).'</span>';
                    $status = "username {$user} tidak valid";
                    unset($session['anggota_baru']['user_name']);
                    redirect(get_base_url_index("anggota/baru?status=error&message="). rawurlencode(base64_encode($status)));
                    return;
                }

                $anggota_exists = database_get_anggota($username);
                if ($anggota_exists) {
                    unset($session['anggota_baru']['user_name']);
                    $status = "username <span class=\"label label-info\">{$username}</span> telah terdaftar, gunakan username lainnya.";
                    redirect(get_base_url_index("anggota/baru?status=error&message="). rawurlencode(base64_encode($status)));
                }
                $toSave = [];
                if (isset($data['password']) && is_string($data['password']) && trim($data['password']) !== '') {
                    $toSave['password'] = $data['password'];
                } else {
                    $status = "Password tidak boleh kosong.";
                    redirect(get_base_url_index("anggota/baru?status=error&message="). rawurlencode(base64_encode($status)));
                }

                if (isset($data['nama_depan']) && is_string($data['nama_depan']) && trim($data['nama_depan']) !== '') {
                    $toSave['nama_depan'] = trim($data['nama_depan']);
                } else {
                    unset($session['nama_depan']);
                    $status = "Nama Depan tidak boleh kosong.";
                    redirect(get_base_url_index("anggota/baru?status=error&message="). rawurlencode(base64_encode($status)));
                }

                if (isset($data['email']) && is_string($data['email']) && trim($data['email']) !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $toSave['email'] = strtolower(trim($data['email']));
                } else {
                    unset($session['email']);
                    $status = "Email tidak valid.";
                    redirect(get_base_url_index("anggota/baru?status=error&message="). rawurlencode(base64_encode($status)));
                }

                if (isset($data['nama_belakang']) && is_string($data['nama_belakang'])) {
                    $toSave['nama_belakang'] = trim($data['nama_belakang']);
                }
                if (isset($data['user_name']) && is_string($data['user_name']) && trim($data['email']) !== '') {
                    $toSave['user_name'] = $username;
                }
                $toSave['is_admin'] = isset($data['is_admin']) && is_string($data['is_admin'])
                                      && in_array(strtolower($data['is_admin']), ['on', 'yes', 'y', '1'])
                    ? true
                    : false;
                $status = database_create_anggota($toSave);
                if (is_object($status)) {
                    $user_id = database_get_anggota($toSave['user_name']);
                    $user_id = $user_id['id'];
                    redirect(get_base_url_index("anggota/ubah/{$user_id}?status=tambah"));
                    return;
                }

                redirect(get_base_url_index("anggota/baru/?status=error"));
                return;
            }
        }

        $status = get('status');
        if ($status && is_string($status) && in_array(strtolower($status), ['sukses', 'error'])) {
            $params['message'] = strtolower($status) == 'sukses'
                ? self::MESSAGE_PROFILE_SIMPAN_SUKSES
                : self::MESSAGE_PROFILE_SIMPAN_GAGAL;
            if (strtolower($status) === 'error') {
                $message = get('message');
                $message = is_string($message) ? base64_decode(rawurldecode($message)) : null;
                if ($message) {
                    $params['message'] = "<div class=\"alert alert-danger alert-dismissable\">{$message}<div class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</div></div>";
                }
            }
        }
        muat_layout('anggota-baru', $params);
    }

    public static function Ubah($params)
    {
        check_redirect_admin_router();
        $user_id = empty($params['id'])
            ? null
            : $params['id'];
        if (!$user_id) {
            redirect(get_base_url_index('anggota'));
        }
        if (abs($user_id) === get_current_user_id()) {
            redirect(get_base_url_index('profile'));
        }
        $params['user_id'] = $user_id;
        if (get_method() === 'POST') {
            $data = post('user');
            if (!is_array($data)) {
                redirect(get_base_url_index("anggota/ubah/{$user_id}?status=error"));
                return;
            }
            if (is_array($data)) {
                $toSave = database_get_anggota_by_id($user_id);
                if (!isset($data['user_name'])) {
                    $data['user_name'] = null;
                }

                $username = validasi_anggota_username($data['user_name']);
                if (!$username) {
                    $user = !is_string($data['user_name']) || trim($data['user_name']) === ''
                        ? ''
                        : '<span class="label label-info">'.htmlentities(trim($data['user_name'])).'</span>';
                    $status = "username {$user} tidak valid";
                    redirect(get_base_url_index("anggota/ubah/{$user_id}?status=error&message="). rawurlencode(base64_encode($status)));
                }
                $anggota_exists = database_get_anggota($username);
                if ($anggota_exists && $anggota_exists['id'] !== $toSave['id']) {
                    $status = "username <span class=\"label label-info\">{$username}</span> telah terdaftar, gunakan username lainnya.";
                    redirect(get_base_url_index("anggota/ubah/{$user_id}?status=error&message="). rawurlencode(base64_encode($status)));
                }
                unset($toSave['password']);
                if (isset($data['nama_depan']) && is_string($data['nama_depan']) && trim($data['nama_depan']) !== '') {
                    $toSave['nama_depan'] = trim($data['nama_depan']);
                }
                if (isset($data['nama_belakang']) && is_string($data['nama_belakang'])) {
                    $toSave['nama_belakang'] = trim($data['nama_belakang']);
                }
                if (isset($data['email']) && is_string($data['email']) && trim($data['email']) !== '') {
                    $toSave['email'] = strtolower(trim($data['email']));
                }
                if (isset($data['user_name']) && is_string($data['user_name']) && trim($data['email']) !== '') {
                    $toSave['user_name'] = $username;
                }
                if (isset($data['password']) && is_string($data['password']) && trim($data['password']) !== '') {
                    $toSave['password'] = $data['password'];
                }
                $toSave['is_admin'] = isset($data['is_admin']) && is_string($data['is_admin'])
                    && in_array(strtolower($data['is_admin']), ['on', 'yes', 'y', '1'])
                    ? true
                    : false;
                $status = database_update_anggota_by_id($toSave['id'], $toSave);
                if (is_object($status)) {
                    redirect(get_base_url_index("anggota/ubah/{$user_id}?status=sukses"));
                    return;
                }
                redirect(get_base_url_index("anggota/ubah/{$user_id}?status=error"));
                return;
            }
        }

        $status = get('status');
        if ($status && is_string($status) && in_array(strtolower($status), ['tambah', 'sukses', 'error'])) {
            $params['message'] = strtolower($status) == 'sukses'
                ? self::MESSAGE_PROFILE_SIMPAN_SUKSES
                : self::MESSAGE_PROFILE_SIMPAN_GAGAL;
            if (strtolower($status) == 'tambah') {
                $params['message'] = '<div class="alert alert-success alert-dismissable">Akun Berhasil Ditambahkan<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
            }
            if (strtolower($status) === 'error') {
                $message = get('message');
                $message = is_string($message) ? base64_decode(rawurldecode($message)) : null;
                if ($message) {
                    $params['message'] = "<div class=\"alert alert-danger alert-dismissable\">{$message}<div class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</div></div>";
                }
            }
        }

        muat_layout('anggota-ubah', $params);
    }

    public static function Hapus($params)
    {
        check_redirect_admin_router();
        $user_id = empty($params['id'])
            ? null
            : $params['id'];
        if (!$user_id) {
            redirect(get_base_url_index('anggota'));
        }
        if (abs($user_id) === get_current_user_id()) {
            $referer = isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], get_host()) !== false
                ? $_SERVER['HTTP_REFERER']
                : get_base_url_index('anggota');
            redirect($referer);
        }
        $params['user_id'] = $user_id;
        muat_layout('anggota-hapus', $params);
    }

    /**
     * Untuk Render Kelola Akun
     *
     * @param array $params
     */
    public static function Profile($params)
    {
        check_redirect_login_router();
        $variable = [];
        if (get_method() === 'POST') {
            $data = post('user');
            if (!is_array($data)) {
                redirect(get_base_url_index('profile?status=error'));
                return;
            }
            if (is_array($data)) {
                $toSave = get_current_user_detail();
                $username = $toSave['user_name'];
                unset($toSave['is_admin']);
                unset($toSave['user_name']);
                unset($toSave['password']);
                if (isset($data['nama_depan']) && is_string($data['nama_depan']) && trim($data['nama_depan']) !== '') {
                    $toSave['nama_depan'] = trim($data['nama_depan']);
                }
                if (isset($data['nama_belakang']) && is_string($data['nama_belakang'])) {
                    $toSave['nama_belakang'] = trim($data['nama_belakang']);
                }
                if (isset($data['email']) && is_string($data['email']) && trim($data['email']) !== '') {
                    $toSave['email'] = strtolower(trim($data['email']));
                }
                if (isset($data['password']) && is_string($data['password']) && trim($data['password']) !== '') {
                    $toSave['password'] = $data['password'];
                }
                $status = database_update_anggota($username, $toSave);
                if (is_object($status)) {
                    redirect(get_base_url_index('profile?status=sukses'));
                    return;
                }
                redirect(get_base_url_index('profile?status=error'));
                return;
            }
        }

        $status = get('status');
        if ($status && is_string($status) && in_array(strtolower($status), ['sukses', 'error'])) {
            $variable['message'] = $status == 'sukses'
                ? self::MESSAGE_PROFILE_SIMPAN_SUKSES
                : self::MESSAGE_PROFILE_SIMPAN_GAGAL;
        }

        muat_layout('profile', $variable);
    }
}