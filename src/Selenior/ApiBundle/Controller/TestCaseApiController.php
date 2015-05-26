<?php

namespace Selenior\ApiBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Selenior\ApiBundle\Http\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestCaseApiController extends Controller {
    /**
     * @ApiDoc(
     *      section="Testcase",
     *      description="list known testcases",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when the user is not authorized."
     *      }
     * )
     * @Get("/api/internal/testcase")
     * @Security("has_role('ROLE_INTERNAL_API')")
     * @View(serializerGroups={"testcase"})
     */
    public function listTestcasesAction() {
        return $this->get('selenior.repo.testcase')->findAll();
    }
}