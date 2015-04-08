<?php
/**
 * Class FileServiceAwareTrait
 * @package ModelFramework\FileService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FilesystemService;

trait FilesystemServiceAwareTrait
{
    /**
     * @var FilesystemServiceInterface
     */
    private $_filesystemService = null;

    /**
     * @param FilesystemServiceInterface $filesystemService
     *
     * @return $this
     */
    public function setFilesystemService(FilesystemServiceInterface $filesystemService)
    {
        $this->_filesystemService = $filesystemService;
    }

    /**
     * @return FilesystemServiceInterface
     */
    public function getFilesystemService()
    {
        return $this->_filesystemService;
    }

    /**
     * @return FilesystemServiceInterface
     * @throws \Exception
     */
    public function getFilesystemServiceVerify()
    {
        $_filesystemService = $this->getFilesystemService();
        if ($_filesystemService == null || !$_filesystemService instanceof FilesystemServiceInterface) {
            throw new \Exception('FileService does not set in the FileServiceAware instance of '.
                                  get_class($this));
        }

        return $_filesystemService;
    }
}
