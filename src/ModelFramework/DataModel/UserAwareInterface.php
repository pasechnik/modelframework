<?php

namespace ModelFramework\DataModel;

interface UserAwareInterface
{
    /**
     * @param DataModelInterface $user
     *
     * @return $this
     */
    public function setUser(DataModelInterface $user);

    /**
     * @return DataModelInterface
     */
    public function getUser();

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getUserVerify();
}
