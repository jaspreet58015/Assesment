<?php
namespace Fedex\OrderStatus\Api;

interface OrderStatusManagementInterface
{
    /**
     * Update order status by increment ID
     *
     * @param string $incrementId
     * @param string $newStatus
     * @return string
     */
    public function updateStatus($incrementId, $newStatus);
}
