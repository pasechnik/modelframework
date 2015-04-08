<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 10:51 AM
 */

namespace ModelFramework\FieldTypesService;

interface FieldTypesServiceInterface
{
    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getInputFilter($type);

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getField($type);

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getFormElement($type);

    /**
     * @param string $modelName
     *
     * @return mixed
     */
    public function getUtilityFields($modelName = '');

    /**
     * @param string $type
     * @param string $part
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldPart($type, $part);
}
