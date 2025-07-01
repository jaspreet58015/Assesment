<?php
declare(strict_types=1);

namespace Fedex\OrderStatus\Test\Integration\Api;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Fedex\OrderStatus\Api\OrderStatusManagementInterface;

/**
 * Integration test for Fedex\OrderStatus\Api\OrderStatusManagementInterface
 */
class OrderStatusManagementTest extends TestCase
{
    /**
     * @var OrderStatusManagementInterface
     */
    private $orderStatusManager;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Set up Magento services using dependency injection.
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->orderStatusManager = $objectManager->get(OrderStatusManagementInterface::class);
        $this->orderRepository = $objectManager->get(OrderRepositoryInterface::class);
    }

    /**
     * Test that updateStatus correctly updates the status of a valid order.
     *
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testUpdateOrderStatusSuccessfully(): void
    {
        $incrementId = '100000001'; // Comes from the order fixture
        $newStatus = 'holded';

        // Perform the status update
        $result = $this->orderStatusManager->updateStatus($incrementId, $newStatus);

        // Load the order and assert the status change
        $order = $this->orderRepository->get($incrementId);
        $this->assertEquals($newStatus, $order->getStatus());
        $this->assertEquals("Order status updated to '{$newStatus}'", $result);
    }

    /**
     * Test that an exception is thrown when using an invalid order increment ID.
     */
    public function testUpdateStatusWithInvalidIncrementId(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->orderStatusManager->updateStatus('non_existing_increment_id', 'processing');
    }
}
