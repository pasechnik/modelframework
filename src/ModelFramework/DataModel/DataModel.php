<?php

namespace ModelFramework\DataModel;

use ModelFramework\ModelService\ModelConfig\ParsedModelConfig;

/**
 * Class DataModel
 * @package ModelFramework\DataModel
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class DataModel implements DataModelInterface
{

    public $_model = '';
    public $_table = '';
    public $_label = '';
    public $_adapter = '';
    protected $_fields = [ ];
    public $_data = [ ];

    public function __construct( $data = [ ] )
    {
        $this->exchangeArray( $data );
    }

    public function setParsedModelConfig( ParsedModelConfig $modelConfig )
    {
        $this->_fields  = $modelConfig->fields;
        $this->_model   = $modelConfig->model;
        $this->_table   = $modelConfig->table;
        $this->_label   = $modelConfig->label;
        $this->_adapter = $modelConfig->adapter;
        $this->exchangeArray( [ ] );
        return $this;
    }

    public function getModelName()
    {
        return $this->_model;
    }

    public function getTableName()
    {
        if (!empty( $this->_table )) {
            return $this->_table;
        }

        return $this->_model;
    }

    public function exchangeArray( array $data )
    {

        if (!isset( $this->_fields )) {
            throw new \Exception( ' _fields property is not set in ' .
                                  get_class() );
        }
        foreach ($this->_fields as $_field => $_properties) {
            if (isset( $data[ $_field ] )) {
                $this->$_field = $data[ $_field ];
            } else {
                $this->$_field = isset( $_properties[ 'default' ] ) ?
                    $_properties[ 'default' ] : null;
            }
            if (isset( $_properties[ 'datatype' ] ) && $this->$_field !== null
            ) {
                if ($this->$_field instanceof \MongoId) {
                } elseif ($this->$_field instanceof \MongoDate) {
                    $this->$_field =
                        date( $_properties[ 'datatype' ] == 'date' ? 'Y-m-d' :
                            'Y-m-d h:i:s', $this->$_field->sec );
                } elseif (!$_properties[ 'datatype' ] == 'date' &&
                          !$_properties[ 'datatype' ] == 'datetime'
                ) {
                    settype( $this->$_field, $_properties[ 'datatype' ] );
                }
            }
        }

        return $this;
    }

    public function merge( $data )
    {
        if (!isset( $this->_fields )) {
            throw new \Exception( ' _fields property is not set in ' .
                                  get_class() );
        }
        if (is_object( $data )) {
            $data = get_object_vars( $data );
        }
        if (!is_array( $data )) {
            throw new \Exception( ' Array or object expected ' );
        }
        foreach (array_keys( $this->_fields ) as $_field) {
            if (isset( $data[ $_field ] )) {
                $this->$_field = $data[ $_field ];
            }
        }

        return $this;
    }

    private function values( $data )
    {
        static $results = [ ];
        if (is_object( $data )) {
            if (method_exists( $data, 'toArray' )) {
                $data = array_keys( $data->toArray() );
            } else {
                $data = array_keys( get_object_vars( $data ) );
            }
        }
        if (is_array( $data )) {
            foreach ($data as $_v) {
                $this->values( $_v );
            }
        } elseif (!isset( $results[ $data ] )) {
            $results[ $data ] = $data;
        }

        return $results;
    }

    public function split( $validationGroup )
    {
        $fields = $this->values( $validationGroup );
        $data   = $this->_data;
        foreach ($fields as $_v) {
            unset( $data[ $_v ] );
        }

        return $data;
    }

    public function getArrayCopy()
    {
        $data = [ ];
        foreach ($this->_fields as $_field => $_properties) {
            $data[ $_field ] = $this->{$_field};
        }

        $this->_data = $data;

        return $data;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function toArray()
    {
        $data = $this->getArrayCopy();
        unset( $data[ '_id' ] );

        return $data;
    }

    public function __set( $name, $value )
    {
//        prn($name, $value);
        if (isset( $this->_fields[ $name ] )) {
            $this->_data[ $name ] = $value;
            if (isset( $this->_fields[ $name ][ 'datatype' ] ) &&
                $value !== null
            ) {
//                prn($name, $this->_data[ $name ],$this->_fields[$name]['datatype'] );
                settype( $this->_data[ $name ],
                    $this->_fields[ $name ][ 'datatype' ] );
            }

            return $this->_data[ $name ];
        } else {
            $this->$name = $value;

            return $this->$name;
        }
    }

    public function __get( $name )
    {
        if (array_key_exists( $name, $this->_data )) {
            return $this->_data[ $name ];
        }
        if ($name == 'id') {
            return (string) $this->_id;
        }

        return;
    }

    public function __call( $name, $arguments )
    {
        if (!isset( $this->_fields[ $name ] )) {
            if ($name == 'id') {
                return (string) $this->_id;
            }
            throw new \Exception( " Missed property '$name' in model {$this->getModelName()}" );
        }
        if (count( $arguments ) == 0) {
            return $this->_data[ $name ] ?:
                $this->_fields[ $name ][ 'default' ];
        }
        $_result = [ ];
        foreach ($arguments as $value) {
            if (isset( $this->_fields[ $name ][ 'datatype' ] ) &&
                $value !== null
            ) {
                settype( $value, $this->_fields[ $name ][ 'datatype' ] );
            }
            $_result[ ] = $value;
        }
        if (count( $_result ) == 1) {
            $_result = array_shift( $_result );
        }

        return $_result;
    }

    public function __isset( $name )
    {
        return isset( $this->_data[ $name ] );
    }

    public function __unset( $name )
    {
        unset( $this->_data[ $name ] );
    }

//    /**
//     * @return AclConfig
//     * @throws \Exception
//     */
//    public function getDataPermissions()
//    {
//        return null;
//    }

}
