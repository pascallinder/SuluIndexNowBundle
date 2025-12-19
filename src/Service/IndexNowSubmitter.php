<?php

namespace Linderp\SuluIndexNowBundle\Service;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class IndexNowSubmitter
{
    private array $endpoints = [
        'IndexNow'      => 'https://api.indexnow.org/indexnow',
        'Amazon'        => 'https://indexnow.amazonbot.amazon/indexnow',
        'Bing'          => 'https://www.bing.com/indexnow',
        'Naver'         => 'https://searchadvisor.naver.com/indexnow',
        'Seznam'        => 'https://search.seznam.cz/indexnow',
        'Yandex'        => 'https://yandex.com/indexnow',
        'Yep'           => 'https://indexnow.yep.com/indexnow',
    ];

    public function submit(string $host, string $key, array $urls): array
    {
        if(empty($urls)) {
            return [];
        }
        $client = HttpClient::create();
        $payload = [
            'host'        => "www.".$host,
            'key'         => $key,
            'urlList'     => $urls,
        ];

        $responses = [];
        foreach ($this->endpoints as $name => $endpoint) {
            try {
                $response = $client->request('POST', $endpoint, [
                    'json' => $payload,
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                    ],
                    'timeout' => 600,
                ]);

                $responses[$name] = [
                    'status' => $response->getStatusCode(),
                    'body'   => $response->getContent(false),
                ];

            } catch (TransportExceptionInterface|RedirectionExceptionInterface|ClientExceptionInterface|ServerExceptionInterface $e) {
                $responses[$name] = [
                    'status' => 'error',
                    'body'   => $e->getMessage(),
                ];
            }
        }

        return $responses;
    }
}
