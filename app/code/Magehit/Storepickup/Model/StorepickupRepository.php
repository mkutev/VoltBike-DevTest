<?php


namespace Magehit\Storepickup\Model;

use Magehit\Storepickup\Api\Data\StorepickupSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\DataObjectHelper;
use Magehit\Storepickup\Api\Data\StorepickupInterfaceFactory;
use Magehit\Storepickup\Model\ResourceModel\Storepickup\CollectionFactory as StorepickupCollectionFactory;
use Magehit\Storepickup\Api\StorepickupRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magehit\Storepickup\Model\ResourceModel\Storepickup as ResourceStorepickup;
use Magento\Framework\Exception\CouldNotSaveException;

class StorepickupRepository implements storepickupRepositoryInterface
{

    private $storeManager;

    protected $dataObjectProcessor;

    protected $storepickupCollectionFactory;

    protected $storepickupFactory;

    protected $dataStorepickupFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $resource;


    /**
     * @param ResourceStorepickup $resource
     * @param StorepickupFactory $storepickupFactory
     * @param StorepickupInterfaceFactory $dataStorepickupFactory
     * @param StorepickupCollectionFactory $storepickupCollectionFactory
     * @param StorepickupSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceStorepickup $resource,
        StorepickupFactory $storepickupFactory,
        StorepickupInterfaceFactory $dataStorepickupFactory,
        StorepickupCollectionFactory $storepickupCollectionFactory,
        StorepickupSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storepickupFactory = $storepickupFactory;
        $this->storepickupCollectionFactory = $storepickupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStorepickupFactory = $dataStorepickupFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Magehit\Storepickup\Api\Data\StorepickupInterface $storepickup
    ) {
        /* if (empty($storepickup->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $storepickup->setStoreId($storeId);
        } */
        try {
            $storepickup->getResource()->save($storepickup);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the storepickup: %1',
                $exception->getMessage()
            ));
        }
        return $storepickup;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($storepickupId)
    {
        $storepickup = $this->storepickupFactory->create();
        $storepickup->getResource()->load($storepickup, $storepickupId);
        if (!$storepickup->getId()) {
            throw new NoSuchEntityException(__('Storepickup with id "%1" does not exist.', $storepickupId));
        }
        return $storepickup;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->storepickupCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Magehit\Storepickup\Api\Data\StorepickupInterface $storepickup
    ) {
        try {
            $storepickup->getResource()->delete($storepickup);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Storepickup: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($storepickupId)
    {
        return $this->delete($this->getById($storepickupId));
    }
}
