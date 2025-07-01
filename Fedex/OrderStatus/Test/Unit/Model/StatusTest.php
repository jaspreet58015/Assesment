<?php

declare(strict_types=1);

namespace Fedex\OrderStatus\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Fedex\OrderStatus\Model\Status;

/**
 * Class StatusTest
 *
 * Unit test for the Status model. This test verifies the functionality
 * of setting and retrieving values using the model's data methods.
 */
class StatusTest extends TestCase
{
    /**
     * Test set and get methods of Status model
     *
     * Ensures that the model properly stores and retrieves order ID and status.
     */
    public function testSetAndGetData(): void
    {
        // Create an instance of the model
        $status = new Status();

        // Set test data
        $status->setOrderId('12345');
        $status->setStatus('pending');

        // Assert that the data is returned correctly
        $this->assertEquals('12345', $status->getOrderId());
        $this->assertEquals('pending', $status->getStatus());
    }
}

