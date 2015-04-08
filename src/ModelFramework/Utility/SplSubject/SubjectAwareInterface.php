<?php
/**
 * Class SubjectAwareTrait
 * @package ModelFramework\Utility\SplSubject
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\Utility\SplSubject;

interface SubjectAwareInterface
{
    /**
     * @param \SplSubject $subject
     *
     * @return $this
     */
    public function setSubject(\SplSubject $subject);

    /**
     * @return SplSubject
     */
    public function getSubject();
}
