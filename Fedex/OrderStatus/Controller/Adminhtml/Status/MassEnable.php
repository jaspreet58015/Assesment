<?php
namespace Fedex\OrderStatus\Controller\Adminhtml\Status;

/**
 * Class MassEnable
 *
 * Controller for mass-enabling order status records from the admin grid.
 * Inherits common logic from AbstractMassAction.
 */
class MassEnable extends \Fedex\OrderStatus\Controller\Adminhtml\Status\AbstractMassAction
{
    /**
     * Perform the mass enable action on selected items
     *
     * @param \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return void
     */
    protected function massAction($collection)
    {
        $count = 0;
        foreach ($collection as $item) {
            $item->setIsActive(1);
            $item->save();
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('%1 status(es) enabled.', $count));
    }
}
