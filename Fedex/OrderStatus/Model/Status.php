<?php
namespace Fedex\OrderStatus\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Model class for fedex_order_status table.
 * Handles each row in the order status grid.
 *
 * @category  Fedex
 * @package   Fedex_OrderStatus
 */
class Status extends AbstractModel
{
    /**
     * Cache tag for this model.
     */
    const CACHE_TAG = 'fedex_order_status';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix for model events.
     *
     * @var string
     */
    protected $_eventPrefix = 'fedex_order_status';

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Fedex\OrderStatus\Model\ResourceModel\Status::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * Get status value
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Set status value
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * Get is_active
     *
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    /**
     * Set is_active
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData('is_active', $isActive);
    }

    /**
     * Get created_at timestamp
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * Set created_at timestamp
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * Get updated_at timestamp
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * Set updated_at timestamp
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData('updated_at', $updatedAt);
    }
}
