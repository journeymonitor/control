<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\TestcaseAndUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TestcasesController extends Controller
{
    public function newAction(Request $request)
    {
        $form = $this->createForm(new TestcaseAndUserType());
        $form->handleRequest($request);

        if ($form->isValid()) { // False if not submitted
            $this->get('selenior.testcase')->createTestcaseAndUser(
                $form->get('user')->getData(),
                $form->get('testcase')->getData()
            );

            $this->addFlash('success', 'Thank you. Your website will now be monitored.');
        }
        return $this->render('AppBundle:default:index.html.twig', array('form' => $form->createView()));
    }

    public function disableAction($testcaseId)
    {
        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->find($testcaseId);
        $testcase->setEnabled(0);
        $em->flush();
        return new JsonResponse("The testcase '" . $testcase->getTitle() . "' has been disabled.");
    }

    public function enableAction($testcaseId)
    {
        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->find($testcaseId);
        $testcase->setEnabled(1);
        $em->flush();
        return new JsonResponse("The testcase '" . $testcase->getTitle() . "' has been enabled.");
    }
}
