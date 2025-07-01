<?php
namespace Fedex\OrderStatus\Model;

use Fedex\OrderStatus\Api\OrderStatusManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Fedex\OrderStatus\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class OrderStatusManagement
 * Implements logic to update order status with validation
 */
class OrderStatusManagement implements OrderStatusManagementInterface
{
    const CACHE_KEY = 'fedex_order_status_available_statuses';
    const CACHE_TAG = Status::CACHE_TAG;
    const CACHE_LIFETIME = 3600;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var StatusCollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Constructor
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param CacheInterface $cache
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StatusCollectionFactory $statusCollectionFactory,
        CacheInterface $cache,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->cache = $cache;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Update order status by increment ID
     *
     * @param string $incrementId
     * @param string $newStatus
     * @return string
     * @throws LocalizedException
     */
    public function updateStatus($incrementId, $newStatus)
    {
        // Search for order using increment ID
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = reset($orders);

        if (!$order || !$order->getId()) {
            throw new LocalizedException(__('Order with increment ID "%1" does not exist.', $incrementId));
        }

        // Validate if the new status is allowed (custom + active)
        $allowedStatuses = $this->getAvailableStatuses();
        if (!in_array($newStatus, array_column($allowedStatuses, 'status'))) {
            throw new LocalizedException(__('Status "%1" is not allowed or not active in Fedex Order Statuses.', $newStatus));
        }

        // Validate against Magento's allowed transitions for the current order state
        $availableMagentoStatuses = $order->getConfig()->getStateStatuses($order->getState());
        if (!in_array($newStatus, $availableMagentoStatuses)) {
            throw new LocalizedException(__('Status "%1" is not allowed for the current order state.', $newStatus));
        }

        // Apply and save new status
        $order->setStatus($newStatus);
        $this->orderRepository->save($order);

        return __('Order status updated successfully.');
    }

    /**
     * Get list of active available statuses from Fedex custom table (cached)
     *
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        $cached = $this->cache->load(self::CACHE_KEY);
        if ($cached) {
            return json_decode($cached, true);
        }

        $collection = $this->statusCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);

        $statuses = [];
        foreach ($collection as $item) {
            $statuses[] = [
                'id' => $item->getId(),
                'status' => $item->getStatus()
            ];
        }

        $this->cache->save(
            json_encode($statuses),
            self::CACHE_KEY,
            [self::CACHE_TAG],
            self::CACHE_LIFETIME
        );

        return $statuses;
    }
}
