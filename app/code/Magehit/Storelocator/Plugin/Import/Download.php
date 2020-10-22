<?php
namespace Magehit\Storelocator\Plugin\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download 
{

    private $reader;
    private $readFactory;
    private $messageManager;
    private $resultRedirectFactory;
    private $fileFactory;
    private $resultRawFactory;
    private $requestInterface;

    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\Module\Dir\Reader $reader,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {

        $this->reader = $reader;
        $this->readFactory = $readFactory;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->requestInterface = $requestInterface;
    }


    public function afterExecute($subject) {
        if ($this->requestInterface->getParam('filename') == 'magehit_storelocator') {
            $fileName = $this->requestInterface->getParam('filename') . '.csv';
            $moduleDir = $this->reader->getModuleDir('', 'Magehit_Storelocator');;
            $fileabtPath = $moduleDir . '/Files/Sample/' . $fileName;
            $directoryRead = $this->readFactory->create($moduleDir);
            $filePath = $directoryRead->getRelativePath($fileabtPath);

            if (!$directoryRead->isFile($filePath)) {
                $this->messageManager->addError(__('There is no sample file for this entity.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/import');
                return $resultRedirect;
            }

            $fileSize = isset($directoryRead->stat($filePath)['size'])
                ? $directoryRead->stat($filePath)['size'] : null;

            $this->fileFactory->create(
                $fileName,
                null,
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                $fileSize
            );
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setContents($directoryRead->readFile($filePath));
            return $resultRaw;
        }
    }

}
