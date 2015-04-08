<?php
/**
 * Class SignOutObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\AclService\AclDataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use ModelFramework\ViewService\View;
use Wepo\Form\SignInForm;
use Wepo\Model\Status;

class SignOutObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{

    use ConfigAwareTrait, SubjectAwareTrait;

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $subject->getAuthServiceVerify()->cleanUsers();
    }
}
