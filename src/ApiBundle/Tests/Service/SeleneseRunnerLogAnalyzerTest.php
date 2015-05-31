<?php

namespace ApiBundle\Tests\Service;

use PHPUnit_Framework_TestCase;
use ApiBundle\Service\SeleneseRunnerLogAnalyzer;

class SeleneseRunnerLogAnalyzerTest extends PHPUnit_Framework_TestCase
{
    public function testGetUrlsOfRequestedPages()
    {
        $logContent = file_get_contents(__DIR__ . '/../fixtures/selenese-runner.log');

        $srla = new SeleneseRunnerLogAnalyzer();

        $actualListOfUrls = $srla->getUrlsOfRequestedPages($logContent);
        $expectedListOfUrls = [
            0 => 'https://www.galeria-kaufhof.de/',
            1 => 'https://www.galeria-kaufhof.de/search?q=hose',
            2 => 'https://www.galeria-kaufhof.de/'
        ];

        $this->assertEquals($expectedListOfUrls, $actualListOfUrls);
    }
}
