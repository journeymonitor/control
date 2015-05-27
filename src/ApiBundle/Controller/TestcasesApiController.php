<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestcasesApiController extends Controller {
    /**
     * @ApiDoc(
     *      section="Testcase",
     *      description="list known testcases",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when the user is not authorized."
     *      }
     * )
     * @Get("/api/internal/testcases")
     * @View(serializerGroups={"testcase"})
     */
    public function listTestcasesAction() {
        $testcases = $this->get('repo.testcase')->findBy(['enabled' => 1]);
        $testcasesArray = [];
        foreach ($testcases as $testcase) {
            $testcasesArray[] = [
                'id'          => $testcase->getId(),
                'title'       => $testcase->getTitle(),
                'notifyEmail' => $testcase->getUser()->getEmailCanonical(),
                'cadence'     => $testcase->getCadence(),
                'script'      => $testcase->getScript(),
            ];
        }
        return $testcasesArray;
    }
}
