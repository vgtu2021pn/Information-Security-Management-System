<?php
// language.php
// Usage:
// - Include at the top of pages that should respect language selection: require_once __DIR__.'/language.php';
// - Or call via GET (e.g. /language/language.php?lang=en&return=/about) to set language and redirect back.
//
// What it does:
// 1) Validates requested language against $SUPPORTED_LANGS.
// 2) Detects language from URL prefix, cookie, or Accept-Language header.
// 3) Sets a 'lang' cookie (1 year), sends Content-Language and Vary headers.
// 4) Optionally redirects to canonical language-prefixed path if $CANONICAL_PREFIX is true.

if (php_sapi_name() === 'cli') return; // no-op in CLI

// Configuration
$SUPPORTED_LANGS = ['en' => 'English', 'lt' => 'Lietuvių', 'pl' => 'Polska'];
$DEFAULT_LANG = 'en';
$COOKIE_NAME = 'site_lang';
$COOKIE_TTL = 31536000; 
$CANONICAL_PREFIX = true; // URLs to /{lang}/path
$PREFIX_REGEX = '/^[a-z]{2}$/';

// Helpers
function send_language_headers($lang) {
    header('Vary: Accept-Language, Cookie', false);
    header('Content-Language: ' . $lang, true);
}

// Parse path and determine first segment
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_path = preg_replace('#/+#', '/', $uri_path); // tidy
$path_segments = array_values(array_filter(explode('/', trim($uri_path, '/')))); // zero-indexed

// 1) If access via direct set-language endpoint (GET or POST param "lang")
$requested_lang = null;
if (!empty($_REQUEST['lang'])) {
    $requested_lang = strtolower(substr(trim($_REQUEST['lang']), 0, 5));
    if (!array_key_exists($requested_lang, $SUPPORTED_LANGS)) {
        // maybe user sent full language name; try two-letter fallback
        $requested_lang = null;
    }
}

// 2) If the first path segment is a language code: e.g., /en/about
$path_has_lang = false;
$lang_from_path = null;
if (!empty($path_segments)) {
    $seg0 = strtolower($path_segments[0]);
    if (preg_match($PREFIX_REGEX, $seg0) && array_key_exists($seg0, $SUPPORTED_LANGS)) {
        $path_has_lang = true;
        $lang_from_path = $seg0;
    }
}

// 3) Cookie
$lang_from_cookie = null;
if (empty($requested_lang) && isset($_COOKIE[$COOKIE_NAME])) {
    $c = strtolower(substr(trim($_COOKIE[$COOKIE_NAME]), 0, 5));
    if (array_key_exists($c, $SUPPORTED_LANGS)) $lang_from_cookie = $c;
}

// 4) Accept-Language negotiation (best-match)
function negotiate_accept_language($supported, $accept_header) {
    if (empty($accept_header)) return null;
    // Parse header "en-US,en;q=0.9,fr;q=0.8"
    $langs = array();
    foreach (explode(',', $accept_header) as $part) {
        $p = trim($part);
        $q = 1.0;
        if (strpos($p, ';q=') !== false) {
            list($pval, $qval) = explode(';q=', $p, 2);
            $p = trim($pval);
            $q = floatval($qval);
        }
        // Use primary tag only (e.g., "en-US" -> "en")
        $primary = strtolower(explode('-', $p)[0]);
        if (!isset($langs[$primary]) || $langs[$primary] < $q) {
            $langs[$primary] = $q;
        }
    }
    // sort by q descending
    arsort($langs);
    foreach ($langs as $candidate => $q) {
        if (array_key_exists($candidate, $supported)) return $candidate;
    }
    return null;
}
$lang_from_accept = null;
if (empty($requested_lang) && empty($lang_from_cookie) && !$path_has_lang) {
    $hdr = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $lang_from_accept = negotiate_accept_language($SUPPORTED_LANGS, $hdr);
}

// Decision order: explicit request -> path -> cookie -> accept -> default
$lang = $DEFAULT_LANG;
if (!empty($requested_lang)) {
    $lang = $requested_lang;
} elseif ($path_has_lang && !empty($lang_from_path)) {
    $lang = $lang_from_path;
} elseif (!empty($lang_from_cookie)) {
    $lang = $lang_from_cookie;
} elseif (!empty($lang_from_accept)) {
    $lang = $lang_from_accept;
}

// Persist cookie when language explicitly chosen or different from cookie
$should_set_cookie = true;
if (isset($_COOKIE[$COOKIE_NAME]) && $_COOKIE[$COOKIE_NAME] === $lang) {
    $should_set_cookie = false;
}
if ($should_set_cookie) {
    // Secure flag for HTTPS, HttpOnly true
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443;
    setcookie($COOKIE_NAME, $lang, time() + $COOKIE_TTL, '/', '', $secure, true);
}

// Always send language headers
send_language_headers($lang);

// Define a constant for use in pages
if (!defined('SITE_LANG')) define('SITE_LANG', $lang);

// If this script was called to "change language", redirect back
// Accepts "return" parameter or uses Referer or root
if (!empty($requested_lang)) {
    $return = '/';
    if (!empty($_REQUEST['return'])) {
        $return = $_REQUEST['return'];
    } elseif (!empty($_SERVER['HTTP_REFERER'])) {
        $return = $_SERVER['HTTP_REFERER'];
    } else {
        // default -> root with prefix if canonical desired
        $return = $CANONICAL_PREFIX ? "/$lang/" : '/';
    }
    // If return URL is same host + path without scheme host, keep as-is; if absolute to other host, just send user back there.
    // Ensure we don't open redirect to other domains unless referer or return param is absolute same host.
    $return_url = $return;
    // If return is an absolute URL on same host, allow it; else make relative
    $parsed = parse_url($return_url);
    if (!empty($parsed['host']) && $parsed['host'] !== $_SERVER['HTTP_HOST']) {
        // avoid redirecting to external by default; fallback to root
        $return_url = $CANONICAL_PREFIX ? "/$lang/" : '/';
    } elseif (!empty($parsed['path'])) {
        // keep path + query
        $return_url = $parsed['path'] . (!empty($parsed['query']) ? '?' . $parsed['query'] : '');
    }
    // If canonical prefix requested and the returned path doesn't start with /{lang}/, prefix it
    if ($CANONICAL_PREFIX) {
        $p = parse_url($return_url, PHP_URL_PATH) ?: '/';
        if ($p === '/') {
            $return_url = "/$lang/";
        } else {
            $pp = preg_replace('#/+#', '/', $p);
            $parts = explode('/', trim($pp, '/'));
            if (empty($parts[0]) || !array_key_exists($parts[0], $SUPPORTED_LANGS)) {
                // prefix
                $qs = parse_url($return_url, PHP_URL_QUERY);
                $return_url = "/$lang" . $pp . ($qs ? "?$qs" : '');
            }
        }
    }
    // Redirect
    header("Location: $return_url", true, 302);
    exit;
}

// Optionally enforce canonical prefix if enabled and current URL doesn't include it and the request is not for the language endpoint itself
if ($CANONICAL_PREFIX) {
    $is_language_endpoint = strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false;
    if (!$is_language_endpoint) {
        // If not prefixed, redirect to prefixed version
        if (!$path_has_lang) {
            $p = $uri_path;
            if ($p === '' || $p === '/') {
                $new = "/$lang/";
            } else {
                $new = "/$lang" . ($p === '/' ? '/' : $p);
            }
            // carry query string
            if (!empty($_SERVER['QUERY_STRING'])) {
                $new .= '?' . $_SERVER['QUERY_STRING'];
            }
            // Avoid infinite loop: only redirect when the new path differs
            if ($new !== $_SERVER['REQUEST_URI']) {
                header("Location: $new", true, 302);
                exit;
            }
        }
    }
}

// Provide small helper for generating language-prefixed URLs
function lang_url($path = '/') {
    $p = preg_replace('#/+#', '/', '/' . ltrim($path, '/'));
    return '/' . SITE_LANG . ($p === '/' ? '/' : $p);
}

// Provide helper to build switch language url keeping same page
function switch_language_url($target_lang) {
    $uri = parse_url($_SERVER['REQUEST_URI']);
    $path = $uri['path'] ?? '/';
    $segments = array_values(array_filter(explode('/', trim($path, '/'))));
    global $SUPPORTED_LANGS;
    if (!empty($segments) && array_key_exists($segments[0], $SUPPORTED_LANGS)) {
        array_shift($segments);
    }
    $new_path = '/' . $target_lang . '/' . implode('/', $segments);
    $new_path = preg_replace('#/+#', '/', $new_path);
    if (isset($uri['query']) && $uri['query'] !== '') $new_path .= '?' . $uri['query'];
    return $new_path;
}

// End of language.php
