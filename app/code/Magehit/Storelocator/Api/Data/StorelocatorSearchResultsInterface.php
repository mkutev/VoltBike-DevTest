<?php


namespace Magehit\Storelocator\Api\Data;

interface StorelocatorSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get storelocator list.
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface[]
     */
    public function getItems();

    /**
     * Set storelocator_id list.
     * @param \Magehit\Storelocator\Api\Data\StorelocatorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
