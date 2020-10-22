<?php

namespace   Magehit\Storepickup\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;



class Rule extends AbstractModel
{


    protected $_eventPrefix = 'magehit_storepickup_rules';

    protected $_eventObject = 'rule';

    protected $_combineFactory;

    protected $_actionCollectionFactory;

    protected $validatedAddresses = [];

    protected $_storeManager;

    protected $_productCollectionFactory;

    protected $_productIds;

    protected $_resourceIterator;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_combineFactory = $combineFactory;
        $this->_actionCollectionFactory = $actionCollectionFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_storeManager = $storeManager;
         $this->_productFactory = $productFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magehit\Storepickup\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return $this->_combineFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->_actionCollectionFactory->create();
    }

    public function getConditionsFieldSetId($formName = '')
    {
        if($this->getRule_id()){
             return $formName . 'rule_conditions_fieldset_'.$this->getRule_id() ;
        }
        return $formName . 'rule_conditions_fieldset_' ;
    }
    private function getRuleConditionConverter()
    {
       
        if (null === $this->ruleConditionConverter) {
            $this->ruleConditionConverter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\CatalogRule\Model\Data\Condition\Converter::class);
        }
        return $this->ruleConditionConverter;
    }
        /**
     * {@inheritdoc}
     */
    public function getRuleCondition()
    {
        return $this->getRuleConditionConverter()->arrayToDataModel($this->getConditions()->asArray());
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleCondition($condition)
    {
        $this->getConditions()
            ->setConditions([])
            ->loadArray($this->getRuleConditionConverter()->dataModelToArray($condition));
        return $this;
    }

//neeeeeeeeeeeeeeeeeeeeeeeeeewwwwwwwww 03/05

    public function getListProductIdsInRule()
    {
         $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
             '\Magento\Catalog\Model\ResourceModel\Product\Collection'
         );
         $productFactory = \Magento\Framework\App\ObjectManager::getInstance()->create(
             '\Magento\Catalog\Model\ProductFactory'
         );
         $this->_productIds = [];
         $this->setCollectedAttributes([]);
         $this->getConditions()->collectValidatedAttributes($productCollection);

         \Magento\Framework\App\ObjectManager::getInstance()->create(
             '\Magento\Framework\Model\ResourceModel\Iterator'
         )->walk(
             $productCollection->getSelect(),
             [[$this, 'callbackValidateProduct']],
             [
                 'attributes' => $this->getCollectedAttributes(),
                 'product' => $productFactory->create()
             ]
         );
         return $this->_productIds;
    }
    /**
    * Callback function for product matching
    *
    * @param array $args
    * @return void
    */
    public function callbackValidateProduct($args)
    {
       
         $product = clone $args['product'];
         $product->setData($args['row']);
         // $websites = $this->_getWebsitesMap();

         // foreach ($websites as $websiteId => $defaultStoreId) {
             $product->setStoreId($this->_storeManager->getStore()->getId());
             if ($this->getConditions()->validate($product)) {
                 $this->_productIds[] = $product->getId();
             }
        // }
    }
    /**
    * Prepare website map
    *
    * @return array
    */
    protected function _getWebsitesMap()
    {
         $map = [];
         $websites = \Magento\Framework\App\ObjectManager::getInstance()->create(
             '\Magento\Store\Model\StoreManagerInterface'
         )->getWebsites();
         foreach ($websites as $website) {
             // Continue if website has no store to be able to create catalog rule for website without store
             if ($website->getDefaultStore() === null) {
                 continue;
             }
             $map[$website->getId()] = $website->getDefaultStore()->getId();
         }
         return $map;
    }


    public function validateData(\Magento\Framework\DataObject $dataObject)
    {
        $result = parent::validateData($dataObject);
        if ($result === true) {
            $result = [];
        }

        $action = $dataObject->getData('simple_action');
        $discount = $dataObject->getData('discount_amount');
        $result = array_merge($result, $this->validateDiscount($action, $discount));

        return !empty($result) ? $result : true;
    }

   
    protected function validateDiscount($action, $discount)
    {
        $result = [];
        switch ($action) {
            case 'by_percent':
            case 'to_percent':
                if ($discount < 0 || $discount > 100) {
                    $result[] = __('Percentage discount should be between 0 and 100.');
                };
                break;
            case 'by_fixed':
            case 'to_fixed':
                if ($discount < 0) {
                    $result[] = __('Discount value should be 0 or greater.');
                };
                break;
            default:
                $result[] = __('Unknown action.');
        }
        return $result;
    }

    public function setProductsFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    /**
     * Returns products filter
     *
     * @return array|int|null
     * @codeCoverageIgnore
     */
    public function getProductsFilter()
    {
        return $this->_productsFilter;
    }

}