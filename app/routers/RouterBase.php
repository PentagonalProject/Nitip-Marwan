<?php
class RouterBase
{
    const MESSAGE_KELUAR = '<div class="alert alert-success alert-dismissable">Anda Berhasil Keluar<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
    const MESSAGE_GAGAL  = '<div class="alert alert-danger alert-dismissable">Otentifikasi Gagal<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';

    public static function Home()
    {
        // check login
        check_redirect_login_router();
        muat_layout('dasbor');
    }

    public static function Keluar()
    {
        check_redirect_login_router();
        $_SESSION =& get_all_sessions();
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = [];
        }
        unset($_SESSION['user']['username'], $_SESSION['user']['time']);
        redirect(url_login() . '?error=logout');
    }

    public static function Daftar()
    {
        // apabila login
        if (is_login()) {
            redirect(get_base_url());
            return;
        }
    }

    public static function Masuk()
    {
        // apabila login
        if (is_login()) {
            redirect(get_base_url());
            return;
        }

        $variable = [];
        $error = get('error');
        // validasikan apabila ada post data
        if (get_method() === 'POST') {
            // lakukan validasi
            $userName = post('username');
            $password = post('password');
            if (buat_login($userName, $password)) {
                // redirect success
                redirect(get_base_url());
                return;
            }

            redirect(url_login() . '?error=true');
            return;
        }

        if ($error && is_string($error) && in_array(strtolower($error), ['logout', 'true', 'error'])) {
            $variable['message'] = $error == 'logout' ? self::MESSAGE_KELUAR : self::MESSAGE_GAGAL;
        }

        muat_layout('login', $variable);
    }
}
