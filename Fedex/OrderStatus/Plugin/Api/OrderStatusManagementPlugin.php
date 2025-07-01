<?php
namespace Fedex\OrderStatus\Plugin\Api;

use Fedex\OrderStatus\Helper\RateLimiter;
use Fedex\OrderStatus\Exception\RateLimitExceededException;

/**
 * Plugin for OrderStatusManagementInterface
 *
 * This plugin adds a rate limiting layer before the updateStatus API method is executed.
 * It uses the RateLimiter helper to ensure that clients do not exceed a defined
 * number of requests within a given time frame.
 */
class OrderStatusManagementPlugin
{
    /**
     * @var RateLimiter
     */
    protected $rateLimiter;

    /**
     * Constructor
     *
     * @param RateLimiter $rateLimiter - Helper class to check and manage rate limits
     */
    public function __construct(RateLimiter $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Before plugin for updateStatus method
     *
     * This method is triggered before updateStatus is executed. It checks if
     * the client has exceeded the allowed number of API requests.
     *
     * @param \Fedex\OrderStatus\Api\OrderStatusManagementInterface $subject
     * @param string $orderIncrementId - Order increment ID passed to API
     * @param string $status - New status to be applied
     * @throws RateLimitExceededException - If request exceeds defined rate limit
     */
    public function beforeUpdateStatus(
        \Fedex\OrderStatus\Api\OrderStatusManagementInterface $subject,
                                                              $orderIncrementId,
                                                              $status
    ) {
        // Identify the client by IP address (can be replaced with token or user ID)
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Check if request is allowed under the rate limit
        if (!$this->rateLimiter->checkLimit($clientIp)) {
            throw new RateLimitExceededException();
        }
    }
}
