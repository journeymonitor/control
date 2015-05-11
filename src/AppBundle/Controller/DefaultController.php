<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Testcase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\TestcaseType;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $testcase = new Testcase();
        $testcase->setCadence('*/15');
        $form = $this->createForm(new TestcaseType(), $testcase);
        $form->handleRequest($request);

        if ($form->isValid()) { // False if not submitted
            $testcase->setId($this->generateUuid());
            $testcase->setUserId('fake');
            $em = $this->getDoctrine()->getManager();
            $em->persist($testcase);
            $em->flush();
            $this->addFlash('success', 'Thank you. Your website will now be monitored.');
        }
        
        return $this->render('default/index.html.twig', array('form' => $form->createView()));
    }
    
    private function generateUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
