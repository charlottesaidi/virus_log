<?php

namespace App\Controller\Www;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class WwwController extends BaseController
{
    /**
     * @Route("/", name="home")
     * @Route("/{any}", requirements={"route"="^.+"})
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}