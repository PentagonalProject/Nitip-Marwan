<?php
class RouterBuku
{
    public static function Baru($params)
    {
        check_redirect_admin_router();
        muat_layout('buku-baru', $params);
    }

    public static function Ubah($params)
    {
        check_redirect_admin_router();
        $id = empty($params['id'])
            ? null
            : $params['id'];
        if (!$id) {
            redirect(get_base_url_index('buku'));
        }

        muat_layout('buku-ubah', $params);
    }

    public static function Hapus($params)
    {
        check_redirect_admin_router();
        $id = empty($params['id'])
            ? null
            : $params['id'];
//        print_r($params);exit;
    }
    public static function List($params)
    {
        check_redirect_login_router();
        muat_layout('buku', $params);
    }

    public static function Pinjaman($params)
    {
        check_redirect_login_router();
        muat_layout('pinjaman-'.(is_admin() ? 'admin' : 'anggota'));
    }
}