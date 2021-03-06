<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:42
 */

namespace ModelFramework\FieldTypesService\FormElementConfig;

interface FormElementConfigInterface
{

    /**
     * @param array $data
     *
     * @return array
     */
    public function exchangeArray( array $data );

    /**
     * @return array
     */
    public function toArray();
}
