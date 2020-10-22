<?php
namespace David\Customevent\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function addAttachment(
        $body,
        $filename    = null
    ) {
		$this->message->createAttachment(
            $body,
            \Zend_Mime::TYPE_OCTETSTREAM,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $filename
        );
        return $this;
    }
}