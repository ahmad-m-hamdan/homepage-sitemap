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

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Current plugin version
 * Versioning for this plugin abides by the semantics set by SemVer. Check https://semver.org 
 */
define('HOMEPAGE_SITEMAP_GENERATOR_VERSION', '1.0.0');

// Schedules the sitemap generation event every hour
function activate_homepage_sitemap_generation_event()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-homepage-sitemap-activator.php';
    Homepage_Sitemap_Activator::activate();
}

// Remove the scheduled event when the plugin is deactivated or your theme is deactivated
function deactivate_homepage_sitemap_generation_event()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-homepage-sitemap-deactivator.php';
    Homepage_Sitemap_Deactivator::deactivate();
}

add_action('homepage_sitemap_generation_event', 'crawl_and_store_links');

register_activation_hook(__FILE__, 'activate_homepage_sitemap_generation_event');
register_deactivation_hook(__FILE__, 'deactivate_homepage_sitemap_generation_event');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-homepage-sitemap.php';

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

    $plugin = new Homepage_Sitemap();
    $plugin->run();
}
run_homepage_sitemap();










// Register a menu item under "Tools"
function homepage_sitemap_add_menu()
{
    add_management_page(
        'Homepage Sitemap',   // Page title
        'Homepage Sitemap',   // Menu title
        'manage_options',     // Capability required to access
        'homepage-sitemap',   // Menu slug
        'homepage_sitemap_page' // Callback function to render the page
    );
}
add_action('admin_menu', 'homepage_sitemap_add_menu');

// Callback function to render the admin page
function homepage_sitemap_page()
{
?>
    <div class="wrap">
        <h2>Homepage Sitemap</h2>
        <button id="crawl-button" class="button">Crawl and Store Links</button>
        <button id="view-button" class="button">View Results</button>
        <div class="results"></div>
    </div>
<?php
}

function crawl_homepage_internal_links()
{
    $homepage_url = home_url(); // Get the homepage URL

    // Fetch the homepage content
    $homepage_content = file_get_contents($homepage_url);

    // Use regular expressions to extract internal links within <body> tag
    preg_match('/<body.*?>(.*?)<\/body>/s', $homepage_content, $body_matches);

    $body_content = $body_matches[1]; // Content within <body> tag

    // Use regular expressions to extract internal links
    preg_match_all('/<a\s+href=["\']([^"\']+)["\'][^>]*>/', $body_content, $matches);

    $internal_links = [];

    // Remove forward slash
    $matches[1] = array_map('remove_forward_slash', $matches[1]);

    // Remove duplicate URLs
    $matches[1] = array_unique($matches[1]);

    // Remove external and admin-based links
    $internal_links = array_filter($matches[1], 'filter_internal_links');

    // Store the internal links in wp_options, delete previous entry
    update_option('homepage_internal_links', serialize($internal_links));

    return $internal_links;
}

// Callback function to crawl homepage and store internal links
function crawl_and_store_links()
{
    // Fetch the homepage content
    $homepage_content = file_get_contents(home_url());

    $internal_links = crawl_homepage_internal_links();

    // Generate and replace sitemap
    create_sitemap_html_file($internal_links);

    // Generate and replace index.html
    create_homepage_html_file($homepage_content);

    wp_send_json(array_values($internal_links));
}

// Hook the AJAX actions
add_action('wp_ajax_crawl_store_links', 'crawl_and_store_links');
add_action('wp_ajax_get_results', 'get_results');

function filter_internal_links($link)
{
    $homepage_url = home_url(); // Get the homepage URL
    return (strpos($link, $homepage_url) !== false && strpos($link, '/wp-admin') === false) ? true : false;
}

function remove_forward_slash($link)
{
    return rtrim($link, '/');
}

function create_sitemap_html_file($sitemap_data)
{
    $sitemap_file = ABSPATH . 'sitemap.html'; // Path to the root directory's sitemap.html file

    // Check if the sitemap.html file exists in the root directory
    if (file_exists($sitemap_file)) {
        // Delete the existing sitemap.html file
        unlink($sitemap_file);
    }

    // Create and write the sitemap.html file
    $sitemap_content = '<!DOCTYPE html>';
    $sitemap_content .= '<html>';
    $sitemap_content .= '<head>';
    // Add any necessary head content here
    $sitemap_content .= '</head>';
    $sitemap_content .= '<body>';
    $sitemap_content .= '<h1>Sitemap</h1>';
    $sitemap_content .= '<ul>';

    foreach ($sitemap_data as $url) {
        $sitemap_content .= '<li><a href="' . esc_url($url) . '">' . esc_html($url) . '</a></li>';
    }

    $sitemap_content .= '</ul>';
    $sitemap_content .= '</body>';
    $sitemap_content .= '</html>';

    // Write the content to the sitemap.html file in the root directory
    file_put_contents($sitemap_file, $sitemap_content);
}

function create_homepage_html_file($homepage_content)
{
    $homepage_file = ABSPATH . 'index.html'; // Path to the root directory's index.html file

    // Check if the index.html file exists in the root directory
    if (file_exists($homepage_file)) {
        // Delete the existing index.html file
        unlink($homepage_file);
    }

    // Write the content to the index.html file in the root directory
    file_put_contents($homepage_file, $homepage_content);
}

// Add this to your plugin's main PHP file

// Function to retrieve and return results
function get_results()
{
    // Retrieve the results from the options table
    $results = get_option('homepage_internal_links'); // Replace with your option name
    $results = unserialize($results);

    // Send the results as a JSON response
    wp_send_json(array_values($results));
}
