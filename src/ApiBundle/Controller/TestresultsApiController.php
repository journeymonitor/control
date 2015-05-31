<?php

namespace ApiBundle\Controller;

use ApiBundle\Service\HarTransformer;
use ApiBundle\Service\SeleneseRunnerLogAnalyzer;
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
        $srla = new SeleneseRunnerLogAnalyzer();
        $urls = $srla->getUrlsOfRequestedPages($output);
        $ht = new HarTransformer();
        $transformedHar = $ht->splitIntoMultiplePages(json_decode($har), $urls);
        $response = new JsonResponse();
        $response->setCallback('onInputData');
        $response->setData($transformedHar);
        return $response;
    }
}
