<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 8:48 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModelInterface;

trait AclDataModelAwareTrait
{
    /**
     * @var DataModelInterface
     */
    private $_aclDataModel = null;

    /**
     * @param DataModelInterface $aclDataModel
     *
     * @return $this
     */
    public function setAclDataModel(DataModelInterface $aclDataModel)
    {
        $this->_aclDataModel = $aclDataModel;

        return $this;
    }

    /**
     * @return DataModelInterface
     */
    public function getAclDataModel()
    {
        return $this->_aclDataModel;
    }

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclDataModelVerify()
    {
        $_aclDataModel = $this->getAclDataModel();
        if ($_aclDataModel == null || !$_aclDataModel instanceof DataModelInterface || $_aclDataModel->getModelName() !== 'Acl') {
            throw new \Exception('AclDataModel is not set in the AclDataAware instance of '.get_class($this));
        }

        return $_aclDataModel;
    }
}
