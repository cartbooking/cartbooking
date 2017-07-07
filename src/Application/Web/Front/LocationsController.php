<?php

namespace CartBooking\Application\Web\Front;

use CartBooking\Model\Location\LocationRepositoryInterface;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class LocationsController
{
    /** @var LocationRepositoryInterface */
    private $locationRepository;
    /** @var array */
    private $settings;
    /** @var Twig_Environment */
    private $twig;

    public function __construct(LocationRepositoryInterface $locationRepository, array $settings, Twig_Environment $twig)
    {
        $this->locationRepository = $locationRepository;
        $this->settings = $settings;
        $this->twig = $twig;
    }

    public function locationAction($locationId): Response
    {
        $location = $this->locationRepository->findById($locationId);
        $name = $location->getName();
        $center = $location->getCentre();
        $markers = $location->getMarkers();
        $description = $location->getDescription();
        $zoom = $location->getZoom();
        $key = $this->settings['google_api_key'];
        $params = http_build_query([
                'center' => $center,
                'size' => '300x300',
                'zoom' => $zoom,
                'scale' => '2',
                'maptype' => 'roadmap',
                'key' => $key
            ]) . '&' . $this->extractMarkers($markers);
        return new Response($this->twig->render('locations/location.twig', [
            'title' => "Map $name",
            'anchor_href' => "https://www.google.com.au/maps/@$center,17.3z",
            'img_href' => "https://maps.googleapis.com/maps/api/staticmap?$params",
            'location_name' => $name,
            'location_description' => $description,
        ]));
    }

    public function indexAction(): Response
    {
        $locations = $this->locationRepository->findAll();
        return new Response($this->twig->render('locations/index.twig', ['locations' => $locations]));
    }

    private function extractMarkers(array $markers): string
    {
        $markersParam = [];
        foreach ($markers as $marker) {
            $markersParam[] = htmlentities('markers=' . $marker);
        }
        return implode('&', $markersParam);
    }
}
