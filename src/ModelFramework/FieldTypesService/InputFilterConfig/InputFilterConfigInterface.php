<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:42
 */

namespace ModelFramework\FieldTypesService\InputFilterConfig;

interface InputFilterConfigInterface
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
