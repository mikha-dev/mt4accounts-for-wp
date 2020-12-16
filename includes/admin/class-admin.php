<?php

/**
* Class MT4_Accounts_Admin
*
* @ignore
* @access private
*/
class MT4_Accounts_Admin
{

    /**
    * @var string The relative path to the main plugin file from the plugins dir
    */
    protected $plugin_file;

    /**
    * @var MT4_Accounts_Admin_Messages
    */
    protected $messages;

    /**
    * @var MT4_Accounts_Admin_Tools
    */
    protected $tools;

    /**
    * Constructor
    *
    * @param MT4_Accounts_Admin_Tools $tools
    * @param MT4_Accounts_Admin_Messages $messages
    */
    public function __construct(MT4_Accounts_Admin_Tools $tools, MT4_Accounts_Admin_Messages $messages)
    {
        $this->tools = $tools;
        $this->messages = $messages;
        $this->plugin_file = plugin_basename(MT4_ACCOUNTS_PLUGIN_FILE);
    }

    /**
    * Registers all hooks
    */
    public function add_hooks()
    {

        // Actions used globally throughout WP Admin
        add_action('admin_menu', array( $this, 'build_menu' ));
        add_action('admin_init', array( $this, 'initialize' ));

        add_action('current_screen', array( $this, 'customize_admin_texts' ));
        add_action('wp_dashboard_setup', array( $this, 'register_dashboard_widgets' ));
        add_action('mt4accounts_admin_empty_debug_log', array( $this, 'empty_debug_log' ));

        add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ));

        $this->messages->add_hooks();
    }

    /**
    * Initializes various stuff used in WP Admin
    *
    * - Registers settings
    */
    public function initialize()
    {

        // register settings
        register_setting('mt4accounts_settings', 'mt4accounts', array( $this, 'save_general_settings' ));

        // Load upgrader
        $this->init_upgrade_routines();

        // listen for custom actions
        $this->listen_for_actions();
    }


    /**
    * Listen for `_mt4accounts_action` requests
    */
    public function listen_for_actions()
    {

        // listen for any action (if user is authorised)
        if (! $this->tools->is_user_authorized() || ! isset($_REQUEST['_mt4accounts_action'])) {
            return false;
        }

        $action = (string) $_REQUEST['_mt4accounts_action'];

        /**
        * Allows you to hook into requests containing `_mt4accounts_action` => action name.
        *
        * The dynamic portion of the hook name, `$action`, refers to the action name.
        *
        * By the time this hook is fired, the user is already authorized. After processing all the registered hooks,
        * the request is redirected back to the referring URL.
        *
        * @since 3.0
        */
        do_action('mt4accounts_admin_' . $action);

        // redirect back to where we came from
        $redirect_url = ! empty($_POST['_redirect_to']) ? $_POST['_redirect_to'] : remove_query_arg('_mt4accounts_action');
        wp_redirect($redirect_url);
        exit;
    }

    /**
    * Register dashboard widgets
    */
    public function register_dashboard_widgets()
    {
        if (! $this->tools->is_user_authorized()) {
            return false;
        }

        /**
        * Setup dashboard widget, users are authorized by now.
        *
        * Use this hook to register your own dashboard widgets for users with the required capability.
        *
        * @since 3.0
        * @ignore
        */
        do_action('mt4accounts_dashboard_setup');

        return true;
    }

    /**
    * Upgrade routine
    */
    private function init_upgrade_routines()
    {

        // upgrade routine for upgrade routine....
        $previous_version = get_option('mt4accounts_lite_version', 0);
        if ($previous_version) {
            delete_option('mt4accounts_lite_version');
            update_option('mt4accounts_version', $previous_version);
        }

        $previous_version = get_option('mt4accounts_version', 0);

        // allow setting migration version from URL, to easily re-run previous migrations.
        if (isset($_GET['mt4accounts_run_migration'])) {
            $previous_version = $_GET['mt4accounts_run_migration'];
        }

        // Ran upgrade routines before?
        if (empty($previous_version)) {
            update_option('mt4accounts_version', MT4_ACCOUNTS_VERSION);

            $previous_version = '3.9';
        }

        // Rollback'ed?
        if (version_compare($previous_version, MT4_ACCOUNTS_VERSION, '>')) {
            update_option('mt4accounts_version', MT4_ACCOUNTS_VERSION);
            return false;
        }

        // This means we're good!
        if (version_compare($previous_version, MT4_ACCOUNTS_VERSION) > -1) {
            return false;
        }

        define('MT4_ACCOUNTS_DOING_UPGRADE', true);
        $upgrade_routines = new MT4_ACCOUNTS_Upgrade_Routines($previous_version, MT4_ACCOUNTS_VERSION, dirname(__FILE__) . '/migrations');
        $upgrade_routines->run();
        update_option('mt4accounts_version', MT4_ACCOUNTS_VERSION);
    }

    /**
    * Load the plugin translations
    */
    private function load_translations()
    {
        // load the plugin text domain
        load_plugin_textdomain('mt4accounts-for-wp', false, dirname($this->plugin_file) . '/languages');
    }

    /**
    * Customize texts throughout WP Admin
    */
    public function customize_admin_texts()
    {
        $texts = new MT4_Accounts_Admin_Texts($this->plugin_file);
        $texts->add_hooks();
    }

    /**
    * Validates the General settings
    * @param array $settings
    * @return array
    */
    public function save_general_settings(array $settings)
    {
        $current = mt4accounts_get_options();

        // merge with current settings to allow passing partial arrays to this method
        $settings = array_merge($current, $settings);

        // toggle usage tracking
        if ($settings['allow_usage_tracking'] !== $current['allow_usage_tracking']) {
            MT4_ACCOUNTS_Usage_Tracking::instance()->toggle($settings['allow_usage_tracking']);
        }

        // Make sure not to use obfuscated key
        if (strpos($settings['api_key'], '*') !== false) {
            $settings['api_key'] = $current['api_key'];
        }

        // Sanitize API key
        $settings['api_key'] = sanitize_text_field($settings['api_key']);

        /**
        * Runs right before general settings are saved.
        *
        * @param array $settings The updated settings array
        * @param array $current The old settings array
        */
        do_action('mt4accounts_save_settings', $settings, $current);

        return $settings;
    }

    /**
    * Load scripts and stylesheet on Mailchimp for WP Admin pages
    *
    * @return bool
    */
    public function enqueue_assets()
    {
        return true;
    }

    /**
    * Register the setting pages and their menu items
    */
    public function build_menu()
    {
        $required_cap = $this->tools->get_required_capability();

        $menu_items = array(
            array(
                'title' => __('MT4 Accounts Settings', 'mt4accounts-for-wp'),
                'text' => __('MT4 Accounts', 'mt4accounts-for-wp'),
                'slug' => '',
                'callback' => array( $this, 'show_generals_setting_page' ),
                'position' => 0
            ),
            array(
                'title' => __('Other Settings', 'mt4accounts-for-wp'),
                'text' => __('Other', 'mt4accounts-for-wp'),
                'slug' => 'other',
                'callback' => array( $this, 'show_other_setting_page' ),
                'position' => 90
            ),

        );

        /**
        * Filters the menu items to appear under the main menu item.
        *
        * To add your own item, add an associative array in the following format.
        *
        * $menu_items[] = array(
        *     'title' => 'Page title',
        *     'text'  => 'Menu text',
        *     'slug' => 'Page slug',
        *     'callback' => 'my_page_function',
        *     'position' => 50
        * );
        *
        * @param array $menu_items
        * @since 3.0
        */
        $menu_items = (array) apply_filters('mt4accounts_admin_menu_items', $menu_items);

        // add top menu item
        add_menu_page('MT4Accounts for WP', 'MT4 Accounts', $required_cap, 'mt4accounts-for-wp', array( $this, 'show_generals_setting_page' ), MT4_ACCOUNTS_PLUGIN_URL . 'assets/img/icon.png', '99.68491');

        // sort submenu items by 'position'
        usort($menu_items, array( $this, 'sort_menu_items_by_position' ));

        // add sub-menu items
        foreach ($menu_items as $item) {
            $this->add_menu_item($item);
        }
    }

    /**
    * @param array $item
    */
    public function add_menu_item(array $item)
    {

        // generate menu slug
        $slug = 'mt4accounts-for-wp';
        if (! empty($item['slug'])) {
            $slug .= '-' . $item['slug'];
        }

        // provide some defaults
        $parent_slug = ! empty($item['parent_slug']) ? $item['parent_slug'] : 'mt4accounts-for-wp';
        $capability = ! empty($item['capability']) ? $item['capability'] : $this->tools->get_required_capability();

        // register page
        $hook = add_submenu_page($parent_slug, $item['title'] . ' - MT4Accounts for WordPress', $item['text'], $capability, $slug, $item['callback']);

        // register callback for loading this page, if given
        if (array_key_exists('load_callback', $item)) {
            add_action('load-' . $hook, $item['load_callback']);
        }
    }

    /**
    * Show the API Settings page
    */
    public function show_generals_setting_page()
    {
        $opts = mt4accounts_get_options();
        $trusted = false;

        try {
            $trusted = $this->get_api()->check_trusted();
        } catch (MT4_Accounts_Api_Connection_Exception $e) {
            $message = sprintf("<strong>%s</strong> %s %s ", __("Error connecting to API:", 'mt4accounts-for-wp'), $e->getCode(), $e->getMessage());

            if (is_object($e->data) && ! empty($e->data->ref_no)) {
                $message .= '<br />' . sprintf(__('Looks like your server is blocked by API\'s firewall. Please contact Mailchimp support and include the following reference number: %s', 'mt4accounts-for-wp'), $e->data->ref_no);
            }

            $this->messages->flash($message, 'error');
        } catch (MT4_Accounts_Api_Exception $e) {
            $this->messages->flash(sprintf("<strong>%s</strong><br /> %s", __("API returned the following error:", 'mt4accounts-for-wp'), $e), 'error');
        }

        $portfolios = array();
        if($trusted) {
            $portfolios = $this->get_api()->list_portfolios();
        }
        require MT4_ACCOUNTS_PLUGIN_DIR . 'includes/views/general-settings.php';
    }

    /**
    * Show the Other Settings page
    */
    public function show_other_setting_page()
    {
        $opts = mt4accounts_get_options();
        $log = $this->get_log();
        $log_reader = new MT4_Accounts_Debug_Log_Reader($log->file);
        require MT4_ACCOUNTS_PLUGIN_DIR . 'includes/views/other-settings.php';
    }

    /**
    * @param $a
    * @param $b
    *
    * @return int
    */
    public function sort_menu_items_by_position($a, $b)
    {
        $pos_a = isset($a['position']) ? $a['position'] : 80;
        $pos_b = isset($b['position']) ? $b['position'] : 90;
        return $pos_a < $pos_b ? -1 : 1;
    }

    /**
    * Empties the log file
    */
    public function empty_debug_log()
    {
        $log = $this->get_log();
        file_put_contents($log->file, '');

        $this->messages->flash(__('Log successfully emptied.', 'mt4accounts-for-wp'));
    }

    /**
    * @return MT4_Accounts_Debug_Log
    */
    protected function get_log()
    {
        return mt4accounts('log');
    }

    /**
    * @return MT4_Accounts_Api
    */
    protected function get_api()
    {
        return mt4accounts('api');
    }
}
