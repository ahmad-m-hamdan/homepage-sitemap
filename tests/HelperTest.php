<?php

namespace HomepageSitemap\Tests;

require_once __DIR__ . '/../autoload.php';

use HomepageSitemap\Includes\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testRemoveForwardSlash()
    {
        $input = 'https://example.com/some/path/';
        $output = 'https://example.com/some/path';

        $helper = new Helper();
        $result = $helper->removeForwardSlash($input);

        $this->assertSame($output, $result);
    }

    public function testRemoveForwardSlashForRoot()
    {
        $input = 'https://example.com/';
        $output = 'https://example.com';

        $helper = new Helper();
        $result = $helper->removeForwardSlash($input);

        $this->assertSame($output, $result);
    }

    public function testRemoveForwardSlashForEmpty()
    {
        $input = '';
        $output = '';

        $helper = new Helper();
        $result = $helper->removeForwardSlash($input);

        $this->assertSame($output, $result);
    }
}
