<?php

namespace ModelFramework\AclService;

use ModelFramework\AclService\AclConfig\AclConfig;
use ModelFramework\AclService\AclConfig\AclConfigAwareInterface;
use ModelFramework\AclService\AclConfig\AclConfigAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\DataModel\UserAwareInterface;
use ModelFramework\DataModel\UserAwareTrait;

class AclDataModel implements DataModelInterface, DataModelAwareInterface,
                              AclConfigAwareInterface, UserAwareInterface
{

    use DataModelAwareTrait, AclConfigAwareTrait, UserAwareTrait;

    private $mixedAclData = null;

    public function __clone()
    {
        $this->setDataModel( clone $this->getDataModel() );
        $this->setAclConfig( clone $this->getAclConfig() );
    }

    public function merge( $data )
    {
        $this->getDataModelVerify()->merge( $data );

        return $this;
    }

    public function split( $data )
    {
        return $this->getDataModelVerify()->split( $data );
    }

    public function __call( $name, $arguments )
    {
        return $this->getDataModelVerify()->__call( $name, $arguments );
    }

    public function __get( $name )
    {
        $aclData = $this->getDataPermissions();
        if (in_array( $name,
            [ '_model', '_label', '_adapter', '_acl', 'id', '_id' ] )) {
            return $this->getDataModelVerify()->{$name};
        }

        if (!in_array( 'read', $aclData->data )
        ) {
            throw new \Exception( 'reading is not allowed' );
        }
        if (empty( $aclData->fields[ $name ] )) {
            return 'denied';
        }
        if ($aclData->fields[ $name ] == 'x') {
            return 'reading is not allowed';
        }

        return $this->getDataModelVerify()->__get( $name );
    }

    public function __set( $name, $value )
    {
        $aclData = $this->getDataPermissions();
        if (in_array( $name,
            [ 'id', '_id' ] )) {
            return $this->getDataModelVerify()->__set( '_id', $value );
        }
        if (!in_array( 'write', $aclData->data )
        ) {
            throw new \Exception( 'writing is not allowed' );
        }
        if (empty( $aclData->fields[ $name ] )) {
            return 'denied';
        }
        if ($aclData->fields[ $name ] == 'x') {
            return 'reading is not allowed';
        }
        if ($aclData->fields[ $name ] !== 'write') {
            return 'writing is not allowed';
        }

        return $this->getDataModelVerify()->__set( $name, $value );
    }

    public function __isset( $name )
    {
        return $this->getDataModelVerify()->__isset( $name );
    }

    public function __unset( $name )
    {
        return $this->getDataModelVerify()->__unset( $name );
    }

    public function exchangeArray( array $data )
    {
        $this->getDataModelVerify()->exchangeArray( $data );

        return $this;
    }

    public function getArrayCopy()
    {
        $data = [ ];
        foreach ($this->getFields() as $_field => $_properties) {
            if ($_field !== '_acl') {
                $data[ $_field ] = $this->{$_field};
            }
        }

        return $data;
//        return $this->getDataModelVerify()->getArrayCopy();
    }

    public function getModelName()
    {
        return $this->getDataModelVerify()->getModelName();
    }

    public function getTableName()
    {
        return $this->getDataModelVerify()->getTableName();
    }

    public function toArray()
    {
        return $this->getDataModelVerify()->toArray();
    }

    public function getFields()
    {
        return $this->getDataModelVerify()->getFields();
    }

    /**
     * @return AclConfig
     * @throws \Exception
     */
    public function getDataPermissions()
    {
        if ($this->mixedAclData !== null) {
            return $this->mixedAclData;
        }

        $this->mixedAclData = $this->getAclConfig();

        $user     = $this->getUser();
        $modelAcl = $this->getDataModelVerify()->_acl;
        foreach ($modelAcl as $acl) {
            if ($acl[ 'role_id' ] == (string) $user->id()
                || $acl[ 'role_id' ] == (string) $user->role_id
            ) {
                foreach ($acl[ 'data' ] as $data) {
                    if (!in_array( $data, $this->mixedAclData->data )) {
                        $this->mixedAclData->data[ ] = $data;
                    }
                }
            }
        }
        return $this->mixedAclData;
    }
}
