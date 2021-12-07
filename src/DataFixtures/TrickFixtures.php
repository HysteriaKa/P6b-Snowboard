<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create("fr_FR");
        $categoriesName = ['Jumps', 'Rotations', 'Grabs', 'Presses', 'Rail Tricks'];
        $trickNames = ['Ollie', 'Nollie', 'Melon', 'Indy', 'Nose Grab', 'japan', 'Tail Press', 'Frontside 180', 'Backside 180', 'Butter', 'Air-to-fakie', 'Beef Curtains','truck drive','mute','sad','Chicken salad','Drunk Driver','Canadian Bacon','Gorilla'];
        $entitiesCategories =[];

        foreach ($categoriesName as $categoryTag) {
            $category = new Category();
            $category->setName($categoryTag);
            $category->setDescription($faker->sentence());
            $manager->persist($category);
            array_push($entitiesCategories, $category);
        }
        foreach ($trickNames as $trickName) {            
            $trick = new Trick();
            $trick->setName($trickName);
            $trick->setDescription($faker->sentence());
            $trick->setCreatedAt(new \DateTime);
            $idCategory = rand(0,count($categoriesName)-1);
            $trick->setCategory($entitiesCategories[$idCategory]);
            $trick->setSlug($trickName);
            $manager->persist($trick);
        }
        $manager->flush();
    }
}
