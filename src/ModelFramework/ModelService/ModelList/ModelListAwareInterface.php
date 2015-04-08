<?php
/**
 * Class ModelListAwareInterface
 *
 * @package ModelFramework\ModelService\ModelList
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelService\ModelList;

interface ModelListAwareInterface
{

    /**
     * @param ModelList $modelList
     *
     * @return $this
     */
    public function setModelList( ModelList $modelList );

    /**
     * @return ModelList
     */
    public function getModelList();

    /**
     * @return ModelList
     * @throws \Exception
     */
    public function getModelListVerify();
}
