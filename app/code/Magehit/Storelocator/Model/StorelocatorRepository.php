<?php
namespace Magehit\Storelocator\Model;

use Magehit\Storelocator\Model\ResourceModel\Storelocator\CollectionFactory as StorelocatorCollectionFactory;
use Magehit\Storelocator\Api\Data\StorelocatorInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magehit\Storelocator\Api\Data\StorelocatorSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magehit\Storelocator\Model\ResourceModel\Storelocator as ResourceStorelocator;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magehit\Storelocator\Api\StorelocatorRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class StorelocatorRepository implements storelocatorRepositoryInterface
{

    protected $resource;

    protected $storelocatorCollectionFactory;

    protected $searchResultsFactory;

    private $storeManager;

    protected $dataStorelocatorFactory;

    protected $storelocatorFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    public function __construct(
        ResourceStorelocator $resource,
        StorelocatorFactory $storelocatorFactory,
        StorelocatorInterfaceFactory $dataStorelocatorFactory,
        StorelocatorCollectionFactory $storelocatorCollectionFactory,
        StorelocatorSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storelocatorFactory = $storelocatorFactory;
        $this->storelocatorCollectionFactory = $storelocatorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStorelocatorFactory = $dataStorelocatorFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }


    public function save(
        \Magehit\Storelocator\Api\Data\StorelocatorInterface $storelocator
    ) {
        try {
            $storelocator->getResource()->save($storelocator);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the storelocator: %1',
                $exception->getMessage()
            ));
        }
        return $storelocator;
    }


    public function getById($storelocatorId)
    {
        $storelocator = $this->storelocatorFactory->create();
        $storelocator->getResource()->load($storelocator, $storelocatorId);
        if (!$storelocator->getId()) {
            throw new NoSuchEntityException(__('storelocator with id "%1" does not exist.', $storelocatorId));
        }
        return $storelocator;
    }


    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->storelocatorCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {

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


    public function delete(
        \Magehit\Storelocator\Api\Data\StorelocatorInterface $storelocator
    ) {
        try {
            $storelocator->getResource()->delete($storelocator);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the storelocator: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    public function deleteById($storelocatorId)
    {
        return $this->delete($this->getById($storelocatorId));
    }
}
