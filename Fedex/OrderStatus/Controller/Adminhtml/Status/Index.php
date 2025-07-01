<?php
namespace Fedex\OrderStatus\Controller\Adminhtml\Status;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * Controller for rendering the Order Status grid page in the Magento Admin panel.
 *
 * @package Fedex\OrderStatus\Controller\Adminhtml\Status
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see etc/acl.xml
     */
    const ADMIN_RESOURCE = 'Fedex_OrderStatus::order_status';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Executes the controller action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Fedex_OrderStatus::order_status');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Status'));
        return $resultPage;
    }
}
