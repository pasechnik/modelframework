<?php
/**
 * Class AbstractObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\QueryService\Query;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

abstract class AbstractObserver
    implements \SplObserver, ConfigAwareInterface
{
    use ConfigAwareTrait, SubjectAwareTrait;

    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

//        $this->process( $subject, '$key', '$value' );
    }

//
//    /**
//     * @param $model
//     * @param $key
//     * @param $value
//     *
//     * @return mixed
//     */
//    abstract public function process( $model, $key, $value );
}
