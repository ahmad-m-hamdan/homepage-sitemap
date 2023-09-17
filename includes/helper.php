<?php

namespace HomepageSitemap\Includes;

/**
 * The Helper class.
 *
 * This is used to define miscellaneous functions that
 * might be needed in other classes
 *
 * @since      1.0.0
 *
 * @package    HomepageSitemap
 * @subpackage Includes
 */
class Helper
{
    /**
     * Removes trailing forward slash from a string
     *
     * @since     1.0.0
     *
     * @param     string    $link
     * @return    string    The trimmed string.
     */
    public function removeForwardSlash($link)
    {
        return rtrim($link, '/');
    }

    /**
     * Checks if a link is internal and non-admin.
     *
     * @since     1.0.0
     *
     * @param     string     $url
     * @param     string     $link
     * @return    boolean    True if internal, false otherwise.
     */
    public function isInternalLink($url, $link)
    {
        // Check if the link is an anchor, or absolute and starts with the base URL.
        if (strpos($link, '#') === 0 || strpos($link, $url) === 0) {
            return true;
        }

        // Check if the link is relative and doesn't point to /wp-admin.
        if (strpos($link, '/') === 0 && strpos($link, '/wp-admin') !== 0) {
            return true;
        }

        // If neither condition is met, it's not an internal link.
        return false;
    }

    /**
     * Converts relative URLs into absolute ones
     *
     * @since     1.0.0
     *
     * @param     string    $link
     * @return    string    The absolute version of a link
     */
    public function linkAbsoluter($link)
    {
        // Check if the link is already an absolute URL.
        if (filter_var($link, FILTER_VALIDATE_URL)) {
            return $link;
        }

        // If it's a relative URL, create the absolute URL.
        return get_permalink(get_page_by_path($link));
    }
}
