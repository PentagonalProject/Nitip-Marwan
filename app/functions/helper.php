<?php
/**
 * Check if sha1 lower case
 *
 * @param string $string
 * @param bool $caseInsensitive
 *
 * @return bool
 */
function is_sha1($string, $caseInsensitive = false)
{
    if (!is_string($string)) {
        return false;
    }
    $regex = '/[0-9a-f]{40}/';
    $caseInsensitive && $regex .= 'i';
    return (bool) preg_match($regex, $string);
}

/**
 * Check if sha1 lower case
 *
 * @param string $string
 * @param bool $caseInsensitive
 *
 * @return bool
 */
function is_md5($string, $caseInsensitive = false)
{
    if (!is_string($string)) {
        return false;
    }
    $regex = '/[0-9a-f]{32}/';
    $caseInsensitive && $regex .= 'i';
    return (bool) preg_match($regex, $string);
}

/**
 * @param string|int $string
 * @param mixed $default
 *
 * @return mixed
 */
function get($string = null, $default = null)
{
    if ($string === null) {
        return $_GET;
    }

    return array_key_exists($string, $_GET)
        ? $_GET[$string]
        : $default;
}

/**
 * @param string|int $string
 * @param mixed $default
 *
 * @return mixed
 */
function post($string = null, $default = null)
{
    if ($string === null) {
        return $_POST;
    }

    return array_key_exists($string, $_POST)
        ? $_POST[$string]
        : $default;
}

/**
 * Muat Layout
 *
 * @param string $layout
 * @param array $variable
 */
function muat_layout($layout, $variable = [])
{
    if (!is_string($layout)) {
        return;
    }

    static $layoutDirectory;
    if (!isset($layoutDirectory)) {
        $layoutDirectory = __DIR__ . '/../../layout/';
        $layoutDirectory = realpath($layoutDirectory)?: $layoutDirectory;
        $layoutDirectory .= '/';
    }

    // set default variable
    if (!is_array($variable)) {
        $variable = ['variable' => $variable];
    }
    $layout = ltrim($layout, '/');
    if (file_exists($layoutDirectory . $layout)) {
        // extension harus phtml
        if (pathinfo($layout, PATHINFO_EXTENSION) !== 'phtml') {
            return;
        }
        include ($layoutDirectory . $layout);
        return;
    }

    $layout = $layoutDirectory . $layout .'.phtml';
    if (file_exists($layout)) {
        include($layout);
    }
}

/**
 * @return bool
 */
function is_404()
{
    return defined('NOT_FOUND_404') && NOT_FOUND_404;
}

/**
 * @return bool
 */
function is_error()
{
    return defined('ERROR_CODE') && ERROR_CODE;
}

/**
 * @return bool
 */
function is_found()
{
    return ! is_404() && defined('FOUND_200') && FOUND_200;
}

/**
 * Tambahkan class atif untuk navigasi
 *
 * @param string $pos
 * @param string $not
 *
 * @return string
 */
function add_class_active($pos, $not = null)
{
    if (!is_string($pos)) {
        $pos = '';
    }
    if (!is_string($not)) {
        $not = null;
    }
    $not = is_string($not) && trim($not) !== '' ? trim(strtolower(trim($not)), '/') : null;
    $uri = ltrim(get_request_uri(), '/') . '/';
    $pos = trim($pos, '/') .'/';
    $pos2 = trim($pos, '/') .'?';
    if (stripos($uri, $pos) === 0) {
        if (!$not || stripos($uri, $pos. $not) !== 0) {
            return 'active';
        }
    }
    if (stripos($uri, $pos2) === 0) {
        if (!$not || stripos($uri, $pos2. $not) !== 0) {
            return 'active';
        }
    }

    return '';
}

/**
 * @return string
 */
function get_path_upload_gambar()
{
    static $path;
    if (!isset($path)) {
        $path = __DIR__ .'/../../gambar';
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }
        $path = realpath($path)?: $path;
    }

    return $path;
}
