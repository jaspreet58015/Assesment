<?php

namespace Fedex\OrderStatus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class LogStatusChange
 *
 * Logs order status changes and sends an email when status is changed to "shipped".
 */
class LogStatusChange implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    protected ResourceConnection $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected \Magento\Framework\DB\Adapter\AdapterInterface $connection;

    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Constructor
     */
    public function __construct(
        ResourceConnection $resource,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->logger = $logger;
    }

    /**
     * Execute observer method for `sales_order_save_after` event.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $origStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();

        if ($origStatus === null || $origStatus === $newStatus) {
            return; // No status change
        }

        try {
            // Log status change to custom table
            $this->connection->insert(
                $this->resource->getTableName('fedex_order_status_log'),
                [
                    'order_id'   => $order->getId(),
                    'old_status' => $origStatus,
                    'new_status' => $newStatus,
                    'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error(__('Failed to log status change: %1', $e->getMessage()));
        }

        // Send notification if status is 'shipped'
        if ($newStatus === 'shipped') {
            $this->sendShippedNotification($order);
        }
    }

    /**
     * Send email when order is marked as "shipped".
     *
     * @param Order $order
     * @return void
     */
    protected function sendShippedNotification(Order $order): void
    {
        try {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('fedex_order_shipped_email_template') // Must be defined in email_templates.xml
                ->setTemplateOptions([
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ])
                ->setTemplateVars([
                    'order' => $order,
                ])
                ->setFromByScope('general')
                ->addTo($order->getCustomerEmail(), $order->getCustomerName())
                ->getTransport();

            $transport->sendMessage();
        } catch (MailException | LocalizedException $e) {
            $this->logger->error(__('Failed to send shipped email: %1', $e->getMessage()));
        } finally {
            $this->inlineTranslation->resume();
        }
    }
}
