<?php

namespace App\Services;

use App\Interfaces\StorageInterface;
use App\Interfaces\RateLimiterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class IPBasedRateLimiterService implements RateLimiterInterface {
    private string $message = '';
    private int $retryAfter = 60;

    public function __construct(
        private readonly StorageInterface $storage,
        private readonly int              $maxPostRequests,
        private readonly int              $maxGetRequests,
        private readonly string           $ipWhitelist,
        private readonly int              $ratelimitInterval,
    ) {
    }


    /**
     * Naive limiter algo:
     * at the start of each request, ask redis storage for value for key(ip+method)
     * clear value from expired requests
     * add current timestamp
     * store back in cache
     * if value contains less than or equal max allowed => ok!
     * else if value is greater than max allowed rate limit => ko!
     *
     */
    public function requestShouldBeLimited(Request $request): bool {
        $now = time();
        $remoteAddress = $request->getClientIp();
        $method = $request->getMethod();

        if ($this->addressIsExemptFromLimiting($remoteAddress)) {
            return false;
        }
        $cacheKey = $this->computeCacheKey($remoteAddress, $method);

        //- at the start of each request, ask cache for value for key(ip)
        $pastRequestsTimestamps = $this->storage->get($cacheKey);

        //- clear value from expired requests
        $timestampsInCurrentTimeFrame = $this->evictExpiredRequestTimestamps(
            $pastRequestsTimestamps,
            $now);

        //- add current timestamp
        $timestampsInCurrentTimeFrame[] = $now;

        // store back in cache
        $this->storage->set($cacheKey, $timestampsInCurrentTimeFrame);

        if (count($timestampsInCurrentTimeFrame) <= $this->getRequestLimitByMethod($method)) {
            //- if value contains less than or equal max allowed => ok!
            return false;
        } else {
            $this->message =
                "$method requests by $remoteAddress have exceeded the limit of " .
                $this->getRequestLimitByMethod($method) .
                " in {$this->ratelimitInterval} seconds";
            //- else if value is greater than max allowed rate limit => ko!
            return true;
        }
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getRetryAfter(): int {
        return $this->retryAfter;
    }

    protected function getRequestLimitByMethod(string $method): int {
        return match ($method) {
            'POST' => $this->maxPostRequests,
            'GET' => $this->maxGetRequests,
            //default is 0 because method should not even be allowed!
            default => 0
        };
    }

    protected function addressIsExemptFromLimiting(string $address): bool {
        return in_array($address, $this->getWhitelistedAddressesAsArray());
    }

    protected function getWhitelistedAddressesAsArray(): array {
        if (empty($this->ipWhitelist)) {
            return [];
        }
        //fixme: add string validation
        return array_map(
            fn(string $ip) => trim($ip),
            explode(',', $this->ipWhitelist));
    }

    protected function computeCacheKey(string $ip_address, string $method): string {
        return $ip_address . $method;
    }

    protected function evictExpiredRequestTimestamps(array $pastRequestsTimestamps, int $currentTime): array {
        return array_filter($pastRequestsTimestamps,
            fn($oldTimestamp) => ($oldTimestamp > ($currentTime - $this->ratelimitInterval))
        );
    }

}
