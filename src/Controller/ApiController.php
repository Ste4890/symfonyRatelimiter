<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController {
    public const OK_STATUS_CODE = 200;
    public const TOO_MANY_REQUESTS_STATUS_CODE = 429;

    public function handleGet(Request $request): Response {
        return new JsonResponse([
            "status" => 'ok',
            "message" => 'Hello GET request! You can also PoST at the same endpoint!',
        ],
            self::OK_STATUS_CODE);
    }

    public function handlePost(Request $request): Response {
        return new JsonResponse([
            "status" => 'ok',
            "message" => 'See? I told you it would work!!',
        ],
            self::OK_STATUS_CODE);
    }
}
