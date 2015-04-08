<?php
/**
 * Class AbstractObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class StaticObserver extends AbstractObserver
{
    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $where = [ ];
        foreach ($this->getRootConfig() as $field => $value) {
            $where[ $field ] = $value;
        }

        $subject->setWhere($where);
    }
}
