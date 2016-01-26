<?php

namespace AppBundle\EventListener;

use AppBundle\Repository\TestcaseRepository;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RegistrationConfirmationListener implements EventSubscriberInterface {

    /**
     * @var TestcaseRepository
     */
    private $testcaseRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param TestcaseRepository $testcaseRepository
     * @param TokenStorage $tokenStorage
     * @param EntityManager $entityManager
     */
    public function __construct(
        TestcaseRepository $testcaseRepository,
        EntityManager $entityManager
    )
    {
        $this->testcaseRepository = $testcaseRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onRegistrationConfirmed',
        );
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function onRegistrationConfirmed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setActivatedAt(new \DateTime());
        $testcases = $this->testcaseRepository->findBy(['user' => $user]);
        foreach ($testcases as $testcase) {
            $testcase->setEnabled(true);
            $testcase->setActivatedAt(new \DateTime());
            $this->entityManager->persist($testcase);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
