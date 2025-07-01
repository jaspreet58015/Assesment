<?php

declare(strict_types=1);

namespace Fedex\OrderStatus\Test\Unit\Controller\Adminhtml\Status;

use PHPUnit\Framework\TestCase;
use Fedex\OrderStatus\Controller\Adminhtml\Status\MassDelete;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Fedex\OrderStatus\Model\ResourceModel\Status\CollectionFactory;
use Fedex\OrderStatus\Model\Status;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class MassDeleteTest
 *
 * Unit test for the MassDelete controller.
 * This test verifies that records are deleted properly
 * and that a success message is added.
 */
class MassDeleteTest extends TestCase
{
    /**
     * Test execute() method deletes records and returns a result
     */
    public function testExecuteDeletesRecords(): void
    {
        // Create a mock status item and expect delete to be called once
        $statusMock = $this->createMock(Status::class);
        $statusMock->expects($this->once())
            ->method('delete');

        // Mock the collection and simulate it returning an array with one status item
        $collectionMock = $this->getMockBuilder(\Magento\Framework\Data\Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems'])
            ->getMock();
        $collectionMock->method('getItems')->willReturn([$statusMock]);

        // Mock the filter to return our mocked collection
        $filterMock = $this->createMock(Filter::class);
        $filterMock->method('getCollection')->willReturn($collectionMock);

        // CollectionFactory mock is required by constructor but not used directly here
        $collectionFactoryMock = $this->createMock(CollectionFactory::class);

        // Mock the message manager and expect a success message to be added
        $messageManagerMock = $this->createMock(ManagerInterface::class);
        $messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');

        // Mock context to return our mocked message manager
        $contextMock = $this->createMock(Context::class);
        $contextMock->method('getMessageManager')->willReturn($messageManagerMock);

        // Create the controller with mocks
        $controller = new MassDelete($contextMock, $filterMock, $collectionFactoryMock);

        // Execute the controller and assert it returns a result (redirect, etc.)
        $result = $controller->execute();
        $this->assertNotNull($result);
    }
}
