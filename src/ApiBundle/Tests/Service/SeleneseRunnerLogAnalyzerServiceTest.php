<?php

namespace ApiBundle\Tests\Service;

use PHPUnit_Framework_TestCase;
use ApiBundle\Service\SeleneseRunnerLogAnalyzerService;

class SeleneseRunnerLogAnalyzerServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetUrlsOfRequestedPages()
    {
        $logContent = file_get_contents(__DIR__ . '/../fixtures/selenese-runner.log');

        $srlas = new SeleneseRunnerLogAnalyzerService();

        $actualListOfUrls = $srlas->getUrlsOfRequestedPages($logContent);
        $expectedListOfUrls = [
            0 => 'https://www.galeria-kaufhof.de/',
            1 => 'https://www.galeria-kaufhof.de/search?q=hose',
            2 => 'https://www.galeria-kaufhof.de/#foo'
        ];

        $this->assertEquals($expectedListOfUrls, $actualListOfUrls);
    }
}
