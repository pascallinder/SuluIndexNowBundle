<?php

namespace Linderp\SuluIndexNowBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class HostExtractor
{
    public function normalizeHost(Request $request): string
    {
        return preg_replace('/^www\./i', '', $request->getHost());
    }
}
