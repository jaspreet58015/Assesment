<?php
namespace Fedex\OrderStatus\Exception;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class RateLimitExceededException
 *
 * Thrown when a client exceeds the allowed number of API requests
 * within a specified time window (rate limit).
 */
class RateLimitExceededException extends LocalizedException
{
    /**
     * RateLimitExceededException constructor.
     *
     * Initializes the exception with a user-friendly message indicating
     * that the rate limit has been exceeded.
     */
    public function __construct()
    {
        parent::__construct(__(
            'API rate limit exceeded. You have made too many requests in a short period. Please wait and try again later.'
        ));
    }
}
