<?php
class RouterAnggota
{
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