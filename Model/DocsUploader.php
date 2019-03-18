<?php

namespace Tunik\Info\Model;

use Tunik\Info\Model\FileInfo;
use Magento\Framework\App\ObjectManager;

/**
 * Docs uploader
 */
class DocsUploader
{
    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    private $coreFileStorageDatabase;

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Base tmp path
     *
     * @var string
     */
    public $baseTmpPath;

    /**
     * Base path
     *
     * @var string
     */
    public $basePath;

    /**
     * Allowed extensions
     *
     * @var string
     */
    public $allowedExtensions;

    // private $fileInfo;

    /**
     * DocsUploader constructor
     *
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $baseTmpPath
     * @param string $basePath
     * @param string[] $allowedExtensions
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        $baseTmpPath = "info/tmp/doc",
        $basePath = "info/doc",
        $allowedExtensions= ['pdf', 'doc', 'rtf', 'tiff'],
        \Tunik\Info\Model\FileInfo $fileInfo
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = $baseTmpPath;
        $this->basePath = $basePath;
        $this->allowedExtensions= $allowedExtensions;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Set base tmp path
     *
     * @param string $baseTmpPath
     *
     * @return void
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    /**
     * Set base path
     *
     * @param string $basePath
     *
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Set allowed extensions
     *
     * @param string[] $allowedExtensions
     *
     * @return void
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }

    /**
     * Retrieve base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Retrieve base path
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $docName
     *
     * @return string
     */
    public function getFilePath($path, $docName)
    {
        return rtrim($path, '/') . '/' . ltrim($docName, '/');
    }

    /**
     * Checking file for moving and move it
     *
     * @param string $docName
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveFileFromTmp($docName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();
            
        $baseDocPath = $this->getFilePath($basePath, $docName);
        $baseTmpDocPath = $this->getFilePath($baseTmpPath, $docName);

        $newDocName = $docName;
        if ($docName)
        {
            try 
            {
                $newDocName = $this->getUniqueDocName($basePath, $docName);
                
                $baseDocPath = $this->getFilePath($basePath, $newDocName);

                if ($this->getFileInfo()->isExist($docName, $this->baseTmpPath)) 
                {
                    $this->coreFileStorageDatabase->copyFile(
                        $baseTmpDocPath,
                        $baseDocPath
                    );
                    $this->mediaDirectory->renameFile(
                        $baseTmpDocPath,
                        $baseDocPath
                    );
                }
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while moving the file(s).')
                );
            }
        }

        return $newDocName;
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
    
    /**
     * Checking file for save and save it to tmp dir
     *
     * @param string $fileId
     *
     * @return string[]
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();

        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        $uploader->setAllowCreateFolders(true);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));

        unset($result['path']);
        
        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }
    
    /**
     * Delete the doc name
     *
     * @param string $docName
     * @param string $type
     *
     * @return void
     */
    public function deleteDoc($docName, $type) // = 'dir'
    {
        $basePath = $this->getBasePath();
        if ($type == 'tmp') {
            $basePath = $this->getBaseTmpPath();
        }

        if ($this->getFileInfo()->isExist($docName, $basePath)) {
            $this->getFileInfo()->deleteFile($docName, $basePath);
        }
    }
    
    public function deleteTempFiles()
    {
        $files = $this->getFileInfo()->getFiles($this->getBaseTmpPath() . '/*');

        foreach($files as $file)
        { 
            if(is_file($file))
            unlink($file); 
        }
        return true;
    }

    public function getUniqueDocName($directory, $filename)
    {
        if ($this->getFileInfo()->isExist($filename, $directory)) {
            $index = 1;
            $extension = strrchr($filename, '.');
            $filenameWoExtension = substr($filename, 0, -1 * strlen($extension));
            while ($this->getFileInfo()->isExist($filenameWoExtension . '_' . $index . $extension, $directory)) {
                $index++;
            }
            $filename = $filenameWoExtension . '_' . $index . $extension;
        }
        return $filename;
    }
}
