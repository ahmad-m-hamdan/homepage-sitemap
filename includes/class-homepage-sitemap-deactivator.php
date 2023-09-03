<?php

/**
 * Fired during plugin deactivation
 *
 * @since      1.0.0
 *
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */
class Homepage_Sitemap_Deactivator
{

    /**
     * Function to handle events that occur during plugin deactivation
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        wp_clear_scheduled_hook('homepage_sitemap_generation_event');
    }
}
