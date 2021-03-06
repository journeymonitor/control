<?php

namespace ApiBundle\Service;

class HarTransformerService
{
    /**
     * @param \StdClass $originalHar
     * @return \Array|String Array of URLs that defines where to split the HAR into multiple pages
     */
    public function splitIntoMultiplePages(\stdClass $originalHar, $urls)
    {
        $urls = $this->normalizeUrls($urls);

        $pageStartedDateTimes = [];
        $pageStartedDateTimes[] = $originalHar->log->entries[0]->startedDateTime;
        $currentIndex = 0;

        foreach ($originalHar->log->entries as $entry) {
            foreach ($urls as $index => $url) {
                if ($index > $currentIndex && $url !== $urls[$currentIndex] && $entry->request->url === $url) {
                    $pageStartedDateTimes[] = $entry->startedDateTime;
                    $currentIndex = $index;
                }
            }
            $entry->pageref = 'Request '. ($currentIndex + 1) . ': ' . $urls[$currentIndex];
        }

        $pages = [];
        foreach ($pageStartedDateTimes as $index => $pageStartedDateTime) {
            $page = new \stdClass();
            $page->id = 'Request '. ($index + 1) . ': ' . $urls[$index];
            $page->startedDateTime = $pageStartedDateTime;
            $page->title = 'Request '. ($index + 1) . ': ' . $urls[$index];
            $pageTimings = new \stdClass();
            $pageTimings->comment = '';
            $page->pageTimings = $pageTimings;
            $page->comment = '';
            $pages[] = $page;
        }

        $originalHar->log->pages = $pages;
        return $originalHar;
    }

    private function normalizeUrls($urls)
    {
        $normalizedUrls = [];
        foreach ($urls as $url) {
            if (strstr($url, '#')) {
                $url = substr($url, 0, strpos($url, '#'));
            }
            $normalizedUrls[] = $url;
        }
        return $normalizedUrls;
    }
}
