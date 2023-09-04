<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Homepage_Sitemap
 * @subpackage Homepage_Sitemap/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Homepage_Sitemap
 * @subpackage Homepage_Sitemap/admin
 */
class Homepage_Sitemap_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/homepage-sitemap-admin.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/homepage-sitemap-admin.js', ['jquery'], $this->version, false);

        // Pass JS parameters
        wp_localize_script($this->plugin_name, 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    /**
     * Register the plugin's menu item under "Tools"
     * 
     * @since 1.0.0
     */
    public function homepage_sitemap_add_menu()
    {
        add_management_page(
            'Homepage Sitemap Generator',   // Page title
            'Homepage Sitemap',   // Menu title
            'manage_options',     // Capability required to access
            'homepage-sitemap',   // Menu slug
            [$this, 'display_admin_page'] // Callback function to render the page
        );
    }

    // Callback function to render the admin page
    public function display_admin_page()
    {
?>
        <div class="wrap">
            <h2>Homepage Sitemap Generator</h2>
            <button id="crawl-button" class="button">Crawl and Store Links</button>
            <button id="view-button" class="button">View Stored Results</button>
            <div class="results"></div>
        </div>
<?php
    }
}
