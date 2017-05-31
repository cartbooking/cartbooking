<?php

namespace CartBooking\Application\Http;

use CartBooking\Location\LocationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class MapsController
{
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var LocationRepository */
    private $locationRepository;
    /** @var array */
    private $settings;
    /** @var Twig_Environment */
    private $twig;

    public function __construct(Request $request, Response $response, LocationRepository $locationRepository, array $settings, Twig_Environment $twig)
    {
        $this->request = $request;
        $this->response = $response;
        $this->locationRepository = $locationRepository;
        $this->settings = $settings;
        $this->twig = $twig;
    }

    public function location($locationId)
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
        $this->response->setContent($this->twig->render('map.twig', [
            'title' => "Map $name",
            'anchor_href' => "https://www.google.com.au/maps/@$center,17.3z",
            'img_href' => "https://maps.googleapis.com/maps/api/staticmap?$params",
            'location_name' => $name,
            'location_description' => $description,
        ]));
        return $this->response->send();
    }

    public function indexAction()
    {
        $locations = $this->locationRepository->findAll();
        $this->response->setContent($this->twig->render('locations.twig', ['locations' => $locations]));
        return $this->response->send();
    }

    private function extractMarkers($markers)
    {
        $return = [];
        foreach (explode("\n", $markers) as $marker) {
            $return[] = htmlentities('markers=' . $marker);
        }
        return implode('&', $return);
    }
}
