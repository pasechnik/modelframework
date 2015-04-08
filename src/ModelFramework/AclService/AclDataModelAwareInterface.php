<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 8:44 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModelInterface;

interface AclDataAwareInterface
{
    /**
     * @param DataModelInterface $aclDataModel
     *
     * @return $this
     */
    public function setAclDataModel(DataModelInterface $aclDataModel);

    /**
     * @return DataModelInterface
     */
    public function getAclDataModel();

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclDataModelVerify();
}
