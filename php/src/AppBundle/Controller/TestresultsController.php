<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestresultsController extends Controller
{
    public function indexAction(Request $request, $testcaseId) {
        $user = $this
                ->get('demo_service')
                ->getUser($request, $this->getUser());

        if (empty($user)) {
            $this->addFlash('error', 'Access denied.');
            return $this->redirect($this->get('router')->generate('homepage'));
        }

        $offset = (int)$request->query->get('offset');

        $em = $this->getDoctrine()->getManager();
        $testcaseRepo = $em->getRepository('AppBundle\Entity\Testcase');
        $testcase = $testcaseRepo->find($testcaseId);

        $arr = [];
        $testresults = $testcase->getLimitedTestresults($offset, 100);
        foreach ($testresults as $testresult) {
            if (is_object($testresult->getStatistics())) {
                $arr[] = [
                    'id' => $testresult->getId(),
                    'datetimeRun' => $testresult->getDatetimeRun(),
                    'exitCode' => $testresult->getExitCode(),
                    'runtimeMilliseconds' => $testresult->getStatistics()->getRuntimeMilliseconds(),
                    'numberOf200' => $testresult->getStatistics()->getNumberOf200(),
                    'numberOf400' => $testresult->getStatistics()->getNumberOf400(),
                    'numberOf500' => $testresult->getStatistics()->getNumberOf500()
                ];
            } else {
                $arr[] = [
                    'id' => $testresult->getId(),
                    'datetimeRun' => $testresult->getDatetimeRun(),
                    'exitCode' => $testresult->getExitCode(),
                    'runtimeMilliseconds' => null,
                    'numberOf200' => null,
                    'numberOf400' => null,
                    'numberOf500' => null
                ];
            }
        }

        $response = new JsonResponse();
        $response->setData($arr);
        
        return $response;
    }

    public function showAction(Request $request, $testresultId)
    {
        $user = $this
            ->get('demo_service')
            ->getUser($request, $this->getUser());

        if (empty($user)) {
            $this->addFlash('error', 'Access denied.');
            return $this->redirect($this->get('router')->generate('homepage'));
        }

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

        return $this->render(
            'AppBundle:testresults:show.html.twig',
            array(
                'testresult' => $testresult,
                'isDemoMode' => $this->get('demo_service')->isDemoMode($request)
            )
        );
    }
}
