<?php

namespace ApiBundle\Service;

class SeleneseRunnerLogAnalyzerService
{
    /**
     * @param string $logContent
     * @return Array|String Array of URLs of the pages that were requested during the testcase run
     */
    public function getUrlsOfRequestedPages($logContent)
    {
        // [2015-05-28 23:46:34.933 +02:00] [INFO] - [Success] URL: [https://www.galeria-kaufhof.de/search?q=hose&page=4] / Title: [Suchergebnis f?r hose | GALERIA Kaufhof]
        $matches = [];
        $urls = [];
        $lines = explode("\n", $logContent);
        foreach ($lines as $line) {
            if (strstr($line, '] [INFO] - [Success] URL: [')) {
                preg_match('/\] \[INFO\] - \[Success\] URL: \[(.*?)\]/', $line, $matches);
                if (array_key_exists(1, $matches)) {
                    $urls[] = $matches[1];
                }
            }
        }
        return $urls;
    }
}
