<?php

namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Commentaire;
use App\Entity\Groupe;
use App\Entity\Publication;
use App\Entity\Message;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Mettre des photos de profil
        $picturePaths = ['/uploads/users/default-1.jpg', '/uploads/users/default-2.jpg'];


        /** -------------------------------------------------------------
         *                      ENTITé USER
         * 
         *   Ne pas oublier de rajouter les photos de profil au projet 
         *   et de compéter l'entité User avec les 
         *   propriétés dont ont a besoin.
         * 
         * ---------------------------------------------------------------- */
        
        // Set Admin 

        $admin = new User();
        $admin->setEmail('admin@admin.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword('$2y$13$wqXiXE8U6QhYtIRJFedLA.MkNVmDzn89jVz5CBYENUOwHfAlyYNG2')
            ->setNom('Admin Johan')
            ->setUsername('adminj')
            ->setProfilPicture($faker->randomElement($picturePaths))
            ->setCreatedAt($faker->dateTimeBetween('now', '+2 month'));
        $manager->persist($admin);


        // Set Utilisateurs

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setPassword('$2y$13$V9Y9X5XjzK5uNq9qIY6Q4eCJfNcZr7xXs3P8C4ZTqo8DvHjX6')
                ->setRoles(['ROLE_USER'])
                ->setNom($faker->lastName)
                ->setUsername($faker->username)
                ->setProfilPicture($faker->randomElement($picturePaths))
                ->setCreatedAt($faker->dateTimeBetween('now', '+2 month'));
            $manager->persist($user);
            array_push($users, $user);
        }


        /** -------------------------------------------------------------
         *                     ENTITé PUBLICATION
         * 
         *  A terme permettre d'uploader les média localement. 
         *  Mais pour le moment, on utilise des liens vers des images. 
         *  Peut-etre rajouter le nombre d'impression de la publication 
         *  --------------------------------------------------------------*/


        $publications = [];
        for ($i = 0; $i < 20; $i++) {
            $publication = new Publication();
            $publication->setContenu($faker->sentence(10))
                ->setAuteur($faker->randomElement($users))
                ->setMedia('https://placehold.co/600x400')
                ->setLikes($faker->numberBetween(0, 100))
                ->setCreatedAt($faker->dateTimeBetween('now', '+2 month'));
            $manager->persist($publication);
            array_push($publications, $publication);
        }

        /** -------------------------------------------------------------
         *                     ENTITé COMMENTAIRES
         * 
         *  Peut etre rajouter les likes de commentaires dans un second temps
         *  
         *  --------------------------------------------------------------*/

        for ($i = 0; $i < 50; $i++) {
            $commentaire = new Commentaire();
            $commentaire->setContenu($faker->sentence(10))
                ->setAuteur($faker->randomElement($users))
                ->setPublication($faker->randomElement($publications))
                ->setCreatedAt($faker->dateTimeBetween('now', '+2 month'));
            $manager->persist($commentaire);
        }


        /** -------------------------------------------------------------
         *                     ENTITé MESSAGE
         *  
         *  Peut etre fait il rajouté un variable destinataire ?
         *  --------------------------------------------------------------*/
        $status = ['En cours', 'Lu', 'Archivé'];
        for ($i = 0; $i < 20; $i++) {
            $message = new Message();
            $message->setContenu($faker->sentence(10))
                ->setUtilisateur($faker->randomElement($users))
                ->setCreatedAt($faker->dateTimeBetween('now', '+2 month'))
                ->setStatus($faker->randomElement($status));
            $manager->persist($message);
        }


        /** -------------------------------------------------------------
         *                     ENTITé GROUPE
         *  
         *  --------------------------------------------------------------*/

        $groupes = [];
        for ($i = 0; $i < 10; $i++) {
            $groupe = new Groupe();
            $groupe->setNom($faker->word)
                ->setCreatedAt($faker->dateTimeBetween('now', '+2 month'))
                ->setDescription($faker->text(100))
                ->setCreateur($faker->randomElement($users));
            $manager->persist($groupe);
            array_push($groupes, $groupe);
        }

        $manager->flush();
    }
}
