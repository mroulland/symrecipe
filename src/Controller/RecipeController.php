<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * 
     */
    #[Route('/recette', name: 'recipe.index', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes  
        ]);
    }
    
    #[Route('/recettes/communauté', name: 'recipe.community', methods:['GET'])]
    public function indexPublic(RecipeRepository $repository, PaginatorInterface $paginator, Request $request) : Response
    {

        $recipes = $paginator->paginate(
            $repository->findPublicRecipes(null),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes
        ]);
    }

    
    /**
     * This controller display form to create a recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/nouveau', name:'recipe.new', methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été crée avec succès !'
            );

           return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig',
        ['form' => $form]);
    }

    /**
     * This controller display form to edit a recipe
     *
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/edition/{id}', 'recipe.edit', methods:['GET', 'POST'])]
    #[IsGranted(
        attribute : new Expression('is_granted("ROLE_USER") and user === subject'), 
        subject: new Expression('args["recipe"].getUser()')
    )]
    public function edit (
        Recipe $recipe, 
        Request $request, 
        EntityManagerInterface $manager
        ) : Response
    {

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

           return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig',
        ['form' => $form]);

    }

    /**
     * This controller allow to delete a recipe
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/suppression/{id}', 'recipe.delete', methods:['GET'])]
    #[IsGranted(
        attribute : new Expression('is_granted("ROLE_USER") and user === subject'), 
        subject: new Expression('args["recipe"].getUser()')
    )]
    public function delete(
        Recipe $recipe,
        EntityManagerInterface $manager
    ) : Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'La recette a bien été supprimée.'
        );

        return $this->redirectToRoute('recipe.index');
    }

    /**
     * This controller allow us to see a recipe if it's public
     *
     * @param Recipe $recipe
     * @return Response
     */
    #[IsGranted(
        attribute : new Expression('is_granted("ROLE_USER") and subject === true'), 
        subject: new Expression('args["recipe"].getIsPublic() || args["recipe"].getUser()')
    )]
    #[Route('/recette/{id}', name: 'recipe.show', methods:['GET', 'POST'])]
    public function show(Recipe $recipe, 
        Request $request, 
        EntityManagerInterface $manager, 
        MarkRepository $markRepository) : Response
    {
        $params['recipe'] = $recipe;

        $existingMark = $markRepository->findOneBy(['user' => $this->getUser(), 'recipe' => $recipe]);

        if(!$existingMark){
            $mark = new Mark();
            $form = $this->createForm(MarkType::class, $mark);
            
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $mark = $form->getData();
                $mark->setUser($this->getUser());
                $mark->setRecipe($recipe);
                $manager->persist($mark);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Votre note a bien été enregistrée !'
                );
                return $this->redirectToRoute('recipe.show', ['id'=>$recipe->getId()]);
            }
            $params['form'] = $form;
        }

        return $this->render('pages/recipe/show.html.twig', $params);
    }

}
