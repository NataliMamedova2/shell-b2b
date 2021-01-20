<?php

declare(strict_types=1);

namespace App\Application\Action\Backend\Partners;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as Templating;

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
     * @Route("/partners", name="partners_homepage", methods={"GET"})
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(): Response
    {
        return new Response(
            $this->templating->render('backend/partners/dashboard.html.twig')
        );
    }
}
