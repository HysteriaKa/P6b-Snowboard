<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create("fr_FR");
        $categoriesName = ['Jumps', 'Rotations', 'Grabs', 'Presses', 'Rail Tricks'];
        $trickNames = ['Ollie', 'Nollie', 'Melon', 'Indy', 'Nose Grab', 'japan', 'Tail Press', 'Frontside 180', 'Backside 180', 'Butter', 'Air-to-fakie', 'Beef Curtains','truck drive','mute','sad','Chicken salad','Drunk Driver','Canadian Bacon','Gorilla'];
        $entitiesCategories =[];

        $user = new User;
        $user->setEmail('test@example.com');
        $user->setUsername('Eglantine');
        $password = "fake12359";
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setAvatar("default-avatar.png");
        $manager->persist($user);
        $commentsContent = [' is simply dummy text of the printing and ', '  It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.', 'Contrary to popular belief, Lorem Ipsum is not simply random text', 'There are many variations of passages of Lorem Ipsum available', 'free from repetition, injected humour, or non-characteristic words etc', ' by accident, sometimes on purpose (injected humour and the like)', 'free from repetition, injected humour, or non-characteristic words etc', 'he first line of Lorem Ipsum, Lorem ipsum dolor sit amet.', ' Many desktop publishing packages and web page editors '];
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
            $trick->setOnTopPic('defaultTrick.jpg');
            $trick->setVideoLink("https://www.youtube.com/embed/V9xuy-rVj9w");
            // foreach ($commentsContent as $content) {
            //     $comment = new Comment();
            //     $comment->setTrick($trick);
            //     $comment->setContent($content);
            //     $comment->setUser($user);
            //     $manager->persist($comment);
            // }

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
