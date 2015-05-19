<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestcasesApiController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle\Entity\User');
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcases = $testcaseRepo->findBy(['enabled' => 1]);
        $testcasesArray = [];
        foreach ($testcases as $testcase) {
            $user = $userRepo->find($testcase->getUserId());
            $testcasesArray[] = [
                'id'          => $testcase->getId(),
                'title'       => $testcase->getTitle(),
                'notifyEmail' => $user->getEmailCanonical(),
                'cadence'     => $testcase->getCadence(),
                'script'      => $testcase->getScript(),
            ];
        }
        return new JsonResponse($testcasesArray);
    }
}
