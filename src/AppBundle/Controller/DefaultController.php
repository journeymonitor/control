<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\TestcaseHomepageForm;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\TestcaseAndUserType;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {

        return $this->render('AppBundle:default:index.html.twig', array('form' => $form->createView()));
    }
    
    private function generateUuid()
    {
        return uniqid();
    }
}
