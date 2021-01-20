<?php

declare(strict_types=1);

namespace App\Application\Action\Backend;

use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as Templating;
use Symfony\Component\HttpFoundation\Response;

final class DashboardAction
{

    /**
     * @var Templating
     */
    private $templating;

    public function __construct(
        Templating $templating
    ) {
        $this->templating = $templating;
    }

    /**
     * @Route("/admin", name="admin_homepage", methods={"GET"})
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(): Response
    {

        return new Response(
            $this->templating->render('backend/dashboard.html.twig')
        );
    }
}
