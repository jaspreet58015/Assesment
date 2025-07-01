<?php
namespace Fedex\OrderStatus\Model\ResourceModel\Status;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * Collection class for Fedex Order Status logs.
 * Handles loading multiple records from the 'fedex_order_status' table.
 *
 * @package Fedex\OrderStatus\Model\ResourceModel\Status
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize collection
     *
     * Sets the model and resource model for this collection.
     */

    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model.
     */

    protected function _construct()
    {
        $this->_init(
            \Fedex\OrderStatus\Model\Status::class,
            \Fedex\OrderStatus\Model\ResourceModel\Status::class
        );
    }
}
