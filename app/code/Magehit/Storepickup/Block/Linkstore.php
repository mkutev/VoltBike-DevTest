<?php
namespace Magehit\Storepickup\Block;

class Linkstore extends \Magento\Framework\View\Element\Template
{
    protected $_storepickupFactory;
    protected $dataHelper;
    protected $_registry;
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magehit\Storepickup\Model\StorepickupFactory $storepickupFactory,
    \Magehit\Storepickup\Helper\Data $dataHelper,
     \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($context);
        $this->_storepickupFactory = $storepickupFactory;
        $this->dataHelper = $dataHelper;
        $this->_registry = $registry;
    }

    public function getStorehtml()
    {
     $html = '';

       $stores = $this->dataHelper->getStorebyproduct($this->_registry->registry('current_product')->getId());
       
       if($stores != Null){
        $html = '<div class="store-html">';
        foreach ($stores as  $val) {
            $_store =$this->_storepickupFactory->create()->load($val);
            $html.= '<span>- '.$_store->getStoreName().'</span>';

        }
        $html .='</div>';
       }
       return $html;
    }
}
