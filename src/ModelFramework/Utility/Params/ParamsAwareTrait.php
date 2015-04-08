<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 8/1/14
 * Time: 4:15 PM
 */

namespace ModelFramework\Utility\Params;

use ModelFramework\DataModel\DataModelInterface;
use Zend\Mvc\Controller\Plugin\Params;

trait ParamsAwareTrait
{

    private $_params = null;
    private $_paramSource = null;

    /**
     * @param DataModelInterface $data
     *
     * @return $this
     */
    public function setParamSource(DataModelInterface $data)
    {
        $this->_paramSource = $data;

        return $this;
    }

    /**
     * @return DataModelInterface
     */
    public function getParamSource()
    {
        return $this->_paramSource;
    }

    /**
     * @param Params $params
     *
     * @return $this
     */
    public function setParams(Params $params)
    {
        $this->_params = $params;

        return $this;
    }

    /**
     * @return Params
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return Params
     * @throws \Exception
     */
    public function getParamsVerify()
    {
        $params = $this->getParams();
        if ($params === null || !$params instanceof Params) {
            throw new \Exception('Params does not set in the ParamsAware instance of '
                . get_class($this));
        }

        return $params;
    }

    public function getParam($name, $default = '')
    {
        $param = $default;

        if ($this->getParamSource() !== null) {
            $params = $this->getParamSource();
            if (isset($params->$name)) {
                $param = $params->$name;
            }

            return $param;
        }

        $param = $this->getParamsVerify()->fromQuery($name, $default);
        if ($param === $default) {
            $param = $this->getParamsVerify()->fromRoute($name, $default);
        }

        if (is_array($default) && !is_array($param)) {
            $param = (array)$param;
        }

        return $param;
    }
}
