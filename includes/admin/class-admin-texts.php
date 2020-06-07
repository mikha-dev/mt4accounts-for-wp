<?php

/**
 * Class MT4_Accounts_Admin_Texts
 *
 * @ignore
 * @since 3.0
 */
class MT4_Accounts_Admin_Texts
{

    /**
     * @param string $plugin_file
     */
    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
    }

    /**
     * Add hooks
     */
    public function add_hooks()
    {
        global $pagenow;

        // Hooks for Plugins overview page
        if ($pagenow === 'plugins.php') {
            add_filter('plugin_action_links_' . $this->plugin_file, array( $this, 'add_plugin_settings_link' ), 10, 2);
            add_filter('plugin_row_meta', array( $this, 'add_plugin_meta_links'), 10, 2);
        }
    }


    /**
     * Add the settings link to the Plugins overview
     *
     * @param array $links
     * @param       $file
     *
     * @return array
     */
    public function add_plugin_settings_link($links, $file)
    {
        if ($file !== $this->plugin_file) {
            return $links;
        }

        $settings_link = '<a href="' . admin_url('admin.php?page=mt4accounts-for-wp') . '">'. __('Settings', 'mt4accounts-for-wp') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Adds meta links to the plugin in the WP Admin > Plugins screen
     *
     * @param array $links
     * @param string $file
     *
     * @return array
     */
    public function add_plugin_meta_links($links, $file)
    {
        if ($file !== $this->plugin_file) {
            return $links;
        }

        $links[] = 'http://dev4traders.com';

        /**
         * Filters meta links shown on the Plugins overview page
         *
         * This takes an array of strings
         *
         * @since 3.0
         * @param array $links
         * @ignore
         */
        $links = (array) apply_filters('mt4accounts_admin_plugin_meta_links', $links);

        return $links;
    }
}
