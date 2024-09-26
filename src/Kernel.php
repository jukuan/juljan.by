<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        // when defining a hardcoded string, don't add the trailing slash to the path
        // e.g. '/home/user/my_project', '/app', '/var/www/example.com'
        return \dirname(__DIR__);
    }
}
