<?php
namespace Magehit\Storelocator\Block\Adminhtml\Storelocator\Edit\Tab;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $productCollectionFactory;
    protected $StorelocatorFactory;
    protected $registry;
    protected $_objectManager = null;
    protected $_category;
    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        StorelocatorFactory $StorelocatorFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magehit\Storelocator\Helper\Data $dataHelper,
        \Magento\Backend\Block\Template\Context $context,   
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->StorelocatorFactory = $StorelocatorFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        $this->_category = $categoryFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('storelocator_id')) {
            $this->setDefaultFilter(array('in_product' => 1));
        }
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    public function getCategory($categoryId) 
    {
        $category = $this->_category->create();
        $category->load($categoryId);
        return $category;
    }
    protected function _prepareCollection()
    {
        $categoryId = $this->_dataHelper->getConfig('general/cat_id');
        if($categoryId){
            $collection = $this->getCategory($categoryId)->getProductCollection();
        }else{
            $collection = $this->productCollectionFactory->create();
        }
        
        //$products->addAttributeToSelect('*');
        //$collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
       
        $model = $this->_objectManager->get('\Magehit\Storelocator\Model\Storelocator');
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => explode(',',$this->_getSelectedProducts()),
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return '';
    }
    protected function _getSelectedProducts()
    {
        $Storelocator = $this->getStorelocator();
        return $Storelocator->getData('product_ids');
    }

    public function getSelectedProducts()
    {
        $Storelocator = $this->getStorelocator();
        $selected = $Storelocator->getProduct_ids();
        //var_dump($selected);
        if ($selected!=null) {
            $selected = explode(",", $selected);
        }
        return $selected;
    }
    protected function getStorelocator()
    {
        $StorelocatorId = $this->getRequest()->getParam('storelocator_id');
        $Storelocator   = $this->StorelocatorFactory->create();
        if ($StorelocatorId) {
            $Storelocator->load($StorelocatorId);
        }
       
        return $Storelocator;
    }
 
    public function canShowTab()
    {
        return true;
    }
   
    public function isHidden()
    {
        return true;
    }
}