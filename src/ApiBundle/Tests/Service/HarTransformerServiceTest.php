<?php

namespace ApiBundle\Tests\Service;

use PHPUnit_Framework_TestCase;
use ApiBundle\Service\HarTransformerService;

class HarTransformerServiceTest extends PHPUnit_Framework_TestCase
{
    public function testSplitIntoMultiplePages()
    {
        $originalHar = file_get_contents(__DIR__ . '/../fixtures/testrun.actual.har.json');
        $originalHarObject = json_decode($originalHar);

        $transformedHar = file_get_contents(__DIR__ . '/../fixtures/testrun.expected.har.json');
        $transformedHarObject = json_decode($transformedHar);

        $hts = new HarTransformerService();

        $urls = [
            0 => 'https://www.galeria-kaufhof.de/',
            1 => 'https://www.galeria-kaufhof.de/search?q=hose',
            2 => 'https://www.galeria-kaufhof.de/',
        ];

        $this->assertEquals($transformedHarObject, $hts->splitIntoMultiplePages($originalHarObject, $urls));
    }
}
