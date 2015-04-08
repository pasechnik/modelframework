<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 10:52 AM
 */

namespace ModelFramework\FieldTypesService;

interface FieldTypesServiceAwareInterface
{
    /**
     * @param FieldTypesServiceInterface $fieldTypesService
     *
     * @return $this
     */
    public function setFieldTypesService(FieldTypesServiceInterface $fieldTypesService);

    /**
     * @return FieldTypesServiceInterface
     */
    public function getFieldTypesService();

    /**
     * @return FieldTypesServiceInterface
     * @throws \Exception
     */
    public function getFieldTypesServiceVerify();
}
