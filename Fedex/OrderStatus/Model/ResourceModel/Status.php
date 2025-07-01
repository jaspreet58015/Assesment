<?php
namespace Fedex\OrderStatus\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Fedex\OrderStatus\Model\Status as StatusModel;
use Magento\Framework\App\CacheInterface;

class Status extends AbstractDb
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        CacheInterface $cache
    ) {
        parent::__construct($context);
        $this->cache = $cache;
    }

    protected function _construct()
    {
        $this->_init('fedex_order_status', 'id');
    }

    /**
     * Invalidate status cache after save
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->cleanCache();
        return parent::_afterSave($object);
    }

    /**
     * Invalidate status cache after delete
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->cleanCache();
        return parent::_afterDelete($object);
    }

    /**
     * Clean cache tag related to Fedex order status
     */
    private function cleanCache(): void
    {
        $this->cache->clean([StatusModel::CACHE_TAG]);
    }
}
