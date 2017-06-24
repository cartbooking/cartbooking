<?php

namespace CartBooking\Model\Publisher;

use CartBooking\Model\Publisher\Command\AddPublisherCommand;
use CartBooking\Model\Publisher\Command\UpdatePublisherCommand;

class PublisherService
{
    /** @var PublisherRepository */
    private $publisherRepository;

    public function __construct(PublisherRepository $publisherRepository)
    {
        $this->publisherRepository = $publisherRepository;
    }

    public function getPublisherData(int $publisherId): array
    {
        $publisher = $this->publisherRepository->findById($publisherId);
        if ($publisher === null) {
            return [];
        }
        return [
            'id' => $publisher->getId(),
            'full_name' => $publisher->getFullName(),
            'email' => $publisher->getEmail(),
            'gender' => $publisher->getGender(),
            'phone' => $publisher->getPhone(),
        ];
    }

    public function updatePublisher(UpdatePublisherCommand $command)
    {
        $publisher = $this->publisherRepository->findById($command->getPublisherId());
        $publisher->setFullName($command->getName());
        $publisher->setPhone($command->getPhone());
        $publisher->setEmail($command->getName());
        $this->publisherRepository->save($publisher);
    }

    public function addPublisher(AddPublisherCommand $command)
    {
        $publisher = new Publisher();
        $publisher->setPreferredName($command->getPreferredName());
        $publisher->setFullName($command->getFullName());
        $publisher->setGender($command->getGender());
        $publisher->setPhone($command->getPhone());
        $publisher->setEmail($command->getEmail());
        return $this->publisherRepository->save($publisher);
    }
}
