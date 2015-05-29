<?php

namespace AppBundle\Tests\Service;

use PHPUnit_Framework_TestCase;
use AppBundle\Service\SeleneseRunnerLogAnalyzer;

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
            2 => 'https://www.galeria-kaufhof.de/search?q=hose&page=4'
        ];

        $this->assertEquals($expectedListOfUrls, $actualListOfUrls);
    }
}
