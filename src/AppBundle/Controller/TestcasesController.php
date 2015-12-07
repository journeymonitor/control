<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\TestcaseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use AppBundle\Form\Type\TestcaseAndUserType;

class TestcasesController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = $this
            ->get('demo_service')
            ->getUser($request, $this->getUser());

        if (empty($user)) {
            $this->addFlash('error', 'Access denied.');
            return $this->redirect($this->get('router')->generate('homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcases = $testcaseRepo->findBy(['user' => $user], ['enabled' => 'DESC']);

        return $this->render(
            'AppBundle:testcases:index.html.twig',
            array(
                'testcases' => $testcases,
                'isDemoMode' => $this->get('demo_service')->isDemoMode($request)
            )
        );
    }

    public function newWithRegAction(Request $request)
    {
        $user = $this->getUser();
        if (!empty($user)) {
            return $this->redirect($this->get('router')->generate('testcases.index'));
        }

        $form = $this->createForm(TestcaseAndUserType::class);
        $form->handleRequest($request);

        if ($form->isValid() && empty($user)) {
            try {
                $this->createOrLoginUserFromForm($form);
            } catch (AuthenticationException $ex) {
                $this->addFlash('error', 'This e-mail/password combination is incorrect.');
                return $this->render('AppBundle:default:index.html.twig', array('form' => $form->createView()));
            }
        }

        if ($form->isValid()) {
            $this->get('testcase')->createTestcaseForUser(
                $form->get('user')->getData(),
                $form->get('testcase')->getData()
            );
            $this->addFlash('success', 'The new testcase has been added.');

            $user = $this->getUser();
            if (!empty($user) && $user->isEnabled()) { // A previously non-logged in user that is fully activated used the homepage form
                return $this->redirect($this->get('router')->generate('testcases.index'));
            } else {
                $this->addFlash('info', 'We will start monitoring your site as soon as your account has been activated.');
                return $this->render('AppBundle:registration:thankyou.html.twig');
            }
        }
        return $this->render('AppBundle:default:index.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new TestcaseType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('testcase')->createTestcaseForUser(
                $user,
                $form->getData()
            );
            $this->addFlash('success', 'The new testcase has been added.');
        }

        return $this->render('AppBundle:testcases:new.html.twig', array('form' => $form->createView()));
    }

    public function editAction(Request $request, $testcaseId)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->find($testcaseId);

        if (empty($testcase)) {
            $this->addFlash('error', 'Testcase not found.');
            return $this->redirect($this->get('router')->generate('testcases.index'));
        }

        if ($testcase->getUser()->getId() != $user->getId()) {
            $this->addFlash('error', 'Access to this testcase has been denied.');
            return $this->redirect($this->get('router')->generate('testcases.index'));
        }

        $form = $this->createForm(new TestcaseType(), $testcase);
        $form->add('Save', 'submit', ['label' => 'Update testcase', 'attr' => ['class' => 'btn-primary']]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The testcase has been updated.');
        }

        return $this->render('AppBundle:testcases:edit.html.twig', array('form' => $form->createView()));
    }

    public function disableAction($testcaseId)
    {
        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->find($testcaseId);
        if (!empty($testcase)) {
            $user = $this->getUser();
            if ($user->getId() != $testcase->getUser()->getId()) {
                $this->addFlash('error', 'Access to this testcase has been denied.');
            } else {
                $testcase->setEnabled(false);
                $em->flush();
                $this->addFlash('success', 'The testcase "' . $testcase->getTitle() . '" has been disabled.');
            }
        } else {
            $this->addFlash('error', 'The testcase could not be found.');
        }
        return $this->redirect($this->get('router')->generate('testcases.index'));
    }

    public function enableAction($testcaseId)
    {
        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->find($testcaseId);
        if (!empty($testcase)) {
            $user = $this->getUser();
            if ($user->getId() != $testcase->getUser()->getId()) {
                $this->addFlash('error', 'Access to this testcase has been denied.');
            } else {
                $testcase->setEnabled(true);
                $em->flush();
                $this->addFlash('success', 'The testcase "' . $testcase->getTitle() . '" has been enabled.');
            }
        } else {
            $this->addFlash('error', 'The testcase could not be found.');
        }
        return $this->redirect($this->get('router')->generate('testcases.index'));
    }

    /**
     * @param Form $form
     */
    protected function createOrLoginUserFromForm(Form $form)
    {
        $this->get('registration')->createUserOrLogin(
            $form->get('user')->getData(),
            $form
        );
    }
}
