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
     * @return    string    The trimmed string.
     */
    public function removeForwardSlash($link)
    {
        return rtrim($link, '/');
    }
}
