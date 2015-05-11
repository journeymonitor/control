<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\TestcaseModel;
use AppBundle\Form\Type\TestcaseType;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $testcaseModel = new TestcaseModel();
        $form = $this->createForm(new TestcaseType(), $testcaseModel);
        $form->handleRequest($request);
        
        if ($form->isValid()) { // False if not submitted
            $this->addFlash('success', 'Thank you. Your website will now be monitored.');
        }
        
        return $this->render('default/index.html.twig', array('form' => $form->createView()));
    }
}
