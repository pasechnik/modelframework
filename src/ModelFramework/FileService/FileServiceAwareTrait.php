<?php
/**
 * Class FileServiceAwareTrait
 * @package ModelFramework\FileService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FileService;

trait FileServiceAwareTrait
{
    /**
     * @var FileServiceInterface
     */
    private $_fileService = null;

    /**
     * @param FileServiceInterface $fileService
     *
     * @return $this
     */
    public function setFileService(FileServiceInterface $fileService)
    {
        $this->_fileService = $fileService;
    }

    /**
     * @return FileServiceInterface
     */
    public function getFileService()
    {
        return $this->_fileService;
    }

    /**
     * @return FileServiceInterface
     * @throws \Exception
     */
    public function getFileServiceVerify()
    {
        $_fileService = $this->getFileService();
        if ($_fileService == null || !$_fileService instanceof FileServiceInterface) {
            throw new \Exception('FileService does not set in the FileServiceAware instance of '.
                                  get_class($this));
        }

        return $_fileService;
    }
}
