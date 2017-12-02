<?php
/**
 * @return string
 */
function get_method()
{
    return strtoupper($_SERVER['REQUEST_METHOD']);
}

/**
 * @return string
 */
function get_scheme()
{
    static $scheme;
    if (isset($scheme)) {
        return $scheme;
    }
    $scheme = 'http';
    if (
        isset($_SERVER['HTTPS'])
        && strtolower($_SERVER['HTTPS']) == 'on'
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        || !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'
    ) {
        $scheme = 'https';
    }

    return $scheme;
}

function get_host()
{
    static $host;
    if (isset($host)) {
        return $host;
    }
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
    if (!$host && isset($_SERVER['SERVER_NAME'])) {
        $host = $_SERVER['SERVER_NAME'];
    }
    if (!$host) {
        $host = 'localhost';
    }
    $portUrl = parse_url("http://{$host}", PHP_URL_PORT);
    if (!$portUrl) {
        $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;
        if ($port && ! in_array($port, [80, 443])) {
            $host .= ":{$port}";
        }
    }
    return $host;
}

/**
 * @param string $path
 * @return string
 */
function get_base_url($path = '')
{
    static $base_url;
    if (!isset($base_url)) {
        $realPath = rtrim(get_base_path(), '/');
        $base_url = get_scheme() .'://'. get_host() . $realPath;
    }

    if (!is_string($path) || $path == '') {
        return $base_url;
    }

    $path = ltrim($path);
    $query = explode('?', $path);
    $query[0] = ltrim(preg_replace('/(\\\|\/)+/', '/', $query[0]), '/');
    return $base_url .'/' . implode('/', $query);
}

/**
 * @param string $path
 *
 * @return string
 */
function get_base_url_index($path = '')
{
    static $fileName;
    if (!$fileName) {
        $fileName =basename($_SERVER['SCRIPT_FILENAME']);
    }
    if (!is_string($path)) {
        $path = (string) $path;
    }
    if ($path) {
        $path = "/{$path}";
    }
    return get_base_url($fileName . $path);
}

/**
 * @return mixed
 */
function get_script_name()
{
    static $script_name;
    if (!isset($script_name)) {
        $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    }
    return $script_name;
}

/**
 * @return string
 */
function get_base_path()
{
    static $base_path;
    if (!isset($base_path)) {
        $base_path   = dirname(get_script_name());
    }

    return $base_path;
}

/**
 * @return string
 */
function get_request_uri()
{
    static $request_uri;
    if (isset($request_uri)) {
        return $request_uri;
    }

    $request_uri = $_SERVER['REQUEST_URI'];
    $script_name = get_script_name();
    $base_path   = get_base_path();
    if ($request_uri === $script_name) {
        $request_uri = '/';
    } elseif (strpos($request_uri, "{$script_name}/") === 0) {
        $request_uri = substr($request_uri, strlen($script_name));
        if ($base_path !== '/') {
            $request_uri = substr($request_uri, strlen($base_path));
        }
    }

    return $request_uri;
}

/**
 * @param string $target
 * @param int $code
 */
function redirect($target, $code = 301)
{
    if (!is_string($target)) {
        return;
    }

    $code = is_numeric($code) && is_int(abs($code))
        && in_array(abs($code), [301, 302])
        ? abs($code)
        : 302;
    $target = urldecode($target);
    if (!headers_sent()) {
        header('Location: '. $target, $code);
    } else {
        echo "<meta http-equiv=\"refresh\" content=\"0; url={$target}\">";
        // encode json
        $target = json_encode($target);
        echo "<script type='text/javascript'>window.location.href={$target};</script>";
    }
    exit(0);
}