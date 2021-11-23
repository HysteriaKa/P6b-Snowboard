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
        $categoriesName = ['Jumps', 'Rotations', 'Grabs', 'Presses', 'Rail Tricks'];
        $trickNames = ['Ollie', 'Nollie', 'Melon', 'Indy', 'Nose Grab', 'japan', 'Tail Press', 'Frontside 180', 'Backside 180', 'Butter', 'Air-to-fakie', 'Beef Curtains','truck drive','mute','sad'];

        foreach ($categoriesName as $categoryTag) {
            $category = new Category();
            $category->setName($categoryTag);
            $category->setDescription("Etiam scelerisque odio arcu, a egestas felis finibus non. Integer libero dolor, malesuada sed porta eu, pulvinar vitae lacus. Proin sodales urna vitae sagittis pharetra.");
            $manager->persist($category);
        }
        foreach ($trickNames as $trickName) {            
            $trick = new Trick();
            $trick->setName($trickName);
            $trick->setDescription('Nam varius vitae nisi semper rhoncus. Maecenas dolor elit, feugiat sit amet nisl non, posuere pellentesque dolor. Quisque vel dignissim lorem. Aenean imperdiet non velit scelerisque bibendum. Proin non libero malesuada diam rutrum lacinia ac ut turpis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nunc ac metus vitae lectus imperdiet ultricies at non enim. Aenean et urna erat. Vestibulum malesuada ante risus, vitae volutpat dui pellentesque varius. Maecenas tortor metus, feugiat in interdum vel, dignissim nec risus. Fusce auctor nisl vel purus feugiat blandit et nec dolor. Vivamus non leo vulputate, venenatis dolor sed, suscipit tellus. Suspendisse potenti. Nam eget nunc malesuada, rutrum orci tempor, euismod augue. Curabitur ultrices velit eu congue mattis.');
            $trick->setCreatedAt(new \DateTimeImmutable);

            $trick->setCategory($category);
            $trick->setSlug($trickName);
            $manager->persist($trick);
        }
        $manager->flush();
    }
}
