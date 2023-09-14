<?php

namespace HomepageSitemap\Tests;

use HomepageSitemap\Includes\Crawler;
use PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{
    public function testFetchInternalLinks()
    {
        $crawler = new Crawler();
    }
}
