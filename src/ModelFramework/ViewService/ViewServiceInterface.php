<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:26 PM
 */

namespace ModelFramework\ViewService;

interface ViewServiceInterface
{
    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function getView($viewName);

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function get($viewName);
}
