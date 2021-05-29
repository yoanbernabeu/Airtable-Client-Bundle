<?php

namespace Yoanbernabeu\AirtableClientBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as HttpKernelKernel;
use Yoanbernabeu\AirtableClientBundle\AirtableClientBundle;

class Kernel extends HttpKernelKernel
{

    public function registerBundles()
    {
        return  [
            new AirtableClientBundle
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}
