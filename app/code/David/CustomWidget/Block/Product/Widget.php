<?php
namespace David\CustomWidget\Block\Product;

class Widget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    public function _toHtml()
    {
        if($this->getData('template')){
            $template = $this->getData('template');
            $this->setTemplate($template);
        }

        return parent::_toHtml();
    }
    public function getProduct()
    {
        if (!$this->getData('product_id')) {
            throw new \RuntimeException('Parameter product id is not set.');
        }

        $productId = explode('/', $this->getData('product_id'));
        if(is_array($productId) && isset($productId[1])){
            $id = $productId[1];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($id);
            if ($product) {
                return $product;
            }
        }
        return false;
    }
}
