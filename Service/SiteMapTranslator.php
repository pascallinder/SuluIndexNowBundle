<?php

namespace Linderp\SuluIndexNowBundle\Service;
class SiteMapTranslator
{
    public function translateUrls(string $sitemapUrl): array
    {
        // Load remote XML (with error handling)
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'SuluIndexNowBot/1.0'
            ]
        ]);

        $xmlContent = @file_get_contents($sitemapUrl, false, $context);

        $sitemap = simplexml_load_string($xmlContent);
        if(!$sitemap) return [];
        $urls = [];
        foreach ($sitemap->url as $entry) {
            $loc = (string) $entry->loc;
            if ($loc === '') {
                continue;
            }
            $urls[] = $loc;
        }
        return $urls;
    }
}
