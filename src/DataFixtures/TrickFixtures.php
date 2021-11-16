<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoriesName = ['Grab', 'Straight airs', 'Spins', 'Flips', 'Slides'];

        foreach ($categoriesName as $categoryTag) { 
            $category= new Category();
            $category -> setName($categoryTag);
            $category -> setDescription("Etiam scelerisque odio arcu, a egestas felis finibus non. Integer libero dolor, malesuada sed porta eu, pulvinar vitae lacus. Proin sodales urna vitae sagittis pharetra.");
            $manager->persist($category);
        }

        $manager->flush();
    }
}
