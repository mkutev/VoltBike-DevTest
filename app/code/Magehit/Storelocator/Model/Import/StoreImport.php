<?php
namespace Magehit\Storelocator\Model\Import;

use Magehit\Storelocator\Model\Import\StoreImport\RowValidatorInterface as ValidatorInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;

class StoreImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{

    //const ID = 'storelocator_id';
    const NAME = 'store_name';
    const URL = 'store_url';
    const IMGURL = 'store_thumnail';
    const IN_STORE = 'in_store';
    const EMAIL = 'email';
    const WEB = 'website';
    const LAT = 'lat';
    const LNG = 'lng';
    const STREET = 'street';
    const CITY = 'city';
    const REGION = 'region';
    const PCODE = 'postcode';
    const COUNTRY = 'country';
    const PHONE = 'telephone';
    const FAX = 'fax';
    const CONTENT = 'content';
    const EN_SCHEDULE ='store_schedule';
    const SCHEDULE ='schedule';
    const STATUS = 'status';
    const PRD = 'product_ids';

    const TABLE_Entity = 'magehit_storelocator_storelocator';
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_URL_IS_EMPTY => 'URL is empty',
    ];
    protected $_permanentAttributes = [self:: URL];
    protected $needColumnCheck = true;
    protected $groupFactory;

    protected $validColumnNames = [
    // self:: ID ,
        self:: NAME,
        self:: URL,
        self:: IMGURL,
        self:: IN_STORE,
        self:: EMAIL,
        self:: WEB,
        self:: LAT,
        self:: LNG,
        self:: STREET,
        self:: CITY,
        self:: REGION,
        self:: PCODE,
        self:: COUNTRY,
        self:: PHONE,
        self:: FAX,
        self:: CONTENT,
        self:: EN_SCHEDULE,
        self:: SCHEDULE,
        self:: STATUS,
        self:: PRD
    ];

    protected $logInHistory = true;
    protected $_validators = [];
    protected $_connection;
    protected $_resource;
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Customer\Model\GroupFactory $groupFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->groupFactory = $groupFactory;
    }
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    public function getEntityTypeCode()
    {
        return 'magehit_storelocator';
    }

    public function validateRow(array $rowData, $rowNum)
    {
        $url = false;
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        if (isset($rowData[self:: URL]) || !empty($rowData[self:: URL])) {
            $url = $rowData[self:: URL];
            
        }
        if( $url == false){
            $this->addRowError(ValidatorInterface::ERROR_URL_IS_EMPTY, $rowNum);
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->deleteEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }

        return true;
    }

    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    public function deleteEntity()
    {
        $listUrl = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowUrl = $rowData[self:: URL];
                    $listUrl[] = $rowUrl;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listUrl) {
            $this->deleteEntityFinish(array_unique($listUrl),self::TABLE_Entity);
        }
        return $this;
    }

    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listUrl = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_URL_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowUrl= $rowData[self:: URL];
                $listUrl[] = $rowUrl;
                $entityList[$rowUrl][] = [
                    //self:: ID => $rowData[self:: URL],
                    self:: NAME => $rowData[self::NAME],
                    self:: URL => $rowData[self::URL],
                    self:: IMGURL => $rowData[self::IMGURL],
                    self:: IN_STORE => $rowData[self::IN_STORE],
                    self:: EMAIL => $rowData[self::EMAIL],
                    self:: WEB => $rowData[self::WEB],
                    self:: LAT => $rowData[self::LAT],
                    self:: LNG => $rowData[self::LNG],
                    self:: STREET => $rowData[self::STREET],
                    self:: CITY => $rowData[self::CITY],
                    self:: REGION => $rowData[self::REGION],
                    self:: PCODE => $rowData[self::PCODE],
                    self:: COUNTRY => $rowData[self::COUNTRY],
                    self:: PHONE => $rowData[self::PHONE],
                    self:: FAX => $rowData[self::FAX],
                    self:: CONTENT => $rowData[self::CONTENT],
                    self:: EN_SCHEDULE => $rowData[self::EN_SCHEDULE],
                    self:: SCHEDULE => $rowData[self::SCHEDULE],
                    self:: STATUS => $rowData[self::STATUS],
                    self:: PRD => $rowData[self::PRD]
                ];
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listUrl) {
                    if ($this->deleteEntityFinish(array_unique( $listUrl), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            }
        }
        return $this;
    }

    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $tableName = $this->_connection->getTableName($table);
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }
            if ($entityIn) {
                $this->_connection->insertOnDuplicate($tableName, $entityIn,[
               // self:: ID ,
                self:: NAME,
                self:: URL,
                self:: IMGURL,
                self:: IN_STORE,
                self:: EMAIL,
                self:: WEB,
                self:: LAT,
                self:: LNG,
                self:: STREET,
                self:: CITY,
                self:: REGION,
                self:: PCODE,
                self:: COUNTRY,
                self:: PHONE,
                self:: FAX,
                self:: CONTENT,
                self:: EN_SCHEDULE,
                self:: SCHEDULE,
                self:: STATUS,
                self:: PRD
                ]);
            }
        }
        return $this;
    }

}

?>