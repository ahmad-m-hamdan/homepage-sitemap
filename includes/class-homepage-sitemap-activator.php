<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */
class Homepage_Sitemap_Activator
{

    /**
     * Function to handle events that occur during plugin activation
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        if (!wp_next_scheduled('homepage_sitemap_generation_event')) {
            wp_schedule_event(current_time('timestamp'), 'hourly', 'homepage_sitemap_generation_event');
        }
    }
}
