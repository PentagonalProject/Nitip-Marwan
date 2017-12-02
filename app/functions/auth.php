<?php
/**
 * Mendapatkan semua sesi
 * selalu panggil fungsi untuk get_all_session()
 * untuk memanggil sesi
 *
 * @return array
 */
function &get_all_sessions()
{
    // -----------------------------------------------------
    // Start session
    // -----------------------------------------------------
    if (!session_id()) {
        session_start();
    }

    return $_SESSION;
}

/**
 * Dapatkan session sesuai dengan nama
 *
 * @param string $name
 * @param null $default
 *
 * @return array|mixed|null
 */
function get_session($name, $default = null)
{
    $session = get_all_sessions();
    $session = array_key_exists($name, $session) ? $session[$name] : $default;
    return $session;
}

/**
 * @return bool|string
 */
function get_current_user_name()
{
    if (is_login()) {
        $session = get_session('user');
        return $session['username'];
    }
    return false;
}

/**
 * @return array|bool
 */
function get_current_user_detail()
{
    static $user;
    $currentUserName = get_current_user_name();
    if (!$user) {
        $user = database_get_anggota($currentUserName);
    }
    return $currentUserName ? $user : false;
}

/**
 * @return bool
 */
function is_login()
{
    $session = get_session('user');
    if (!is_array($session)
        || empty($session['username'])
        || !is_string($session['username'])
        || empty($session['time'])|| !is_int($session['time'])
        || $session['time'] > time()+5
    ) {
        return false;
    }

    return (bool) database_get_anggota($session['username']);
}

/**
 * Buat Sesi Login
 *
 * @param string $username
 * @param string $password
 * @param bool $ignoreIfLogin
 *
 * @return bool|int boolean true apabila sukses, integer 1 apabila telah login
 *                          dan false apabila gagal
 */
function buat_login($username, $password, $ignoreIfLogin = false)
{
    if (!is_string($username) || !is_string($password)) {
        return false;
    }

    if ($ignoreIfLogin && is_login()) {
        return 1;
    }

    $user = database_get_anggota($username);
    if (!$user || $user['password'] !== sha1($password)) {
        return false;
    }

    $session =& get_all_sessions();
    $session_user = isset($session['user']) && is_array($session['user'])
        ? $session['user']
        : [];
    $session_user['username'] = $user['user_name'];
    $session_user['time']     = time();
    $session['user'] = $session_user;

    return true;
}
