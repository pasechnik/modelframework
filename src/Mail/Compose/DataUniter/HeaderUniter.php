<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/17/14
 * Time: 8:44 PM
 */

namespace Mail\Compose\DataUniter;

class HeaderUniter extends BaseUniter
{
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
        //        prn('uniter', $newData, $oldData);
        return $newData;
    }
}
