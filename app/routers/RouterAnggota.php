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

        $pencarian = cari_anggota_by_username($username);
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
        muat_layout('anggota-baru', $params);
    }

    public static function Ubah($params)
    {
        check_redirect_admin_router();
        $username = empty($params['username'])
            ? null
            : $params['username'];
        if (!$username) {
            redirect(get_base_url_index('anggota'));
        }

        muat_layout('anggota-ubah', $params);
    }

    public static function Hapus($params)
    {
        check_redirect_admin_router();
        $username = empty($params['username'])
            ? null
            : $params['username'];
        if (!$username || strtolower($username) === strtolower(get_current_user_name())) {
            redirect(get_base_url_index('anggota'));
        }
//        print_r($params);exit;
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
                if (isset($data['nama_awal']) && is_string($data['nama_awal']) && trim($data['nama_awal']) !== '') {
                    $toSave['nama_awal'] = trim($data['nama_awal']);
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