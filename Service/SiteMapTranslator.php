<?php

namespace Linderp\SuluIndexNowBundle\Service;
use Psr\Log\LoggerInterface;
class SiteMapTranslator
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @return array<int, string>
     */
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
        if ($xmlContent === false) {
            $this->logger->warning('IndexNow sitemap fetch failed', [
                'sitemapUrl' => $sitemapUrl,
            ]);
            return [];
        }

        $sitemap = simplexml_load_string($xmlContent);
        if(!$sitemap) {
            $this->logger->warning('IndexNow sitemap XML parse failed', [
                'sitemapUrl' => $sitemapUrl,
            ]);
            return [];
        }
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
