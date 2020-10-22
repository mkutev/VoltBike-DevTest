<?php


namespace Magehit\Storepickup\Api\Data;

interface StorepickupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Storepickup list.
     * @return \Magehit\Storepickup\Api\Data\StorepickupInterface[]
     */
    public function getItems();

    /**
     * Set store_name list.
     * @param \Magehit\Storepickup\Api\Data\StorepickupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
