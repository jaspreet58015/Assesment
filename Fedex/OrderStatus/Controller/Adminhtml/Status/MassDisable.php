<?php
namespace Fedex\OrderStatus\Controller\Adminhtml\Status;

/**
 * Class MassDisable
 *
 * Controller for mass-disabling order status records from the admin grid.
 * Extends AbstractMassAction to reuse filtering and permission logic.
 */
class MassDisable extends \Fedex\OrderStatus\Controller\Adminhtml\Status\AbstractMassAction
{
    /**
     * Perform the mass disable action on selected items
     *
     * @param \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return void
     */
    protected function massAction($collection)
    {
        $count = 0;
        foreach ($collection as $item) {
            $item->setIsActive(0);
            $item->save();
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('%1 status(es) disabled.', $count));
    }
}
