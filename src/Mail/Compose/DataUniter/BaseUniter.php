<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/17/14
 * Time: 8:44 PM
 */

namespace Mail\Compose\DataUniter;

class BaseUniter
{
    public function __construct($params = null)
    {
    }

    /**
     * return merged data array
     *
     * @param Array $newData
     * @param Array $oldData
     *
     * @return null|['text'=>[], 'header'=>[], 'info'=>[], 'attachment'=>[]]
     */
    public function uniteData($newData, $oldData)
    {
        return array_merge($newData, $oldData);
    }
}
