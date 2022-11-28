<?php
//Autoload composer dependencies
include 'vendor/autoload.php';
require 'scrape.php';

use PHPUnit\Framework\TestCase;

class ScrapeTest extends TestCase{
    public function testResolvingHost(){
        //expected true when resolving URL is give as input.
        $this->assertContains(true, scrape('https://wltest.dns-systems.net/'));
        $this->assertContains(true, scrape('https://www.google.com/'));
    }

    public function testNonResolvingHost(){
        //expected false when wrong/non resolving URL is give as input.
        $this->assertContains(false, scrape('https://wlte.dns-systems.net'));
        $this->assertContains(false, scrape('https://www.gogle.com'));
    }

    public function testNoHTTPProtocol(){
        //expected false when HTTP/HTTPS not added in URL
        $this->assertContains(false, scrape('wltest.dns-systems.net'));
        $this->assertContains(false, scrape('www.google.com'));
    }
}