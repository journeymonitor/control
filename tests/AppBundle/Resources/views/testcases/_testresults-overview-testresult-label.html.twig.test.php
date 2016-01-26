<?php

class TestresultsOverviewTestresultLabelTest extends PHPUnit_Framework_TestCase
{
    // Avoid regression of https://github.com/journeymonitor/control/issues/27
    public function test()
    {
        `rm -rf /var/tmp/journeymonitor-twig-tests-cache`;
        `mkdir -p /var/tmp/journeymonitor-twig-tests-cache`;

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../../../../src/AppBundle/Resources/views/testcases/');
        $twig = new Twig_Environment($loader, array(
            'cache' => '/var/tmp/journeymonitor-twig-tests-cache',
        ));
        $template = $twig->loadTemplate('_testresults-overview-testresult-label.html.twig');

        $testresult = new \AppBundle\Entity\Testresult();
        $testresult->setExitCode(0);
        $this->assertSame('success', $template->render(['testresult' => $testresult]));

        $testresult = new \AppBundle\Entity\Testresult();
        $testresult->setExitCode(1);
        $this->assertSame('warning', $template->render(['testresult' => $testresult]));

        $testresult = new \AppBundle\Entity\Testresult();
        $testresult->setExitCode(2);
        $this->assertSame('danger', $template->render(['testresult' => $testresult]));
    }
}
