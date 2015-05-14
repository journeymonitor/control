<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\TestcaseAndUserType;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
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
            $user = $this->getUser();

            if (!$user->hasRole('ROLE_USER')) {
                $result = $this->get('selenior.registration')->createUserOrLogin(
                    $form->get('user')->getData()
                );
                if ($result instanceof User) {
                    $event = new FormEvent($form, $request);
                    $this->get('event_dispatcher')->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                }
            }

            $this->get('selenior.testcase')->createTestcaseForUser(
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
