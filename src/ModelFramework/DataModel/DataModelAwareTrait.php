<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 4:14 PM
 */

namespace ModelFramework\DataModel;

trait DataModelAwareTrait
{
    /**
     * @var DataModelInterface
     */
    private $_dataModel = null;

    /**
     * @param DataModelInterface $dataModel
     *
     * @return $this
     */
    public function setDataModel(DataModelInterface $dataModel)
    {
        $this->_dataModel = $dataModel;

        return $this;
    }

    /**
     * @return DataModelInterface
     */
    public function getDataModel()
    {
        return $this->_dataModel;
    }

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getDataModelVerify()
    {
        $_dataModel = $this->getDataModel();
        if ($_dataModel == null || !$_dataModel instanceof DataModelInterface) {
            throw new \Exception('DataModel does not set in the DataModelAware instance '.get_class($this));
        }

        return $_dataModel;
    }
}
