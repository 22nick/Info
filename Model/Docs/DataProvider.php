<?php
namespace Tunik\Info\Model\Docs;

use Tunik\Info\Model\ResourceModel\Docs\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Tunik\Info\Model\FileInfo;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    private $fileInfo;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->meta = $this->prepareMeta($this->meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        foreach ($items as $item) 
        {
            $this->loadedData[$item->getId()] = $item->getData();
            $fileName = $item->getFileLink();

            if ($this->getFileInfo()->isExist($fileName)) 
            {
                $stat = $this->getFileInfo()->getStat($fileName);

                $m['file_link'][0]['name'] = $fileName;
                $m['file_link'][0]['url'] = $this->getMediaUrl().$fileName;
                $m['file_link'][0]['size'] = isset($stat) ? $stat['size'] : 0;
                $fullData = $this->loadedData;
                $this->loadedData[$item->getId()] = array_merge($fullData[$item->getId()], $m);
            }
        }

        $data = $this->dataPersistor->get('tunik_info_docs');
        if (!empty($data)) {
            $item = $this->collection->getNewEmptyItem();
            $item->setData($data);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear('tunik_info_docs');
        }

        return $this->loadedData;
    }
    
    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'info/tmp/doc';
        return $mediaUrl;
    }
    
    /**
     * Get FileInfo instance
     *
     * @return FileInfo
     *
     * @deprecated 101.1.0
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }
}
