<?php

namespace PaulMaxwell\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PaulMaxwellBlogBundle:Default:index.html.twig', array());
    }
}
