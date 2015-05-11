<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Testcase;
use AppBundle\Entity\TestcaseHomepageForm;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\TestcaseHomepageFormType;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $testcaseHomepageForm = new TestcaseHomepageForm();
        $testcaseHomepageForm->setCadence('*/15');
        $form = $this->createForm(new TestcaseHomepageFormType(), $testcaseHomepageForm);
        $form->handleRequest($request);

        if ($form->isValid()) { // False if not submitted
            $salt = sha1(mt_rand(0, PHP_INT_MAX));
            
            $user = new User();
            $user->setId($this->generateUuid());
            $user->setEmail($testcaseHomepageForm->getNotifyEmail());
            $user->setPassword(sha1($salt.$testcaseHomepageForm->getPassword()));
            $user->setSalt($salt);

            $testcase = new Testcase();
            $testcase->setId($this->generateUuid());
            $testcase->setUserId($user->getId());
            $testcase->setTitle($testcaseHomepageForm->getTitle());
            $testcase->setNotifyEmail($testcaseHomepageForm->getNotifyEmail());
            $testcase->setCadence($testcaseHomepageForm->getCadence());
            $testcase->setScript($testcaseHomepageForm->getScript());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
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
