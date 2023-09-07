# Homepage Sitemap Plugin - Technical Explanation

## Problem Statement

This plugin aims to give WordPress administrators with a tool to easily generate and maintain an up-to-date sitemap. The sitemap should by dynamic and lists all internal links found on the homepage, helping with SEO optimization and user navigation.

## Technical Specification

To address the problem at hand, we will create a plugin called "Homepage Sitemap Generator". The plugin will perform the following tasks:

1. Crawling the Homepage: This process can occur in three scenarios: on plugin activation, automatically every hour, or manually via the plugin's settings page. In essence, the crawl should comply with the following criteria:
   a. Extracts all internal hyperlinks found within the `<body>` tags, excluding links that are either external or related to a WordPress administrative page
   b. Filters duplicate entries making sure each link in the sitemap is unique
2. Storage: The discovered links will be stored in the WordPress database, particularly the `wp_options` table, for quick retrieval
3. Sitemap Generation: A sitemap.html file will be created and saved in the root directory of the WordPress installation. This file will represent the homepage's internal links in the structured format, and can be easily accessed by going to the `[YOUR_DOMAIN]/sitemap.html`
4. HTML Format for Homepage: In addition to the sitemap, the plugin should also create an `index.html` file located in the root directory as well
5. Admin Panel Integration: The plugin will integrate with the WordPress admin panel under the "Tools" menu. Users can initiate the crawl and generate the sitemap from there

## Technical Decisions

### 1. Language and Framework

The core language for this project is PHP, used in accordance with WordPress's fundamentals for custom plugin development and the PSR-12 format. In addition, we used JavaScript (jQuery) to handle AJAX requests initiated via the admin area.

### 2. Database Storage

We use WordPress's built-in database captabilities to temporarily store and retrieve the list of internal links.

### 3. AJAX for Asynchronous Crawling

To provide a seamless user experience, we use AJAX for asynchronous crawling, allowing users to trigger the crawl without page refresh.

### 4. Sitemap File Generation

We generate the sitemap.html file directly in the root directory of the WordPress installation to comply with SEO best practices. The aim is to make the sitemap as concise and clear as possible, it contains a simple description, the number of links present, and the list of URLs that the sitemap is composed of.

## Code Implementation

### 1. Plugin Package and Structure

Our plugin follows the package guidelines set by WP Media, a respected name in the WordPress community. Their package template (viewable here: [WP Media Package Template](https://github.com/wp-media/package-template)) offers a well-structured foundation for creating robust WordPress plugins.

### 2. WordPress Plugin Boilerplate

To ensure adherence to object-oriented programming principles and provide a structured and understandable codebase, we've based our plugin's file structure on the WordPress Plugin Boilerplate. This boilerplate, created by Devin Vinson, is a widely recognized framework for building high-quality WordPress plugins. You can explore the boilerplate here: [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).

### 3. Key Components and Directory Structure

Our plugin's directory structure is designed with clarity and maintainibility in mind:

- `\admin`: This directory handles all functionalities related to the admin area
- `\public`: This directory handles all functionalities facing the public
- `\includes`: The heart of the plugin resides here. It contains essential classes, including:
  - The `Crawler` class: responsible for crawing the homepage, storing and extracting internal links
  - The `Helper` class: This hosts miscellaneous functions that don't neatly fit into a specific class, offering a centralzied location for shared functionality
  - Activator and deactivator classes: to manage the plugin's activation and deactivation processes
  - The `Homepage_Sitemap_Loader` class: Handles the loading of hook dependencies, mainly filters and actions, ensuring seamless integration with WordPress core
  - The `Homepage_Sitemap` class: This acts as the core of the plugin. It specifies and orchestrates the necessary hooks and interactions, ensuring the smooth functioning of the sitemap generation process

## Achieving Desired Outcome

Our solution enables administraotrs to generate and maintain an accurate sitemap of their homepage with minimal effort. By automating the crawling process and providing an easy-to-access admin panel interface, the plugin aligns with the user story's goal of improving SEO and user navigation.
