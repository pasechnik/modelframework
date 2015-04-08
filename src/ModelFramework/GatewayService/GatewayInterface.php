<?php

namespace ModelFramework\GatewayService;

use ModelFramework\DataModel\DataModelInterface;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\ResultSet\ResultSet;

interface GatewayInterface extends TableGatewayInterface
{

    /**
     * @param string $id
     *
     * @return DataModelInterface
     */
    public function get($id);

    /**
     * @param array $fields
     * @param array $orders
     * @param null  $limit
     * @param null  $offset
     *
     * @return ResultSet
     */
    public function find(
        $fields = [],
        $orders = [],
        $limit = null,
        $offset = null
    );

    /**
     * @return ResultSet
     */
    public function fetchAll();

    /**
     * @param DataModelInterface $model
     *
     * @return mixed
     */
    public function isUnique(DataModelInterface $model);

    /**
     * @param DataModelInterface $model
     *
     * @return mixed
     */
    public function save($model);
}
