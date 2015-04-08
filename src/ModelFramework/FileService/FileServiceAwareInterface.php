<?php
/**
 * Class FileServiceAwareInterface
 * @package ModelFramework\FileService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FileService;

interface FileServiceAwareInterface
{
    /**
     * @param FileServiceInterface $fileService
     *
     * @return $this
     */
    public function setFileService(FileServiceInterface $fileService);

    /**
     * @return FileServiceInterface
     */
    public function getFileService();

    /**
     * @return FileServiceInterface
     * @throws \Exception
     */
    public function getFileServiceVerify();
}
