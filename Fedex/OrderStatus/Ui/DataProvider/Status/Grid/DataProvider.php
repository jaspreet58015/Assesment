<?php
namespace Fedex\OrderStatus\Ui\DataProvider\Status\Grid;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Fedex\OrderStatus\Model\ResourceModel\Status\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $items = $this->collection->getItems();
        if (!isset($this->loadedData)) {
            $items = $this->collection->getItems();
            $this->loadedData = [
                'items' => [],
                'totalRecords' => $this->collection->getSize()
            ];

            foreach ($items as $item) {
                $this->loadedData['items'][] = $item->getData();
            }
        }

        return $this->loadedData;
    }

}
