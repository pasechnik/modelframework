<?php
/**
 * Class ModelListAwareTrait
 *
 * @package ModelFramework\ModelService\ModelList
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelService\ModelList;

trait ModelListAwareTrait
{

    private $_modelList = null;

    /**
     * @param ModelList $modelList
     *
     * @return $this
     */
    public function setModelList( ModelList $modelList )
    {
        $this->_modelList = $modelList;

        return $this;
    }

    /**
     * @return ModelList
     */
    public function getModelList()
    {
        return $this->_modelList;
    }

    /**
     * @return ModelList
     * @throws \Exception
     */
    public function getModelConfigVerify()
    {
        $modelList = $this->getModelList();
        if ($modelList == null || !$modelList instanceof ModelList) {
            throw new \Exception( 'ModelList is not set in ' .
                                  get_class( $this ) );
        }

        return $modelList;
    }
}
