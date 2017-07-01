<?php

namespace CartBooking\Application;

use CartBooking\Model\Publisher\Command\UpdatePasswordCommand;
use CartBooking\Model\Publisher\Command\UpdatePublisherCommand;
use CartBooking\Model\Publisher\Publisher;
use CartBooking\Model\Publisher\PublisherRepository;
use CartBooking\Model\Publisher\PublisherService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class WebPublisherService
{
    /** @var PublisherRepository */
    private $publisherRepository;
    /** @var TokenStorage */
    private $tokenStorage;
    /** @var PublisherService */
    private $publisherService;

    public function __construct(PublisherRepository $publisherRepository, PublisherService $publisherService, TokenStorage $tokenStorage)
    {
        $this->publisherRepository = $publisherRepository;
        $this->tokenStorage = $tokenStorage;
        $this->publisherService = $publisherService;
    }

    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null) {
            return $this->publisherRepository->findByPhone($token->getUsername());
        }
        return null;
    }

    public function updateUser(Publisher $publisher)
    {
        $this->publisherService->updatePublisher(new UpdatePublisherCommand(
            $publisher->getId(),
            $publisher->getFullName(),
            $publisher->getPhone(),
            $publisher->getEmail()
        ));
        $this->publisherService->updatePublisherPassword(new UpdatePasswordCommand($publisher->getId(), $publisher->getPassword()));
    }
}
