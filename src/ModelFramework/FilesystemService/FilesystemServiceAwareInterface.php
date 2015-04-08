<?php
/**
 * Class FileServiceAwareInterface
 * @package ModelFramework\FileService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FilesystemService;

interface FilesystemServiceAwareInterface
{
    /**
     * @param FilesystemServiceInterface $filesystemService
     *
     * @return $this
     */
    public function setFilesystemService(FilesystemServiceInterface $filesystemService);

    /**
     * @return FilesystemServiceInterface
     */
    public function getFilesystemService();

    /**
     * @return FilesystemServiceInterface
     * @throws \Exception
     */
    public function getFilesystemServiceVerify();
}
