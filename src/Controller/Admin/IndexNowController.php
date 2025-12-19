<?php

namespace Linderp\SuluIndexNowBundle\Controller\Admin;
use Linderp\SuluIndexNowBundle\Service\IndexNowSubmitter;
use Linderp\SuluIndexNowBundle\Service\SiteMapTranslator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexNowController extends AbstractController{
    public function __construct(
        private readonly string $indexNowKey,
        private readonly IndexNowSubmitter $submitter,
        private readonly SiteMapTranslator $translator){
    }
    #[Route(path: '/admin/api/index-now/start', name: 'app.index-now.start', methods: ['GET'])]
    public function indexNow(Request $request): Response{
        $urls = $this->translator->translateUrls($this->getSiteMapUrl($request));
        $responses = $this->submitter->submit($request->getHost(),$this->indexNowKey,$urls);
        return new JsonResponse(["responses"=>$responses,"urls"=>$urls]);
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
