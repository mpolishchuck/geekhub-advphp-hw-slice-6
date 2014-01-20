<?php

namespace PaulMaxwell\BlogAdminBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FOSUserBundleListener
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onResettingResetSuccess(FormEvent $event)
    {
        $url = $this->router->generate('sonata_admin_redirect');
        $event->setResponse(new RedirectResponse($url));
    }
}
