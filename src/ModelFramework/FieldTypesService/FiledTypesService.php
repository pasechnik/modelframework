<?php

/**
 * Class FieldTypesService
 *
 * @package ModelFramework\FieldTypesService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FieldTypesService;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;

class FiledTypesService
    implements FieldTypesServiceInterface, ConfigAwareInterface
{

    use ConfigAwareTrait;

    /**
     * @param string $type
     * @param string $part
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldPart( $type, $part )
    {
        return $this->getConfigDomainPart( 'fieldTypes', $type, $part );

//        $_config = $this->getConfigPart( $type );
//        if ( !isset( $_config[ $part ] ) )
//        {
//            throw new \Exception( 'Unknown type "' . $type . '" for ' .  $part );
//        }
//
//        return $_config[ $part ];
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getInputFilter( $type )
    {
        return $this->getFieldPart( $type, 'filter' );
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getField( $type )
    {
        return $this->getFieldPart( $type, 'field' );
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getFormElement( $type )
    {
        return $this->getFieldPart( $type, 'element' );
    }

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getUtilityFields( $modelName = '' )
    {
        return [
            'fields'  => [
                '_id'  => [
                    'type'      => 'pk',
                    'fieldtype' => '_id',
                    'datatype'  => 'string',
                    'default'   => '',
                    'label'     => 'ID',
                    'source'    => '_id',
                ],
                '_acl' => [
                    'type'      => 'field',
                    'fieldtype' => 'array',
                    'datatype'  => 'array',
                    'default'   => [ ],
                    'label'     => 'acl',
                    'source'    => '_acl',
                ],
            ],
            'filters' => [ '_id' => $this->getInputFilter( 'text' ) ],
        ];
    }
}
