<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        // Ingredients
        $ingredients = [];
        for($i=1; $i <= 50; $i++){
            $ingredient = new Ingredient;
            $ingredient->setName('Ingredient #' . $i)
                ->setPrice(mt_rand(0,100));
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        for($j=1; $j <= 25; $j++){
            $recipe = new Recipe;
            $recipe->setName('Recipe #'.$j)
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setQuantity(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false);
            for($k = 0; $k < mt_rand(5,15); $k++){
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients)-1)]);
            }
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
