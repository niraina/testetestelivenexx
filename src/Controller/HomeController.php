<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManager;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $entityManager, TacheRepository $tacheRepository): Response
    {
        $taches = $entityManager->createQuery('SELECT COUNT(t) FROM App\Entity\Tache t')->getSingleScalarResult();
        return $this->render('home/index.html.twig', [
            'taches' => $tacheRepository->findAll(),
            'stats' => compact ('taches')
        ]);
    }
    /**
     * @Route("/nouveau", name="nouveau")
     */
    
    public function nouveau(Request $request)
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tache);
            $this->addFlash('success','Catégorie ajouté avec succès');
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/nouveau.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="modifier")
     */

    public function modifier(Request $request, Tache $tache)
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success','Catégorie modifié avec succès');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/modifier.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="supprimer")
     */
    public function supprimer(Request $request, Tache $tache): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($tache);
        $this->addFlash('success','Catégorie supprimé avec succès');
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
