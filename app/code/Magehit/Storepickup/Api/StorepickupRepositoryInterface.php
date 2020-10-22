<?php


namespace Magehit\Storepickup\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StorepickupRepositoryInterface
{


    /**
     * Save Storepickup
     * @param \Magehit\Storepickup\Api\Data\StorepickupInterface $storepickup
     * @return \Magehit\Storepickup\Api\Data\StorepickupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Magehit\Storepickup\Api\Data\StorepickupInterface $storepickup
    );

    /**
     * Retrieve Storepickup
     * @param string $storepickupId
     * @return \Magehit\Storepickup\Api\Data\StorepickupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($storepickupId);

    /**
     * Retrieve Storepickup matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magehit\Storepickup\Api\Data\StorepickupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Storepickup
     * @param \Magehit\Storepickup\Api\Data\StorepickupInterface $storepickup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Magehit\Storepickup\Api\Data\StorepickupInterface $storepickup
    );

    /**
     * Delete Storepickup by ID
     * @param string $storepickupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($storepickupId);
}
