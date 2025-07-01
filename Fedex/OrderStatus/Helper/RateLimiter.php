<?php
namespace Fedex\OrderStatus\Helper;

use Magento\Framework\App\CacheInterface;

class RateLimiter
{
    const LIMIT = 10; // max 10 requests
    const WINDOW = 60; // per 60 seconds

    protected $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function checkLimit($identifier)
    {
        $cacheKey = 'rate_limit_' . md5($identifier);
        $data = $this->cache->load($cacheKey);

        $now = time();
        $requests = $data ? json_decode($data, true) : [];

        // Remove outdated requests
        $requests = array_filter($requests, function ($timestamp) use ($now) {
            return ($now - $timestamp) < self::WINDOW;
        });

        if (count($requests) >= self::LIMIT) {
            return false;
        }

        $requests[] = $now;
        $this->cache->save(json_encode($requests), $cacheKey, [], self::WINDOW);
        return true;
    }
}
