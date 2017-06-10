<?php

namespace CartBooking\Application\Web;

use CartBooking\Lib\Utilities\FileSystem;
use CartBooking\Model\Publisher\PublisherRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ReportsController
{
    /** @var Twig_Environment */
    private $twig;
    /** @var PublisherRepository */
    private $pioneerRepository;
    /** @var FileSystem */
    private $fileSystem;

    public function __construct(Twig_Environment $twig, PublisherRepository $pioneerRepository, FileSystem $fileSystem)
    {
        $this->twig = $twig;
        $this->pioneerRepository = $pioneerRepository;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return (new Response($this->twig->render('reports.twig')));
    }

    public function listBrothersAction(): Response
    {
        $headers = [
            'Content-Type' => 'application/excel',
            'Content-Disposition' => 'attachment; filename="listBrothers.csv"',
        ];
        $body = '';
        $file = $this->fileSystem->fopen('listBrothers.csv', 'w');
        foreach ($this->pioneerRepository->findByGender('m') as $pioneer) {
            $row = [$pioneer->getFirstName(), $pioneer->getLastName()];
            $this->fileSystem->fputcsv($file, $row);
            $body .= implode(',', $row) . PHP_EOL;
        }
        $this->fileSystem->fclose($file);
        return new Response($body, Response::HTTP_OK, $headers);
    }

    public function listInviteesAction(): Response
    {
        $headers = [
            'Content-Type' => 'application/excel',
            'Content-Disposition' => 'attachment; filename="listInvitees.csv"',
        ];
        $body = '';
        $file = $this->fileSystem->fopen('listInvitees.csv', 'w');
        foreach ($this->pioneerRepository->findActive() as $pioneer) {
            $row = [$pioneer->getFirstName(), $pioneer->getLastName()];
            $this->fileSystem->fputcsv($file, $row);
            $body .= implode(',', $row) . PHP_EOL;
        }
        $this->fileSystem->fclose($file);
        return new Response($body, Response::HTTP_OK, $headers);
    }
}
