<?php
/**
 * Class AgeObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class AgeObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        $model->$key = '';
        if (!isset($model->$value[ 'source' ])) {
            throw new \Exception('Field '.$value[ 'source' ].' does not exist in '.$model->getModelName());
        }
        if (!empty($model->$value[ 'source' ])) {
            $model->$key =
                $this->dateDifference(date('Y-m-d H:i:s'), $model->$value[ 'source' ], $value[ 'format' ]);
        }
    }

    //////////////////////////////////////////////////////////////////////
    //PARA: Date Should In YYYY-MM-DD Format
    //RESULT FORMAT:
    // '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
    // '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
    // '%m Month %d Day'                                            =>  3 Month 14 Day
    // '%d Day %h Hours'                                            =>  14 Day 11 Hours
    // '%d Day'                                                        =>  14 Days
    // '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
    // '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
    // '%h Hours                                                    =>  11 Hours
    // '%a Days                                                        =>  468 Days
    //////////////////////////////////////////////////////////////////////
    private function dateDifference($date_1, $date_2, $differenceFormat = '%a')
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);

        $interval = date_diff($datetime1, $datetime2);
        if($interval)
        {
            return $interval->format($differenceFormat);
        }
    }
}
