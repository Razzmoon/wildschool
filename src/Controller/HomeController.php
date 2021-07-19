<?php

namespace App\Controller;

use App\Entity\Name;
use App\Form\NameType;
use App\Repository\NameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController

{
    /**
     * @Route("/", name="wild")
     */
    public function home(NameRepository $nameRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $names = $nameRepository->findAll();

        $name = new Name();

        //On genere le formulaire en utilisant le gabarit + une instance de l'entité article
        $nameForm = $this->createForm(NameType::class, $name);

        //on lie le formulaire au donné de POST (donné envoyer par post)
        $nameForm->handleRequest($request);

        //si le form a été poster et qu'il et valide (que tous les champ obligatoire son bien rempli)
        //alors on enregistre l'article en bdd=
        if ($nameForm->isSubmitted() && $nameForm->isValid()) {
            $entityManager->persist($name);
            $entityManager->flush();

            return $this->redirectToRoute('wild');


        }

        return $this->render('home.html.twig', [
            'nameForm' => $nameForm->createView(),
            'names' => $names

        ]);
    }

    /**
     * @Route("/article/delete/{id}",name="admin_name_Delete")
     */
    public function deleteName($id,NameRepository $nameRepository,EntityManagerInterface $entityManager)
    {
        $name= $nameRepository->find($id);
        $entityManager->remove($name);
        //prend tous et direction la bdd
        $entityManager->flush();

        //redirige vers la page article_list
        return $this->redirectToRoute('wild');
    }
}