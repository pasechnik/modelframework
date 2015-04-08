<?php
/**
 * Class SubjectAwareTrait
 * @package ModelFramework\Utility\SplSubject
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\Utility\SplSubject;

trait SubjectAwareTrait
{
    /**
     * @var SplSubject
     */
    private $_subject = null;

    /**
     * @param \SplSubject $subject
     *
     * @return $this
     */
    public function setSubject(\SplSubject $subject)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * @return SplSubject
     */
    public function getSubject()
    {
        return $this->_subject;
    }
}
