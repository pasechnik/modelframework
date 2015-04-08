<?php
/**
 * Class ListParamsServiceInterface
 * @package ModelFramework\ListParams
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ListParamsService;

interface ListParamsServiceInterface
{
    public function getListParams($hash);

    public function getHash($modelName, $params, $customParams = null);

    public function gatherParams($model,$modelName, $qparams);

    public function checkParams($model);
}
