<?php

/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since 1.0.0
 * @package HOMEPAGE_SITEMAP
 *
 * Plugin Name: Homepage Sitemap Generator
 * Description: A plugin to generate and display a sitemap of the website's homepage.
 * Author: Ahmad Hamdan
 * Author URI: https://github.com/ahmad-m-hamdan/
 * Version: 1.0.0
 */

use HomepageSitemap\Includes\Core;
use HomepageSitemap\Includes\Activator;
use HomepageSitemap\Includes\Deactivator;

/**
 * Current plugin version
 * Versioning for this plugin abides by the semantics set by SemVer. Check https://semver.org
 */
define('HOMEPAGE_SITEMAP_GENERATOR_VERSION', '1.0.0');

// Schedules the sitemap generation event every hour
function activateHomepageSitemapGenerationEvent()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
    Activator::activate();
}

// Remove the scheduled event when the plugin is deactivated or your theme is deactivated
function deactivateHomepageSitemapGenerationEvent()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-deactivator.php';
    Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activateHomepageSitemapGenerationEvent');
register_deactivation_hook(__FILE__, 'deactivateHomepageSitemapGenerationEvent');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_homepage_sitemap()
{

    $plugin = new Core();
    $plugin->run();
}
run_homepage_sitemap();
