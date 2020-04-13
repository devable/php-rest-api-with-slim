<?php

namespace App\Controller;

use Slim\Http;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Helper\UtilsHelper;
use App\Helper\HttpHelper;
use App\Middleware\Validation\AuthValidation;
use App\Middleware\Validation\HashParamValidation;
use App\Exception\CustomException;

class AuthController
{
    public function __construct(\Slim\App $app)
    {
        $app->post('', function (Request $request, Response $response) {
            /** @var  $auth \App\Storage\CRM\Auth */
            $auth = $this->crmAuth;

            $uid = $request->getParam('uid');
            $token = $request->getParam('token');
            
            if (!$auth->check($uid, $token)) throw new CustomException('Устройство не идентифицировано');

            return HttpHelper::successResponse($response);
        })->add(new AuthValidation());

        $app->post('/login', function (Request $request, Response $response) {
            /** @var  $auth \App\Storage\CRM\Auth */
            $auth = $this->crmAuth;

            $hash = $request->getParam('hash');
            $uid = $request->getParam('uid');

            $userId = $auth->validation($hash);

            if (!$userId) throw new CustomException('Ошибка валидации устройства');

            $token = UtilsHelper::makeUid(13);

            if (!$auth->registration($userId, $uid, $token)) throw new CustomException('Устройство не зарегистрировано');

            return HttpUtils::successResponse($response, ['token' => $token]);
        })->add(new HashParamValidation());

        $app->post('/logout', function (Request $request, Response $response) {
            /** @var  $auth \App\Storage\CRM\Auth */
            $auth = $this->crmAuth;

            $uid = $request->getParam('uid');
            $token = $request->getParam('token');

            if (!$auth->logout($uid, $token)) throw new CustomException('Не удалось осуществить выход');

            return HttpHelper::successResponse($response);
        })->add(new AuthValidation());

        $app->post('/user', function (Request $request, Response $response) {
            /** @var  $auth \App\Storage\CRM\Auth */
            $auth = $this->crmAuth;

            $uid = $request->getParam('uid');
            $token = $request->getParam('token');

            $data = $auth->user($uid, $token);

            if (!$data) throw new CustomException('Устройство не идентифицировано');

            return HttpUtils::successResponse($response, ['data' => $data]);
        })->add(new AuthValidation());
    }
}
