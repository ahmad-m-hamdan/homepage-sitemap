<?php

namespace HomepageSitemap\Includes;

use WP_Error;

/**
 * The Crawler class.
 *
 * This is used to define functionalities related to fetching and
 * storing crawled homepage data.
 *
 * @since      1.0.0
 *
 * @package    HomepageSitemap
 * @subpackage Includes
 */
class Crawler
{
    /**
     * The URL to crawl from
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $url    The URL to crawl from.
     */
    private $url;

    /**
     * The name of the CRON job related to the crawler
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $scheduled_event_name    The name of the event.
     */
    private $scheduled_event_name;

    public function __construct()
    {
        $this->url = home_url();
        $this->scheduled_event_name = 'homepage_sitemap_generation_event';
    }

    /**
     * Retrieve the crawled URL.
     *
     * @since     1.0.0
     * @return    string    The crawled URL.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Retrieve the scheduled event name.
     *
     * @since     1.0.0
     * @return    string    The event name.
     */
    public function getScheduledEventName()
    {
        return $this->scheduled_event_name;
    }

    /**
     * Checks if a link is internal and non-admin.
     *
     * @since     1.0.0
     * @return    boolean    True if internal, false otherwise.
     */
    public function isInternalLink($link)
    {
        $baseUrl = $this->getUrl();

        // Check if the link is an anchor, or absolute and starts with the base URL.
        if (strpos($link, '#') === 0 || strpos($link, $baseUrl) === 0) {
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
     * Store a URL's internal links.
     *
     * The function abides by the following criteria
     * - Handles links residing exclusively within the <body> tags
     * - Removes duplicate entries
     * - Disregards external and admin links
     * - Saves links in a serialized array in the options table
     *
     * @since    1.0.0
     * @access   public
     * @return   array    The list of internal links.
     */
    public function storeInternalLinks()
    {
        $helperObj = new Helper();

        // Use regular expressions to extract internal links within <body> tag
        preg_match('/<body.*?>(.*?)<\/body>/s', file_get_contents($this->getUrl()), $body_matches);

        $body_content = $body_matches[1]; // Content within <body> tag

        // Use regular expressions to extract internal links
        preg_match_all('/<a\s+href=["\']([^"\']+)["\'][^>]*>/', $body_content, $matches);

        // Remove anchor, external and admin-based links
        $internal_links = array_filter($matches[1], [$this, 'isInternalLink']);

        // Transform relative links into absolute
        $internal_links = array_map([$helperObj, 'linkAbsoluter'], $internal_links);

        // Remove forward slash
        $internal_links = array_map([$helperObj, 'removeForwardSlash'], $internal_links);

        // Remove duplicate URLs
        $internal_links = array_unique($internal_links);

        // Store the internal links in wp_options, delete previous entry
        update_option('homepage_internal_links', serialize($internal_links));
    }

    /**
     * Retrieves the internal links from the database.
     *
     * @since     1.0.0
     * @return    array    The list of internal links.
     */
    private function getStoredInternalLinks()
    {
        // Retrieve the results from the options table
        $results = get_option('homepage_internal_links'); // Replace with your option name
        $results = unserialize($results);

        // Amend the indexes of the array and return it
        return array_values($results);
    }

    /**
     * Similar to getStoredInternalLinks(), but more specific to AJAX requests
     *
     * @since     1.0.0
     * @return    string    The JSON string of stored internal links.
     */
    public function getStoredInternalLinksAjax()
    {
        $run = $this->getStoredInternalLinks();
        if (is_array($run)) {
            wp_send_json($run);
        } else {
            $error = new WP_Error(
                404,
                'List of links wasn\'t stored properly. Please generate a new crawl process.',
                ''
            );
            wp_send_json_error($error, 404);
        }
    }

    /**
     * Generates the sitemap HTML file.
     *
     * @since     1.0.0
=    */
    private function createSitemapHTMLFile()
    {
        $sitemap_file = ABSPATH . 'sitemap.html'; // Path to the root directory's sitemap.html file

        // Check if the sitemap.html file exists in the root directory
        if (file_exists($sitemap_file)) {
            // Delete the existing sitemap.html file
            unlink($sitemap_file);
        }

        $internal_links = $this->getStoredInternalLinks();

        // Create and write the sitemap.html file
        $sitemap_content = '<!DOCTYPE html>';
        $sitemap_content .= '<html>';
        $sitemap_content .= '<head>';
        $sitemap_content .= '<title>Homepage Sitemap</title>';
        $sitemap_content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>';
        $sitemap_content .= '<style type="text/css">';
        $sitemap_content .= 'body {font-family: Helvetica, Arial, sans-serif; font-size: 13px; color: #545353;}';
        $sitemap_content .= '#content {margin: 0 auto; width: 1000px;}';
        $sitemap_content .= '#sitemap {border: none; border-collapse: collapse;}';
        $sitemap_content .= '#sitemap tr:nth-child(odd) td {background-color: #eee !important;}';
        $sitemap_content .= '#sitemap tbody tr:hover td {background-color: #ccc;}';
        $sitemap_content .= '#sitemap tbody tr:hover td, #sitemap tbody tr:hover td a {color: #000;}';
        $sitemap_content .= '#sitemap th {text-align:left;}';
        $sitemap_content .= 'thead th {border-bottom: 1px solid #000;}';
        $sitemap_content .= 'a {color: #000; text-decoration: none;}';
        $sitemap_content .= 'a:visited {color: #777;}';
        $sitemap_content .= 'a:hover {text-decoration: underline;}';
        $sitemap_content .= '#sitemap tbody tr:hover td, #sitemap tbody tr:hover td a {color: #000;}';
        $sitemap_content .= '</style>';
        $sitemap_content .= '</head>';
        $sitemap_content .= '<body>';
        $sitemap_content .= '<div id="content">';
        $sitemap_content .= '<h1>Homepage Sitemap</h1>';
        $sitemap_content .= '<p>This sitemap is generated by the Homepage Sitemap Generator plugin</p>';
        $sitemap_content .= '<p>This sitemap contains ' . count($internal_links) . '</p>';
        $sitemap_content .= '<table id="sitemap">';
        $sitemap_content .= '<thead>';
        $sitemap_content .= '<tr>';
        $sitemap_content .= '<th>Sitemap</th>';
        $sitemap_content .= '</tr>';
        $sitemap_content .= '</thead>';
        $sitemap_content .= '</div>';
        $sitemap_content .= '<tbody>';

        foreach ($internal_links as $url) {
            $sitemap_content .= '<tr><td><a href="' . esc_url($url) . '">' . esc_html($url) . '</a></td></tr>';
        }

        $sitemap_content .= '</tbody>';
        $sitemap_content .= '</body>';
        $sitemap_content .= '</html>';

        // Write the content to the sitemap.html file in the root directory
        file_put_contents($sitemap_file, $sitemap_content);
    }

    /**
     * Fetches the homepage's HTML content and saves it in an
     * index.html file located as the root directory
     *
     * @since     1.0.0
     */
    private function generateHomepageHTMLFile()
    {
        $homepage_content = file_get_contents($this->getUrl());
        $index_file = ABSPATH . 'index.html'; // Path to the root directory's index.html file

        // Check if the index.html file exists in the root directory
        if (file_exists($index_file)) {
            // Delete the existing index.html file
            unlink($index_file);
        }

        // Write the content to the index.html file in the root directory
        file_put_contents($index_file, $homepage_content);
    }

    /**
     * Resets the crawl process timer.
     *
     * @since     1.0.0
=    */
    private function resetCrawlEventTimer()
    {
        $event_name = $this->getScheduledEventName();
        // Cancel the existing scheduled event
        wp_clear_scheduled_hook($event_name);
        // Reschedule the event to run in an hour
        wp_schedule_event(time() + 3600, 'hourly', $event_name);
    }

    /**
     * Runs the crawl process.
     *
     * @since     1.0.0
     * @return    array    The updated list of internal links.
=    */
    public function run()
    {
        $this->resetCrawlEventTimer();
        $this->storeInternalLinks();
        $this->createSitemapHTMLFile();
        $this->generateHomepageHTMLFile();

        return $this->getStoredInternalLinks();
    }

    /**
     * Similar to run(), but more specific to AJAX requests
     *
     * @since     1.0.0
     * @return    string    The JSON string of updated internal links.
     */
    public function runAjax()
    {
        $run = $this->run();
        if (is_array($run)) {
            wp_send_json($run);
        } else {
            $error = new WP_Error(
                404,
                'List of internal links wasn\'t stored properly. Please try again later.',
                ''
            );
            wp_send_json_error($error, 404);
        }
    }
}
