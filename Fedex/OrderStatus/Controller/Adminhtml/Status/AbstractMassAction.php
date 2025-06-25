<?php
namespace Fedex\OrderStatus\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Fedex\OrderStatus\Model\ResourceModel\Status\CollectionFactory;

/**
 * Class AbstractMassAction
 *
 * Abstract base class for mass actions (enable, disable, delete) on order status records.
 * Provides reusable logic for filtering selected items from the admin grid.
 */
abstract class AbstractMassAction extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * AbstractMassAction constructor.
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute the action: get filtered collection and pass it to the child class's massAction method
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $this->massAction($collection);

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }

    /**
     * Abstract method to be implemented in child classes for specific mass actions
     *
     * @param \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return void
     */
    abstract protected function massAction($collection);
}
