<?php

/**
 * The file that defines the Crawler class
 *
 * @since      1.0.0
 *
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
 */

/**
 * The Crawler class.
 *
 * This is used to define functionalities related to fetching and
 * storing crawled homepage data.
 *
 * @since      1.0.0
 * @package    HOMEPAGE_SITEMAP
 * @subpackage HOMEPAGE_SITEMAP/includes
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
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Retrieve the scheduled event name.
     *
     * @since     1.0.0
     * @return    string    The event name.
     */
    public function get_scheduled_event_name()
    {
        return $this->scheduled_event_name;
    }

    /**
     * Checks if a link is internal and non-admin.
     *
     * @since     1.0.0
     * @return    boolean    True if internal, false otherwise.
     */
    public function is_internal_link($link)
    {
        return (strpos($link, $this->get_url()) !== false && strpos($link, '/wp-admin') === false) ? true : false;
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
    public function store_internal_links()
    {
        // Use regular expressions to extract internal links within <body> tag
        preg_match('/<body.*?>(.*?)<\/body>/s', file_get_contents($this->get_url()), $body_matches);

        $body_content = $body_matches[1]; // Content within <body> tag

        // Use regular expressions to extract internal links
        preg_match_all('/<a\s+href=["\']([^"\']+)["\'][^>]*>/', $body_content, $matches);

        // Remove forward slash
        $matches[1] = array_map(['Helper', 'remove_forward_slash'], $matches[1]);

        // Remove duplicate URLs
        $matches[1] = array_unique($matches[1]);

        // Remove external and admin-based links
        $internal_links = array_filter($matches[1], [$this, 'is_internal_link']);

        // Store the internal links in wp_options, delete previous entry
        update_option('homepage_internal_links', serialize($internal_links));
    }

    /**
     * Retrieves the internal links from the database.
     *
     * @since     1.0.0
     * @return    array    The list of internal links.
     */
    private function get_stored_internal_links()
    {
        // Retrieve the results from the options table
        $results = get_option('homepage_internal_links'); // Replace with your option name
        $results = unserialize($results);

        // Amend the indexes of the array and return it
        return array_values($results);
    }

    /**
     * Similar to get_stored_internal_links(), but more specific to AJAX requests
     * 
     * @since     1.0.0
     * @return    string    The JSON string of stored internal links.
     */
    public function get_stored_internal_links_ajax()
    {
        $run = $this->get_stored_internal_links();
        if (is_array($run)) {
            wp_send_json($run);
        } else {
            $error = new WP_Error(404, 'List of internal links is either missing from the database or wasn\'t stored properly. Please try to generate a new crawl process.', '');
            wp_send_json_error($error, 404);
        }
    }

    /**
     * Generates the sitemap HTML file.
     *
     * @since     1.0.0
=    */
    private function create_sitemap_html_file()
    {
        $sitemap_file = ABSPATH . 'sitemap.html'; // Path to the root directory's sitemap.html file

        // Check if the sitemap.html file exists in the root directory
        if (file_exists($sitemap_file)) {
            // Delete the existing sitemap.html file
            unlink($sitemap_file);
        }

        $internal_links = $this->get_stored_internal_links();

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
        $sitemap_content .= '<p>Generated by Homepage Sitemap Generator plugin. This sitemap is meant to be used by Search Engines</p>';
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
    private function generate_homepage_html_file()
    {
        $homepage_content = file_get_contents($this->get_url());
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
    private function reset_crawl_event_timer()
    {
        $event_name = $this->get_scheduled_event_name();
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
        $this->reset_crawl_event_timer();
        $this->store_internal_links();
        $this->create_sitemap_html_file();
        $this->generate_homepage_html_file();

        return $this->get_stored_internal_links();
    }

    /**
     * Similar to run(), but more specific to AJAX requests
     * 
     * @since     1.0.0
     * @return    string    The JSON string of updated internal links.
     */
    public function run_ajax()
    {
        $run = $this->run();
        if (is_array($run)) {
            wp_send_json($run);
        } else {
            $error = new WP_Error(404, 'List of internal links is either missing from the database or wasn\'t stored properly. Please try again later.', '');
            wp_send_json_error($error, 404);
        }
    }
}
