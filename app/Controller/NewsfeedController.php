<?php

namespace App\Controller;

use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Helper\HttpHelper;

class NewsfeedController
{
    public $container;

    public function __construct(\Slim\App $app, string $type = 'news')
    {
        $app->get('', function (Request $request, Response $response) use ($type) {
            /** @var  $newsfeed Newsfeed */
            $newsfeed = $this->crmNewsfeed;

            $list = $newsfeed->list(
                $type,
                $request->getParam('limit', 10),
                $request->getParam('page', 1),
                $request->getParam('content_length', 0),
                in_array($request->getParam('with_images', 'true'), ['true', '1'], true)
            );

            if (!$list) {
                throw new NotFoundException($request, $response);
            }

            return HttpHelper::successResponse($response, [
                'count' => $newsfeed->total($type),
                'items' => $list
            ]);
        });

        $app->get('/{id:\d+}', function (Request $request, Response $response, array $args) use ($type) {
            /** @var  $newsfeed Newsfeed */
            $newsfeed = $this->crmNewsfeed;

            $item = $newsfeed->item($type, $args['id']);

            if (!$item) {
                throw new NotFoundException($request, $response);
            }

            return HttpHelper::successResponse($response, ['item' => $item]);
        });
    }
}
