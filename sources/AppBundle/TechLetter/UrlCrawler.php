<?php

declare(strict_types=1);

namespace AppBundle\TechLetter;

class UrlCrawler
{
    public function crawlUrl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            throw new \RuntimeException(
                sprintf('Error when crawling the page: %s (%s)', curl_error($curl), curl_errno($curl))
            );
        }
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status !== 200) {
            throw new \RuntimeException(
                sprintf('The url responded with a wrong status code: %s', $status)
            );
        }
        return $output;
    }
}
