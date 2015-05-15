<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\TestcaseAndUserType;
use AppBundle\Form\Type\TestcaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TestcasesController extends Controller
{
    public function newWithRegAction(Request $request)
    {
        $user = $this->getUser();
        if (!empty($user)) {
            return $this->redirect($this->get('router')->generate('selenior.testcase_new'));
        }

        $form = $this->createForm(new TestcaseAndUserType());
        $form->handleRequest($request);

        if ($form->isValid() && empty($user)) {
            $this->handleUserForm($form);
        }

        if ($form->isValid()) {
            $this->get('selenior.testcase')->createTestcaseForUser(
                $form->get('user')->getData(),
                $form->get('testcase')->getData()
            );

            $this->addFlash('success', 'Thank you. Your website will now be monitored.');
        }
        return $this->render('AppBundle:default:index.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(new TestcaseType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('selenior.testcase')->createTestcaseForUser(
                $this->getUser(),
                $form->getData()
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

    /**
     * @param Form $form
     */
    protected function handleUserForm(Form $form)
    {
        try {
            $this->get('selenior.registration')->createUserOrLogin(
                $form->get('user')->getData(),
                $form
            );
        } catch (AuthenticationException $ex) {
            $form->addError(new FormError('login failed'));
        }
    }
}
