<?php

namespace Wepo\Lib;

use ModelFramework\ModelService\ModelServiceAwareTrait;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PdoGateway extends TableGateway implements ServiceLocatorAwareInterface
{
    use ModelServiceAwareTrait;

    protected $serviceLocator = null;

    public function __construct($table, AdapterInterface $adapter, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null)
    {
        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
    }

    /**
     * Set serviceManager instance
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Retrieve serviceManager instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }
        $result  = parent::initialize();
        if (($object  = $this->resultSetPrototype->getArrayObjectPrototype()) instanceof WepoModel &&
            method_exists($object, 'getFieldNames') && is_array($columns = $object->getFieldNames())) {
            $this->columns = $columns;
        }

        return $result;
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $resultSet = $this->select();

        return $resultSet;
    }

    /**
     * @param  string    $id
     * @return WepoModel
     */
    public function get($id)
    {
        $id     = (int) $id;
        $rowset = $this->select(array( 'id' => $id ));
        $row    = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    /**
     * @param  array     $where
     * @return WepoModel
     */
    public function findOne($where)
    {
        $row = $this->select($where)->current();
        if (!$row) {
            throw new \Exception("Could not find row $where ");
        }

        return $row;
    }

    protected function assemble($fields = array(), $orders = array(), $limit = null, $offset = null)
    {
    }

    public function gather($fields = array(), $orders = array(), $limit = null, $offset = null)
    {
        if (!is_array($fields)) {
            throw new \Exception("Wrong input type - not error !");
        }

        $sql   = $this->getSql();
        $model = $this->getResultSetPrototype()->getArrayObjectPrototype();

        $_fieldmap = [ ];
        foreach (array_keys($model->getFields()) as $_field) {
            $_fieldmap[ $_field ] = $sql->getTable().'.'.$_field;
        }

        $select = $this->getSql()->select();

        foreach ($model->getJoins() as $_k => $_join) {
            $_on         = '';
//            $_class      = 'Wepo\Model\\' . $_join[ 'model' ];
//            $_table      = $_class::TABLE_NAME;
            $_jmodel     = $this->getServiceLocator()->get('Wepo\Lib\GatewayService')->getModel($_join[ 'model' ]);
            $_table      = $_jmodel->table_name;
            $_tablealias = $_table.$_k;
            foreach ($_join[ 'on' ] as $_key => $_value) {
                if (strlen($_on)) {
                    $_on .= ' AND ';
                }
                $_on .= $sql->getTable().'.'.$_key.' = '.$_tablealias.'.'.$_value;
            }
//         $select -> join( 'user', 'lead.owner_id = user.id', array( 'owner_login' => 'login' ) );
            foreach ($_join[ 'fields' ] as $_alias => $_field) {
                $_fieldmap[ $_alias ] = $_tablealias.'.'.$_field;
            }
            $select->join([ $_tablealias => $_table ], $_on, $_join[ 'fields' ], \Zend\Db\Sql\Select::JOIN_LEFT);
        }

        foreach ($fields as $_k => $_value) {
            if ($_k{0} == '-') {
                if (count($_value)) {
                    $select->where(new \Zend\Db\Sql\Predicate\NotIn($_fieldmap[ substr($_k, 1) ], is_array($_value) ? $_value : [ $_value ]));
                }
            } else {
                if (!count($_value)) {
                    return false;
                }
                $select->where([$_fieldmap[ $_k ] => $_value ]);
            }
        }

        if (count($orders)) {
            $_ord = [ ];
            foreach ($orders as $_k => $_value) {
                $_fieldname = is_string($_k) ? $_k : $_value;
                $_keyname   = $_k;
                $_keyvalue  = $_value;

                if (isset($_fieldmap[ $_fieldname ])) {
                    $_keyname  = is_string($_k) ? $_k : $_fieldmap[ $_fieldname ];
                    $_keyvalue = is_string($_k) ? $_value : $_fieldmap[ $_fieldname ];
                }

                $_ord[ $_keyname ] = $_keyvalue;
            }
            $select->order($_ord);
        }
        if ($limit !== null) {
            $select->limit($limit);
        }
        if ($offset !== null) {
            $select->offset($offset);
        }

        $rowset = $this->selectWith($select);

        return $rowset;
    }

    /**
     * @param  array                        $fields
     * @param  array                        $orders
     * @param  null|int                     $limit
     * @param  null|int                     $offset
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws Exception\RuntimeException
     */
    public function find($fields = array(), $orders = array(), $limit = null, $offset = null)
    {
        if (!is_array($fields)) {
            throw new \Exception("Wrong input type - not error !");
        }
        $select = $this->getSql()->select();

        $antiFields = [ ];
        foreach ($fields as $key => $value) {
            if ($key{0} == '-') {
                if (count($value)) {
                    $select->where(new \Zend\Db\Sql\Predicate\NotIn(substr($key, 1), is_array($value) ? $value : [$value ]));
                }
            } else {
                if (!count($value)) {
                    return false;
                }
                $select->where([$key => $value ]);
            }
        }

        if (count($orders)) {
            $select->order($orders);
        }
        if ($limit !== null) {
            $select->limit($limit);
        }
        if ($offset !== null) {
            $select->offset($offset);
        }

//        var_dump($select->);

        $rowset = $this->selectWith($select);

        return $rowset;
    }

    protected function isUnique(WepoModel $model)
    {
        foreach ($model->unique() as $_unique) {
            $_data = [ ];
            foreach ((array) $_unique as $_key) {
                $_data[ $_key ] = $model->$_key;
            }
            $check = $this->find($_data);
            if ($check->count() > 0 && $check->current()->id != $model->id) {
                return false;
            }
        }

        return true;
    }

    public function save(WepoModel $model)
    {
        $result = false;
        $data   = $model->toArray();
        $id     = (int) $model->id;

        if (!$this->isUnique($model)) {
            throw new \Exception('Data is not unique');
        }

        if ($id == 0) {
            $result = $this->insert($data);
        } else {
            if ($this->get($id)) {
                $result = $this->update($data, array( 'id' => $id ));
            } else {
                throw new \Exception('Model id does not exist');
            }
        }

        return $result;
    }

    public function getPages($fields = array(), $conditions = array(), $orders = array()/* $params = array( ) */)
    {
        $sql    = $this->getSql();
        $select = $sql->select();
        if (count($fields)) {
            $select->columns(array_unique($fields));
        }
        $_fieldmap = [ ];
        $model     = $this->getResultSetPrototype()->getArrayObjectPrototype();

        foreach (array_keys($model->getFields()) as $_field) {
            $_fieldmap[ $_field ] = $sql->getTable().'.'.$_field;
        }

        $_aliasmap = $model->aliasmap();

        foreach ($model->getJoins() as $_i => $_join) {
            $_on         = '';
//            $_class      = 'Wepo\Model\\' . $_join[ 'model' ];
//            $_table      = $_class::TABLE_NAME;
//            prn( $this -> getServiceLocator() );
//            exit();
            $_jmodel     = $this->getServiceLocator()->get('Wepo\Lib\GatewayService')->getModel($_join[ 'model' ]);
            $_table      = $_jmodel->table_name;
            $_tablealias = is_string($_i) ? $_i : $_table.$_i;

//            prn( $_join,  );

            foreach ($_join[ 'on' ] as $_key => $_value) {
                if (strlen($_on)) {
                    $_on .= ' AND ';
                }
                $_on .= $sql->getTable().'.'.$_key.' = '.$_tablealias.'.'.$_value;
            }
//         $select -> join( 'user', 'lead.owner_id = user.id', array( 'owner_login' => 'login' ) );
            foreach ($_join[ 'fields' ] as $_alias => $_field) {
                $_fieldmap[ $_alias ] = $_tablealias.'.'.$_field;
            }

//            prn( $_aliasmap, $_join[ 'fields' ] );

            $_bjoin = false;
            foreach (array_keys($_join[ 'fields' ]) as $_field) {
                $_src   = $_aliasmap[ $_field ];
                $_bjoin = in_array($_src, $fields);
                if ($_bjoin) {
                    break;
                }
            }
            if ($_bjoin) {
                $select->join([ $_tablealias => $_table ], $_on, $_join[ 'fields' ], \Zend\Db\Sql\Select::JOIN_LEFT);
            }
        }

        if (count($conditions)) {
            foreach ($conditions as $_k => $_value) {
                if (isset($_fieldmap[ $_k ])) {
                    $conditions[ $_fieldmap[ $_k ] ] = $_value;
                    unset($conditions[ $_k ]);
                }
            }
            $select->where($conditions);
        }

        if (count($orders)) {
            $_ord = [ ];
            foreach ($orders as $_k => $_value) {
                $_fieldname = is_string($_k) ? $_k : $_value;
                $_keyname   = $_k;
                $_keyvalue  = $_value;

                if (isset($_fieldmap[ $_fieldname ])) {
                    $_keyname  = is_string($_k) ? $_k : $_fieldmap[ $_fieldname ];
                    $_keyvalue = is_string($_k) ? $_value : $_fieldmap[ $_fieldname ];
                }

                $_ord[ $_keyname ] = $_keyvalue;
            }

            $select->order($_ord);
        }
        $adapter   = new \Zend\Paginator\Adapter\DbSelect($select, $sql, $this->getResultSetPrototype());
        $paginator = new \Zend\Paginator\Paginator($adapter);

        return $paginator;
    }
}
