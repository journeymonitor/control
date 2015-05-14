<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Testcase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\TestcaseAndUserType;

class RegistrationController extends Controller
{
    public function completeTestcaseCreationAction(Request $request)
    {
        $testcase = new Testcase();
        $testcase->setTitle($request->get('testcase_title'));
        $form = $this->createForm(new TestcaseAndUserType(), $testcase);

        if ($form->isValid()) { // False if not submitted
            $testcase->setId($this->generateUuid());
            $testcase->setUser('fake');
            $em = $this->getDoctrine()->getManager();
            $em->persist($testcase);
            $em->flush();
            $this->addFlash('success', 'Thank you. Your website will now be monitored.');
        }
        
        return $this->render('registration/completeTestcaseCreation.html.twig', array('form' => $form->createView()));
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
