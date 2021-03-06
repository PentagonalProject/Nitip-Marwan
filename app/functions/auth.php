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
 * Hapus session sesuai dengan nama
 *
 * @param string $name
 *
 * @return array|mixed|null
 */
function delete_session($name)
{
    $session = get_all_sessions();
    if (array_key_exists($name, $session)) {
        unset($_SESSION[$name]);
        return true;
    }
    return false;
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
 * @return bool|int
 */
function get_current_user_id()
{
    static $id;
    if (isset($id)) {
        return $id;
    }

    $id = false;
    $detail = get_current_user_detail();
    if ($detail && isset($detail['id'])) {
        $id = abs($detail['id']);
    }

    return $id;
}

/**
 * @return bool|string
 */
function get_current_first_name()
{
    $user_detail = get_current_user_detail();
    if (empty($user_detail['nama_depan'])) {
        return false;
    }

    return $user_detail['nama_depan'];
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

function is_admin()
{
    $userDetail = get_current_user_detail();
    if (!$userDetail) {
        return false;
    }
    return $userDetail['is_admin'];
}

/**
 * @param string $username
 *
 * @return bool
 */
function is_user_is_admin($username)
{
    if (!is_string($username)) {
        return false;
    }
    $user = database_get_anggota($username);
    if (!$user) {
        return false;
    }

    return $user['is_admin'];
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
