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