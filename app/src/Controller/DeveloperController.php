<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Developer;
use App\Form\DeveloperType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    /**
     * @Route("/developers", name="developers")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $developer = new Developer();
        $form = $this->createForm(DeveloperType::class, $developer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($developer);
            $entityManager->flush();

            $this->addFlash('success', 'Developer created successfully!');

            return $this->redirectToRoute('developers');
        }

        return $this->render('developer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
