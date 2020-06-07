<?php

/**
 * Get a service by its name
 *
 * _Example:_
 *
 * $forms = mt4accounts('forms');
 * $api = mt4accounts('api');
 *
 * When no service parameter is given, the entire container will be returned.
 *
 * @ignore
 * @access private
 *
 * @param string $service (optional)
 * @return mixed
 *
 * @throws Exception when service is not found
 */
function mt4accounts($service = null)
{
    static $mt4accounts;

    if (!$mt4accounts) {
        $mt4accounts = new MT4_Accounts_Container();
    }

    if ($service) {
        return $mt4accounts->get($service);
    }

    return $mt4accounts;
}

/**
 * Gets the Mailchimp for WP options from the database
 * Uses default values to prevent undefined index notices.
 *
 * @since 1.0
 * @access public
 * @static array $options
 * @return array
 */
function mt4accounts_get_options()
{
    $defaults = require MT4_ACCOUNTS_PLUGIN_DIR . 'config/default-settings.php';
    $options = (array) get_option('mt4accounts', array());
    $options = array_merge($defaults, $options);

    /**
     * Filters the Mailchimp for WordPress settings (general).
     *
     * @param array $options
     */
    return apply_filters('mt4accounts_settings', $options);
}

/**
 * @return array
 */
function mt4accounts_get_settings() {
    return mt4accounts_get_options();
}

function mt4accounts_get_api_key()
{
    // try to get from constant
    if (defined('MT4_ACCOUNTS_API_KEY') && constant('MT4_ACCOUNTS_API_KEY') !== '') {
        return MT4_ACCOUNTS_API_KEY;
    }

    // get from options
    $opts = mt4accounts_get_options();
    return $opts['api_key'];
}

function mt4accounts_get_api_url()
{
    // try to get from constant
    if (defined('MT4_ACCOUNTS_API_URL') && constant('MT4_ACCOUNTS_API_URL') !== '') {
        return MT4_ACCOUNTS_API_URL;
    }

    // get from options
    $opts = mt4accounts_get_options();
    return $opts['api_url'];
}

/**
 * Gets the Mailchimp for WP API class (v3) and injects it with the API key
 *
 * @since 4.0
 * @access public
 *
 * @return MC4WP_API_v3
 */
function mt4accounts_get_api()
{
    $api_key = mt4accounts_get_api_key();
    $api_url = mt4accounts_get_api_url();
    $instance = new MT4_Accounts_API($api_key, $api_url);
    return $instance;
}


/**
 * Creates a new instance of the Debug Log
 *
 * @return MC4WP_Debug_Log
 */
function mt4accounts_get_debug_log()
{
    $opts = mt4accounts_get_options();

    // get default log file location
    $upload_dir = wp_upload_dir(null, false);
    $file = trailingslashit($upload_dir['basedir']) . 'mt4accounts-debug-log.php';

    /**
     * Filters the log file to write to.
     *
     * @param string $file The log file location. Default: /wp-content/uploads/mt4wp-debug.log
     */
    $file = apply_filters('mt4accounts_debug_log_file', $file);

    /**
     * Filters the minimum level to log messages.
     *
     * @see MC4WP_Debug_Log
     *
     * @param string|int $level The minimum level of messages which should be logged.
     */
    $level = apply_filters('mt4accounts_debug_log_level', $opts['debug_log_level']);

    return new MT4_Accounts_Debug_Log($file, $level);
}


/**
 * Get current URL (full)
 *
 * @return string
 */
function mt4accounts_get_request_url()
{
    global $wp;

    // get requested url from global $wp object
    $site_request_uri = $wp->request;

    // fix for IIS servers using index.php in the URL
    if (false !== stripos($_SERVER['REQUEST_URI'], '/index.php/' . $site_request_uri)) {
        $site_request_uri = 'index.php/' . $site_request_uri;
    }

    // concatenate request url to home url
    $url = home_url($site_request_uri);
    $url = trailingslashit($url);

    return esc_url($url);
}

/**
 * Get current URL path.
 *
 * @return string
 */
function mt4accounts_get_request_path()
{
    return $_SERVER['REQUEST_URI'];
}

/**
* Get IP address for client making current request
*
* @return string
*/
function mt4accounts_get_request_ip_address()
{
    $headers = (function_exists('apache_request_headers')) ? apache_request_headers() : $_SERVER;

    if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $headers['X-Forwarded-For'];
    }

    if (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $headers['HTTP_X_FORWARDED_FOR'];
    }

    return $_SERVER['REMOTE_ADDR'];
}

function mt4accounts_use_sslverify()
{

    // Disable for all transports other than CURL
    if (!function_exists('curl_version')) {
        return false;
    }

    $curl = curl_version();

    // Disable if OpenSSL is not installed
    if (empty($curl['ssl_version'])) {
        return false;
    }

    // Disable if on WP 4.4, see https://core.trac.wordpress.org/ticket/34935
    if ($GLOBALS['wp_version'] === '4.4') {
        return false;
    }

    return true;
}