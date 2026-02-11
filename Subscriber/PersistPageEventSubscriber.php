<?php

namespace Linderp\SuluIndexNowBundle\Subscriber;

use Linderp\SuluIndexNowBundle\Service\HostExtractor;
use Linderp\SuluIndexNowBundle\Service\IndexNowSubmitter;
use Psr\Log\LoggerInterface;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\DocumentManager\Event\PersistEvent;
use Sulu\Component\DocumentManager\Event\PublishEvent;
use Sulu\Component\DocumentManager\Events;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class PersistPageEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire('%sulu_index_now.key%')]
        private string $indexNowKey,
        private IndexNowSubmitter $submitter,
        private HostExtractor $hostExtractor,
        private RequestStack $requestStack,
        private WebspaceManagerInterface $webspaceManager,
        #[Autowire('%kernel.environment%')]
        private string $environment,
        private LoggerInterface $logger
    )
    {}
    public static function getSubscribedEvents(): array
    {
        return [
            Events::PUBLISH  => 'onPublish',
        ];
    }
    public function onPublish(PublishEvent $event): void
    {
        $document = $event->getDocument();
        if($document instanceof PageDocument){
            $extensions =  $document->getExtensionsData();
            if (($extensions['seo']['noIndex'] ?? false) === true) {
                return;
            }
            $request = $this->requestStack->getCurrentRequest();
            if (!$request) {
                // Publishing can happen without an HTTP request (CLI/worker); skip submission.
                return;
            }
            $url = $this->buildUrl(
                $request,
                $event->getLocale(),
                $document->getResourceSegment(),
                $document->getWebspaceName()
            );
            if (!$url) {
                $this->logger->warning('IndexNow URL resolution failed', [
                    'locale' => $event->getLocale(),
                    'resourceSegment' => $document->getResourceSegment(),
                    'webspace' => $document->getWebspaceName(),
                ]);
                return;
            }
            $this->submitter->submit($this->hostExtractor->normalizeHost($request), $this->indexNowKey, [$url]);
        }
    }
    public function buildUrl(
        Request $request,
        string $locale,
        string $resourceSegment,
        string $webspaceKey
    ): ?string
    {
        if (!$resourceSegment) {
            return null;
        }

        return $this->webspaceManager->findUrlByResourceLocator(
            $resourceSegment,
            $this->environment,
            $locale,
            $webspaceKey,
            null,
            $request->getScheme()
        );
    }
}
