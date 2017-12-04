<?php
class RouterPinjaman
{
    public static function Pinjaman($params)
    {
        check_redirect_login_router();
        if (!is_admin()) {
            redirect(get_base_url_index('/buku/pinjaman/ku'));
        }

        muat_layout('pinjaman-admin');
    }

    public static function Pinjamanku($params)
    {
        check_redirect_login_router();
        muat_layout('pinjaman-anggota');
    }
}