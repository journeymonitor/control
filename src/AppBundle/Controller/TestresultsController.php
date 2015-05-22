<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\testresultType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class TestresultsController extends Controller
{
    public function showAction($testresultId)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $testresultRepo = $em->getRepository('AppBundle\Entity\testresult');
        $testresult = $testresultRepo->find($testresultId);

        if (empty($testresult)) {
            $this->addFlash('error', 'Testresult not found.');
            return $this->redirect($this->get('router')->generate('testcases.index'));
        }

        if ($testresult->getTestCase()->getUser()->getId() != $user->getId()) {
            $this->addFlash('error', 'Access to this testresult has been denied.');
            return $this->redirect($this->get('router')->generate('testcases.index'));
        }

        return $this->render('AppBundle:testresults:show.html.twig', array('testresult' => $testresult));
    }
}
