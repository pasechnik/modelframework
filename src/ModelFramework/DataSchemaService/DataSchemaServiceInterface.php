<?php
/**
 * Interface DataSchemaServiceInterface
 * @package ModelFramework\DataSchemaService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataSchemaService;

interface DataSchemaServiceInterface
{
    /**
     * @param $modelName
     *
     * @return DataSchema
     * @throws \Exception
     */
    public function getDataSchema($modelName);
}
