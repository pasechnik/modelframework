<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 4:14 PM
 */

namespace ModelFramework\DataModel;

trait UserAwareTrait
{
    /**
     * @var DataModelInterface
     */
    private $_user = null;

    /**
     * @param DataModelInterface $user
     *
     * @return $this
     */
    public function setUser(DataModelInterface $user)
    {
        $this->_user = $user;

        return $this;
    }

    /**
     * @return DataModelInterface
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getUserVerify()
    {
        $_user = $this->getUser();
        if ($_user == null || !$_user instanceof DataModelInterface) {
            throw new \Exception('User does not set in the UserAware instance '.get_class($this));
        }

        return $_user;
    }
}
