<?php

namespace Linderp\SuluIndexNowBundle\Controller\Admin;
use Linderp\SuluIndexNowBundle\Service\HostExtractor;
use Linderp\SuluIndexNowBundle\Service\IndexNowSubmitter;
use Linderp\SuluIndexNowBundle\Service\SiteMapTranslator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexNowController extends AbstractController{
    private const SUBMIT_BATCH_SIZE = 1000;
    public function __construct(
        #[Autowire('%sulu_index_now.key%')]
        private readonly string $indexNowKey,
        private readonly IndexNowSubmitter $submitter,
        private readonly SiteMapTranslator $translator,
        private readonly HostExtractor $hostExtractor){
    }
    #[Route(path: '/admin/api/index-now/start', name: 'app.index-now.start', methods: ['POST'])]
    public function indexNow(Request $request): Response{
        $urls = $this->translator->translateUrls($this->getSiteMapUrl($request));
        $batches = array_chunk($urls, self::SUBMIT_BATCH_SIZE);
        $responses = [];
        $submitted = 0;
        foreach ($batches as $index => $batch) {
            $responses[$index] = $this->submitter->submit(
                $this->hostExtractor->normalizeHost($request),
                $this->indexNowKey,
                $batch
            );
            $submitted += count($batch);
        }

        return new JsonResponse([
            "responses" => $responses,
            "urls" => $urls,
            "submitted" => $submitted,
            "batchSize" => self::SUBMIT_BATCH_SIZE,
            "batchCount" => count($batches),
        ]);
    }
    #[Route(path: '/admin/api/index-now/urls', name: 'app.index-now.urls', methods: ['GET'])]
    public function getUrls(Request $request): Response{
        $urls = $this->translator->translateUrls($this->getSiteMapUrl($request));
        return new JsonResponse(["urls"=>$urls]);
    }
    private function getSiteMapUrl(Request $request):string
    {
        return $request->getSchemeAndHttpHost().'/sitemap.xml';
    }
}
