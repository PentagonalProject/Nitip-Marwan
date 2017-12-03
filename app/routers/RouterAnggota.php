<?php
class RouterAnggota
{
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

        echo json_encode([
            "code" => 200,
            "total" => count($pencarian),
            "data" => $pencarian
        ],JSON_PRETTY_PRINT);
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

    public static function Profile($params)
    {
        check_redirect_login_router();
        muat_layout('profile');
    }
}