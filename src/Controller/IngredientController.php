<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    // Injection de dépendance - permet d'accéder directement à un service
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
    
        return $this->render('pages/ingredient/index.html.twig',
            ["ingredients" => $ingredients]);
    }

    /**
     * This controller show a form to create an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name:'ingredient.new', methods:['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ) : Response
    {

        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été crée avec succès !'
            );

           return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig',
        ['form' => $form]);
    }

    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods:['GET', 'POST'])]
    public function edit (
        Ingredient $ingredient, 
        Request $request, 
        EntityManagerInterface $manager
        ) : Response
    {
        /* Méthode 1 */
        // $ingredient = $repository->findOneBy(['id' => $id]);
        // $form = $this->createForm(IngredientType::class, $ingredient);

        // return $this->render('pages/ingredient/edit.html.twig', [
        //     'form' => $form->createView()
        // ]);

        /** Méthode 2
         * Au lieu de passer un id, on passe directement Ingredient 
         * et Symfony fait la correspondance avec l'id dans Ingredient
         */

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

           return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig',
        ['form' => $form]);

    }

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods:['GET'])]
    public function delete(
        Ingredient $ingredient,
        Request $request, 
        EntityManagerInterface $manager
    ) : Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'ingrédient a bien été supprimé.'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
