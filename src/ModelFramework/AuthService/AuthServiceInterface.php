<?php
namespace ModelFramework\AuthService;

use ModelFramework\DataModel\DataModelInterface;

interface AuthServiceInterface
{
    public function init();

    public function setUser(DataModelInterface $user);
    public function getUser();
    public function setMainUser(DataModelInterface $user);
    public function getMainUser();
    public function checkAuth();
}
