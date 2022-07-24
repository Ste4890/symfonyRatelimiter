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
            "message" => 'Hello GET request! You can also POST at the same endpoint!',
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

    public function index(): Response {
        $html = <<<HTML
            <div id="app">
                <label for="interval"> Numero di secondi tra un tentativo e l'altro</label>
                <input type="number" name="interval" id="interval" value="5">
                <br>
                <label for="method-get"> GET</label>
                <input type="radio" class="method-radio" name="method" id="method-get" value="GET" checked>
                <label for="method-post"> POST</label>
                <input type="radio" class="method-radio" name="method" id="method-post" value="POST">
                <br>
                <p>Risultato delle chiamate</p>
                <div id="result" style="white-space: pre;font-family: monospace;"></div>
                <script src="/app.js"></script>
            </div>
HTML;

        $response = new Response();


        $response->setContent("<html><body><h1>Pagina per Test</h1>$html</body></html>");
        $response->setStatusCode(Response::HTTP_OK);

        // sets a HTTP response header
        $response->headers->set('Content-Type', 'text/html');
        return $response;

    }
}
