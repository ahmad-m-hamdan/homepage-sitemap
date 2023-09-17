<?php

namespace HomepageSitemap\Includes;

use HomepageSitemap\Admin\AdminHandler;
use HomepageSitemap\FrontEnd\FrontEndHandler;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @package    HomepageSitemap
 * @subpackage Includes
 */
class Core
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('HOMEPAGE_SITEMAP_GENERATOR_VERSION')) {
            $this->version = HOMEPAGE_SITEMAP_GENERATOR_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'homepage-sitemap';

        $this->loadDependencies();
        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Homepage_Sitemap_Loader. Orchestrates the hooks of the plugin.
     * - Homepage_Sitemap_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies()
    {
        // /**
        //  * The class responsible for providing miscellaneous helper functions
        //  */
        // require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-helper.php';

        // /**
        //  * The class responsible for orchestrating the actions and filters of the
        //  * core plugin.
        //  */
        // require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-loader.php';

        // /**
        //  * The class responsible for defining all actions that occur in the admin area.
        //  */
        // require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin-handler.php';

        // /**
        //  * The class responsible for defining all actions that occur in the public-facing
        //  * side of the site.
        //  */
        // require_once plugin_dir_path(dirname(__FILE__)) . 'front-end/class-front-end-handler.php';

        // /**
        //  * The class responsible for defining all crawling actions.
        //  */
        // require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-crawler.php';

        $this->loader = new Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks()
    {

        $adminHandler = new AdminHandler($this->getPluginName(), $this->getVersion());
        $crawler = new Crawler();

        $this->loader->addAction('admin_enqueue_scripts', $adminHandler, 'enqueueStyles');
        $this->loader->addAction('admin_enqueue_scripts', $adminHandler, 'enqueueScripts');
        $this->loader->addAction('admin_menu', $adminHandler, 'addMenu');

        // Crawler AJAX actions
        $this->loader->addAction('homepage_sitemap_generation_event', $crawler, 'runAjax');
        $this->loader->addAction('wp_ajax_crawl_store_links', $crawler, 'runAjax');
        $this->loader->addAction('wp_ajax_get_results', $crawler, 'getStoredInternalLinksAjax');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks()
    {

        $frontEndHandler = new FrontEndHandler($this->getPluginName(), $this->getVersion());

        $this->loader->addAction('wp_enqueue_scripts', $frontEndHandler, 'enqueueStyles');
        $this->loader->addAction('wp_enqueue_scripts', $frontEndHandler, 'enqueueScripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }
}
