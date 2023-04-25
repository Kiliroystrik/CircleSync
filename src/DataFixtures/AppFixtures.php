<?php

namespace App\DataFixtures;

use App\Entity\Commentary;
use App\Entity\Group;
use App\Entity\Image;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $type = ['Post', 'User', 'commentary'];
        $faker = Factory::create();

        $users = [];
        $groups = [];
        $posts = [];

        // Create Users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail);
            $user->setRoles([]);
            $user->setPassword('password');
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setBirthdate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-60 years', '-18 years', null)));
            $user->setSubscribedDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-5 years', 'now', null)));

            $manager->persist($user);
            $users[] = $user;
        }

        // Create Groups
        for ($i = 0; $i < 20; $i++) {
            $group = new Group();
            $group->setName($faker->unique()->word);

            $manager->persist($group);
            $groups[] = $group;
        }

        // Create Posts
        for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setContent($faker->paragraph);
            $post->setPublicationDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now', null)));

            $post->setUser($faker->randomElement($users));
            $post->setGroupe($faker->randomElement($groups));

            $manager->persist($post);
            $posts[] = $post;
        }

        // Create Commentaries
        for ($i = 0; $i < 20; $i++) {
            $commentary = new Commentary();
            $commentary->setContent($faker->sentence);

            $commentary->setUser($faker->randomElement($users));
            $commentary->setPost($faker->randomElement($posts));

            $manager->persist($commentary);
        }

        // Create Likes
        // for ($i = 0; $i < 20; $i++) {
        //     $like = new Like();
        //     $like->setPost($faker->randomElement($posts));
        //     $like->setUser($faker->randomElement($users));
        //     $like->setType($type[0]);

        //     $manager->persist($like);
        // }

        // Create Images
        for ($i = 0; $i < 20; $i++) {
            $image = new Image();
            $image->setUrl($faker->imageUrl());
            if ($i < 11) {
                $image->setType($type[0]);
            } else {
                $image->setType($type[1]);
            }
            $manager->persist($image);
        }

        $manager->flush();
    }
}
