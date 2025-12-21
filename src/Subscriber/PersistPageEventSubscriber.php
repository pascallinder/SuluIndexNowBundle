<?php

namespace Linderp\SuluIndexNowBundle\Subscriber;

use Linderp\SuluIndexNowBundle\Service\HostExtractor;
use Linderp\SuluIndexNowBundle\Service\IndexNowSubmitter;
use Sulu\Component\DocumentManager\Event\PersistEvent;
use Sulu\Component\DocumentManager\Event\PublishEvent;
use Sulu\Component\DocumentManager\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class PersistPageEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $indexNowKey,
        private IndexNowSubmitter $submitter,
        private HostExtractor $hostExtractor,
        private RequestStack $requestStack
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
        $request = $this->requestStack->getCurrentRequest();

        $this->submitter->submit($this->hostExtractor->normalizeHost($request),$this->indexNowKey,[
            $this->buildUrl($request,$document->getLocale(),$document->getResourceSegment()),
        ]);
    }
    public function buildUrl(Request $request, string $locale, string $resourceSegment): string
    {
        return $request->getScheme() . '://www.' . $this->hostExtractor->normalizeHost($request) .'/'.$locale.$resourceSegment;
    }
}
