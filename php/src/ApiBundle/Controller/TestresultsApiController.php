<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestresultsApiController extends Controller {
    /**
     * @ApiDoc(
     *      section="Testcase",
     *      description="list known testcases",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when the user is not authorized."
     *      }
     * )
     * @Get("/api/testresults/{id}/har.jsonp")
     * @View(serializerGroups={"testcase"})
     */
    public function getTestcaseHarAction($id) {
        $testresult = $this->get('repo.testresult')->find($id);
        $har = $testresult->getHar();
        $output = $testresult->getOutput();
        $srlas = $this->get('selenese_runner_log_analyzer');
        $urls = $srlas->getUrlsOfRequestedPages($output);
        $hts = $this->get('har_transformer');

        // This is only neccessary for HARs from before https://github.com/journeymonitor/monitor/issues/19
        $transformedHar = $hts->splitIntoMultiplePages(json_decode($har), $urls);

        $response = new JsonResponse();
        $response->setCallback('onInputData');
        $response->setData($transformedHar);
        return $response;
    }
}
