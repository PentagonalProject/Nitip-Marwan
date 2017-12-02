<?php
/**
 * @var \FastRoute\RouteCollector $this
 */
if (!isset($this) || !$this instanceof \FastRoute\RouteCollector) {
    if (!headers_sent()) {
        header('Location: ../', 301);
    }
    exit;
}

/**
 * url Untuk Login
 *
 * @return string
 */
function router_url_login()
{
    return get_base_url_index('masuk');
}

/**
 * Url untuk logout
 *
 * @return string
 */
function router_url_keluar()
{
    return get_base_url_index('keluar');
}

/**
 * Helper untuk check login
 */
function router_check_redirect_login()
{
    if (!is_login()) {
        redirect(router_url_login());
        return false;
    }

    return true;
}


// -----------------------------------------------------------------
// MULAI ROUTER
// -----------------------------------------------------------------

/**
 * HTTP Method tersedia
 *
 * @var array
 */
$allMethods = [
    'GET',
    'POST',
    'PUT',
    'DELETE',
    'PATCH',
    'TRACE',
    'HEAD',
];

// KELUAR
$this->addRoute($allMethods, '/keluar[/]', function () {
    router_check_redirect_login();
    $_SESSION =& get_all_sessions();
    if (!isset($_SESSION['user'])) {
        $_SESSION['user'] = [];
    }
    unset($_SESSION['user']['username'], $_SESSION['user']['time']);
    redirect(router_url_login().'?error=logout');
});

// MASUK / LOGIN
$this->addRoute($allMethods, '/masuk[/]', function () {
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
        redirect(router_url_login() .'?error=true');
        return;
    }

    if ($error) {
        if ($error == 'logout') {
            $variable['message'] = '<div class="alert alert-success alert-dismissable">Anda Berhasil Keluar<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
        } else {
            $variable['message'] = '<div class="alert alert-danger alert-dismissable">Otentifikasi Gagal<div class="close" data-dismiss="alert" aria-label="close">&times;</div></div>';
        }
    }

    muat_layout('login', $variable);
});


// HOME PAGE
$this->addRoute($allMethods, '/', function () {
    // check login
    router_check_redirect_login();
    muat_layout('dasbor');
});
