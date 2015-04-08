<?php
/**
 * Interface DataSchemaServiceAwareInterface
 * @package ModelFramework\DataSchemaService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataSchemaService;

interface DataSchemaServiceAwareInterface
{
    /**
     * @param DataSchemaServiceInterface $dataSchemaService
     *
     * @return $this
     */
    public function setDataSchemaService(DataSchemaServiceInterface $dataSchemaService);

    /**
     * @return DataSchemaServiceInterface
     */
    public function getDataSchemaService();

    /**
     * @return DataSchemaServiceInterface
     * @throws \Exception
     */
    public function getDataSchemaServiceVerify();
}
