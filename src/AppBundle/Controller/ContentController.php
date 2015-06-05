<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentController extends Controller
{
    public function imprintAction()
    {
        return $this->render('AppBundle:default:imprint.html.twig');
    }
}
