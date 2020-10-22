<?php

namespace Magehit\Storepickup\Controller\Adminhtml\Storepickup;

class Regionlist extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    protected $_countryFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {


        $countrycode = $this->getRequest()->getParam('country');
        $id = $this->getRequest()->getParam('region');
        $state = "<option value=''>--Please Select--</option>";
        $result['status']=0;
        if ($countrycode != '') {
            $statearray =$this->_countryFactory->create()->setId(
                    $countrycode
                )->getLoadedRegionCollection()->toOptionArray();
            if(count($statearray)){
                $result['status']=1;
                foreach ($statearray as $_state) {
                if($_state['value']){
                    if($id && $id === $_state['value']){
                        $state .= "<option selected= \"selected\" value=\"".$_state['value']."\">" . $_state['label'] . "</option>";
                    }else{
                        $state .= "<option value=\"".$_state['value']."\">" . $_state['label'] . "</option>";
                    }
                    
                }
            }
            
           }
        }
       $result['htmlconent']=$state;
         $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    } 

  }