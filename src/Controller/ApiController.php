<?php

namespace App\Controller;

use App\Interfaces\RateLimiterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController {
    public const OK_STATUS_CODE = 200;
    public const TOO_MANY_REQUESTS_STATUS_CODE = 429;

    public function __construct(
        private RateLimiterInterface $rateLimiter,
    ) {

    }

    public function handleGet(Request $request): Response {
        if ($this->rateLimiter->requestShouldBeLimited($request)) {
            return new JsonResponse([
                "status" => 'ko',
                "message" => $this->rateLimiter->getMessage(),
            ],
                self::TOO_MANY_REQUESTS_STATUS_CODE,
                [
                    'Retry-After' => $this->rateLimiter->getRetryAfter(),
                ]
            );
        }
        return new JsonResponse([
            "status" => 'ok',
            "message" => 'Hello GET request! You can also PoST at the same endpoint!',
        ],
            self::OK_STATUS_CODE);
    }

    public function handlePost(Request $request): Response {
        if ($this->rateLimiter->requestShouldBeLimited($request)) {
            return new JsonResponse([
                "status" => 'ko',
                "message" => $this->rateLimiter->getMessage(),
            ],
                self::TOO_MANY_REQUESTS_STATUS_CODE,
                [
                    'Retry-After' => $this->rateLimiter->getRetryAfter(),
                ]
            );
        }
        return new JsonResponse([
            "status" => 'ok',
            "message" => 'See? I told you it would work!!',
        ],
            self::OK_STATUS_CODE,);
    }
}
