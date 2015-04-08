<?php

/**
 * Created by PhpStorm.
 * User: Leader
 * Date: 22.04.14
 * Time: 19:38
 */

namespace Wepo\View\Helper;

use Zend\Navigation\Page\AbstractPage;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Helper\Navigation\Menu as ZendMenu;

//use Zend\Mvc\Controller\AbstractActionController;

class Menu extends ZendMenu implements ServiceLocatorAwareInterface
{

    protected $user = null;

    public function htmlify(
        AbstractPage $page,
        $escapeLabel = true,
        $addClassToListItem = false
    ) {
        $str   = parent::htmlify($page, $escapeLabel, $addClassToListItem);
        $user  = $this->user();
        $route = ucfirst($page->getRoute());

        $str = preg_replace('@">(.*?)</a>@i',
            '"><span class="tooltip">\\1</span><span class="menu-text">\\1</span></a>',
            $str);
        $str = preg_replace('@">(.*?)</a>@i',
            '"><span class="menu icon"></span>\\1</a>', $str);
        if ($user->id() && isset($page->getParams()['data'])) {
            $param = ucfirst($page->getParams()['data']);
            if (isset($user->newitems[$param])
                && (int)$user->newitems[$param]
            ) {
                $str = preg_replace('/<\//',
                    '<span class="quantity">' . $user->newitems[$param]
                    . '</span></', $str);
            }
        }

        return $str;
    }

    public function user()
    {
        if ($this->user == null) {
            $this->user = $this->getServiceLocator()->getServiceLocator()
                ->get('\ModelFramework\AuthService')->getUser();
        }

        return $this->user;
    }
}
