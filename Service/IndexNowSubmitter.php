<?php

namespace Linderp\SuluIndexNowBundle\Service;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class IndexNowSubmitter
{
    public function __construct(
        #[Autowire('%sulu_index_now.search_engines%')]
        private array           $endpoints,
        private LoggerInterface $logger,
    )
    {

    }

    public function submit(string $host, string $key, array $urls): array
    {
        if(empty($urls)) {
            return [];
        }
        $client = HttpClient::create();
        $payload = [
            'host'        => $host,
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
                $this->logger->debug('Index now submitted to: '.$endpoint . ', status: '.$response->getStatusCode());
            } catch (TransportExceptionInterface|RedirectionExceptionInterface|ClientExceptionInterface|ServerExceptionInterface $e) {
                $responses[$name] = [
                    'status' => 'error',
                    'body'   => $e->getMessage(),
                ];
                $this->logger->error($e->getMessage(),[
                    "payload" => $payload,
                    "endpoint" => $endpoint,
                    "name" => $name,
                ]);
            }
        }

        return $responses;
    }
}
