<?php
declare(strict_types=1);

namespace Fedex\OrderStatus\Test\Integration\Model;

use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;
use Fedex\OrderStatus\Model\Status;
use Fedex\OrderStatus\Model\StatusFactory;
use Magento\Framework\Registry;

/**
 * Integration test for Fedex\OrderStatus\Model\Status
 *
 * This test verifies that the Status model saves and loads correctly
 * using Magento's ORM and factory classes, without direct use of the ObjectManager.
 */
class StatusTest extends TestCase
{
    /**
     * @var StatusFactory
     */
    private $statusFactory;

    /**
     * Set up dependencies using Magento's DI
     */
    protected function setUp(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = ObjectManager::getInstance();
        $this->statusFactory = $objectManager->get(StatusFactory::class);
    }

    /**
     * Test that a Status model can be saved and loaded properly.
     */
    public function testSaveAndLoadStatus(): void
    {
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setOrderId('100000001');
        $status->setStatus('shipped');

        $status->save();
        $this->assertNotNull($status->getId(), 'Model ID should not be null after save.');

        // Reload the model from DB using the factory
        /** @var Status $loaded */
        $loaded = $this->statusFactory->create()->load($status->getId());

        $this->assertEquals('100000001', $loaded->getOrderId(), 'Order ID should match.');
        $this->assertEquals('shipped', $loaded->getStatus(), 'Status should match.');
    }

    /**
     * Test that an unsaved model has no ID.
     */
    public function testNewModelHasNoId(): void
    {
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $this->assertNull($status->getId(), 'New model should not have an ID before save.');
    }
}
