<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScriptController extends AbstractController
{
    #[Route('/script', name: 'app_script')]
    public function index(): Response
    {
        return $this->render('script/index.html.twig', [
            'controller_name' => 'ScriptController',
        ]);
    }
}
