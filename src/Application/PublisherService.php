<?php

namespace CartBooking\Application;

use CartBooking\Model\Publisher\PublisherRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PublisherService
{
    /** @var PublisherRepository */
    private $publisherRepository;
    /** @var TokenStorage */
    private $tokenStorage;

    public function __construct(PublisherRepository $publisherRepository, TokenStorage $tokenStorage)
    {
        $this->publisherRepository = $publisherRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function getCurrentPublisher()
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null) {
            return $this->publisherRepository->findByPhone($token->getUsername());
        }
        return null;
    }
}
