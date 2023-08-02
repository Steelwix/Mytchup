<?php

namespace App\Controller;

use App\Entity\Pick;
use App\Form\CreatePickType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PickController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/pick/create', name: 'app_create_pick')]
    public function createPick(Request $request): Response
    {
        $pick = new Pick();
        $form = $this->createForm(CreatePickType::class, $pick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pick->setPlayer($this->getUser());
            $this->em->persist($pick);
            $this->em->flush();
            $this->redirectToRoute('app_data_manager');

        }
        return $this->render('pick/create.html.twig', ['createPickForm' => $form->createView()]);
    }
}
