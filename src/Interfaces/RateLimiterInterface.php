<?php

namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RateLimiterInterface {
    public function requestShouldBeLimited(Request $request): bool;
    public function getMessage():string;
    public function getRetryAfter(): int;
}
