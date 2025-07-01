<?php
// File: Test/Unit/Api/OrderStatusManagementTest.php

declare(strict_types=1);

namespace Fedex\OrderStatus\Test\Unit\Api;

use PHPUnit\Framework\TestCase;
use Fedex\OrderStatus\Model\OrderStatusManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Unit test for OrderStatusManagement class
 */
class OrderStatusManagementTest extends TestCase
{
    public function testUpdateStatusSuccessfully(): void
    {
        $orderId = '100000001';
        $newStatus = 'complete';

        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->once())
            ->method('setStatus')
            ->with($newStatus);
        $orderMock->expects($this->once())
            ->method('save');

        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($orderMock);

        $manager = new OrderStatusManagement($orderRepositoryMock);
        $result = $manager->updateStatus($orderId, $newStatus);

        $this->assertEquals("Order status updated to '{$newStatus}'", $result);
    }

    public function testUpdateStatusThrowsException(): void
    {
        $this->expectException(NoSuchEntityException::class);

        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $orderRepositoryMock->method('get')
            ->willThrowException(new NoSuchEntityException(__('Not found')));

        $manager = new OrderStatusManagement($orderRepositoryMock);
        $manager->updateStatus('invalid_id', 'complete');
    }
}
