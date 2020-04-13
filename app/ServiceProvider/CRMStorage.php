<?php

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CRMStorage implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['crmNewsfeed'] = function (\Slim\Container $c) {
            return new \App\Storage\CRM\Newsfeed($c->get('db'));
        };

        $container['crmAuth'] = function (\Slim\Container $c) {
            return new \App\Storage\CRM\Auth($c->get('db'));
        };
    }
}
