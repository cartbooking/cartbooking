<?php

namespace CartBooking\Application\Http;

use CartBooking\Lib\Utilities\FileSystem;
use CartBooking\Publisher\PioneerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ReportsController
{
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var Twig_Environment */
    private $twig;
    /** @var PioneerRepository */
    private $pioneerRepository;
    /** @var FileSystem */
    private $fileSystem;

    public function __construct(Request $request, Response $response, Twig_Environment $twig, PioneerRepository $pioneerRepository, FileSystem $fileSystem)
    {
        $this->request = $request;
        $this->response = $response;
        $this->twig = $twig;
        $this->pioneerRepository = $pioneerRepository;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->response->setContent($this->twig->render('reports.twig'));
    }

    public function listBrothersAction(): Response
    {
        $this->response->headers->set('Content-Type',  'application/excel');
        $this->response->headers->set('Content-Disposition', 'attachment; filename="listBrothers.csv"');
        $file = $this->fileSystem->fopen('listBrothers.csv', 'w');
        foreach ($this->pioneerRepository->findByGender('m') as $pioneer) {
            $this->fileSystem->fputcsv($file, [$pioneer->getFirstName(), $pioneer->getLastName()]);
        }
        $this->fileSystem->fclose($file);
        return $this->response;
    }

    public function listInviteesAction(): Response
    {
        $this->response->headers->set('Content-Type', 'application/excel');
        $this->response->headers->set('Content-Disposition',  'attachment; filename="listInvitees.csv"');
        $file = $this->fileSystem->fopen('listInvitees.csv', 'w');
        foreach ($this->pioneerRepository->findActive() as $pioneer) {
            $this->fileSystem->fputcsv($file, [$pioneer->getFirstName(), $pioneer->getLastName()]);
        }
        $this->fileSystem->fclose($file);
        return $this->response;
    }
}
