<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestcasesApiController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcases = $testcaseRepo->findBy(['enabled' => 1]);
        $testcasesArray = [];
        foreach ($testcases as $testcase) {
            $testcasesArray[] = [
                'id'          => $testcase->getId(),
                'title'       => $testcase->getTitle(),
                'notifyEmail' => $testcase->getNotifyEmail(),
                'cadence'     => $testcase->getCadence(),
                'script'      => $testcase->getScript(),
            ];
        }
        return new JsonResponse($testcasesArray);
    }
}
