<?php
/**
 * Class FormatObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class FormatObserver extends AbstractObserver
{
    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $format = [];

        $config = $this->getRootConfig();

        foreach ($this->getRootConfigVerify() as $key => $value) {
            $format[ $key ] = $value;
        }

        $subject->setData([ 'format' => $format ]);
    }
}
