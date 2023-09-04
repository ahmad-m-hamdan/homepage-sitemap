<?php

/**
 * The file that defines the Helper class
 *
 * @since      1.0.0
 *
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */

/**
 * The Crawler class.
 *
 * This is used to define miscellaneous functions that
 * might be needed in other classes
 *
 * @since      1.0.0
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */
class Helper
{
    /**
     * Removes trailing forward slash from a string
     *
     * @since     1.0.0
     * @return    string    The trimmed string.
     */
    public function remove_forward_slash($link)
    {
        return rtrim($link, '/');
    }
}
