<?php


namespace Magehit\Storepickup\Controller\Adminhtml\Storepickup;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    const XML_NOTIFY_PRICE = 'carriers/storepickup/price';
    protected $dataPersistor;
    protected $_serialize;
    protected $scopeConfig;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magehit\Storepickup\Serialize\Serializer\Json $serialize,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_serialize = $serialize;
         $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storepickup::manage');
    }
    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $data2 = [];$kq = '';
        if ($data) {
            $id = $this->getRequest()->getParam('storepickup_id');
        
            $model = $this->_objectManager->create('Magehit\Storepickup\Model\Storepickup')->load($id);
            
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Store no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if(isset($data['schedule'])){
                if(is_array($data['schedule'])){
                    $data['schedule'] = $this->_serialize->serialize($data['schedule']);
                }else{
                    $this->messageManager->addErrorMessage( __('Something went wrong while saving the Storelocator.'));
                    return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
                }
            }
            if(isset($data['region_id'])){
                $data['region'] = '';
            }
            if($data['in_store']){
                $data['in_store'] = (implode(",",$data['in_store']));
            }
            if (isset($data['rule'])) {
                    $rule_id = $model->getRule_id();
                    $ruleRepository = $this->_objectManager->get('Magento\CatalogRule\Api\CatalogRuleRepositoryInterface');
                    $model2 = $this->_objectManager->create('Magehit\Storepickup\Model\Rule')->load($rule_id);
                    if($rule_id){
                        $data2 = $model2->getData();
                    }
                     
                    $data2['conditions'] = $data['rule']['conditions'];
                    $model2->setData($data2);
                    $kq=$model2->save();
                    if(!$rule_id){
                        $data['rule_id'] = $kq->getId();
                    }
                    unset($data['rule']);
            }

            if($data['chose_handlingfee'] == 0 || !isset($data['handling_fee'])){
                 $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                 $price_config = $this->scopeConfig->getValue(self::XML_NOTIFY_PRICE, $storeScope);
                $data['handling_fee'] == $price_config;
            }

            $model2->loadPost($data2);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
            $this->dataPersistor->set('catalog_rule', $data2);
            $model2->save();
            
            $model->setData($data);
            try {

                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Store.'));
                $this->dataPersistor->clear('magehit_storepickup_storepickup');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['storepickup_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Store.'));
            }
        
            $this->dataPersistor->set('magehit_storepickup_storepickup', $data);
            return $resultRedirect->setPath('*/*/edit', ['storepickup_id' => $this->getRequest()->getParam('storepickup_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
