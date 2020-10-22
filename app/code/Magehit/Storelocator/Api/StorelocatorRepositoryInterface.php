<?php
namespace Magehit\Storelocator\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StorelocatorRepositoryInterface
{


    /**
     * Save storelocator
     * @param \Magehit\Storelocator\Api\Data\StorelocatorInterface $storelocator
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magehit\Storelocator\Api\Data\StorelocatorInterface $storelocator
    );

    /**
     * Retrieve storelocator
     * @param string $storelocatorId
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($storelocatorId);

    /**
     * Retrieve storelocator matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magehit\Storelocator\Api\Data\StorelocatorSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete storelocator
     * @param \Magehit\Storelocator\Api\Data\StorelocatorInterface $storelocator
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magehit\Storelocator\Api\Data\StorelocatorInterface $storelocator
    );

    /**
     * Delete storelocator by ID
     * @param string $storelocatorId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($storelocatorId);
}
