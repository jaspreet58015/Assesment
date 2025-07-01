<?php
namespace Fedex\OrderStatus\Test\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Magento\Framework\App\CacheInterface;
use Fedex\OrderStatus\Helper\RateLimiter;

/**
 * Unit tests for the RateLimiter helper class.
 */
class RateLimiterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CacheInterface
     */
    private $cacheMock;

    /**
     * @var RateLimiter
     */
    private $rateLimiter;

    /**
     * Setup test environment and mock dependencies.
     */
    protected function setUp(): void
    {
        // Create a mock for the CacheInterface
        $this->cacheMock = $this->createMock(CacheInterface::class);

        // Inject the mock into the RateLimiter instance
        $this->rateLimiter = new RateLimiter($this->cacheMock);
    }

    /**
     * Test that checkLimit() allows requests when under the rate limit.
     */
    public function testAllowsRequestUnderLimit()
    {
        // Simulate 2 previous requests within the allowed time window
        $timestamps = [time() - 10, time() - 20];
        $this->cacheMock->method('load')->willReturn(json_encode($timestamps));

        // Expect save() to be called to store the new timestamp
        $this->cacheMock->expects($this->once())->method('save');

        $result = $this->rateLimiter->checkLimit('user_abc');
        $this->assertTrue($result);
    }

    /**
     * Test that checkLimit() blocks requests when the limit has been reached.
     */
    public function testDeniesRequestOverLimit()
    {
        // Simulate 10 recent requests — at the rate limit
        $timestamps = array_fill(0, 10, time() - 5);
        $this->cacheMock->method('load')->willReturn(json_encode($timestamps));

        // Expect save() not to be called since request should be denied
        $this->cacheMock->expects($this->never())->method('save');

        $result = $this->rateLimiter->checkLimit('user_abc');
        $this->assertFalse($result);
    }

    /**
     * Test that old timestamps are cleaned up and request is allowed.
     */
    public function testAllowsRequestAfterOldTimestampsAreFiltered()
    {
        // Simulate 2 old timestamps and 1 valid recent one
        $timestamps = [
            time() - 120, // expired
            time() - 110, // expired
            time() - 5    // valid
        ];
        $this->cacheMock->method('load')->willReturn(json_encode($timestamps));

        // Expect save() to be called after filtering old entries
        $this->cacheMock->expects($this->once())->method('save');

        $result = $this->rateLimiter->checkLimit('user_abc');
        $this->assertTrue($result);
    }

    /**
     * Test that the first request for a user is allowed and saved.
     */
    public function testAllowsFirstRequest()
    {
        // Simulate no prior data for the user
        $this->cacheMock->method('load')->willReturn(null);

        // Expect save() to be called for the first request
        $this->cacheMock->expects($this->once())->method('save');

        $result = $this->rateLimiter->checkLimit('user_new');
        $this->assertTrue($result);
    }
}
