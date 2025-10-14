<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // === 1. Créer l'admin ===
        $admin = new \App\Entity\User();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail('admin@example.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setTelephone('0123456789');
        $admin->setAdresse('123 Admin St, Admin City');
        $admin->setDateDeNaissance(new \DateTime('-30 years'));
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);
        $utilisateur = new \App\Entity\User();
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setEmail('utilisateur@example.com');
        $utilisateur->setNom('Utilisateur');
        $utilisateur->setPrenom('Simple');
        $utilisateur->setTelephone('0987654321');
        $utilisateur->setAdresse('456 User Ave, User City');
        $utilisateur->setDateDeNaissance(new \DateTime('-25 years'));
        $utilisateur->setPassword($this->passwordHasher->hashPassword($utilisateur, 'userpass'));
        $manager->persist($utilisateur);
        
        // === 2. Créer les utilisateurs ===
        $users = [$admin]; // Tableau pour stocker les users
        
        for ($i = 0; $i < 5; $i++) {
            $user = new \App\Entity\User();
            
            $plaintextPassword = $faker->password();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setPassword($hashedPassword);
            
            $user->setRoles(['ROLE_USER']);
            $user->setEmail($faker->email());
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setTelephone($faker->phoneNumber());
            $user->setAdresse($faker->address());
            $user->setDateDeNaissance($faker->dateTimeBetween('-70 years', '-18 years'));
            
            $manager->persist($user);
            $users[] = $user; // Ajouter au tableau
        }
        
        // === 3. Créer les types de lieu ===
        $typesDeLieu = [];
        for ($i = 0; $i < 5; $i++) {
            $typeDeLieu = new \App\Entity\TypeDeLieu();
            $typeDeLieu->setLibelle($faker->word());
            $manager->persist($typeDeLieu);
            $typesDeLieu[] = $typeDeLieu;
        }
        
        // === 4. Créer les types de réseau ===
        $typesDeReseau = [];
        for ($i = 0; $i < 5; $i++) {
            $typeDeReseau = new \App\Entity\TypeDeReseau();
            $typeDeReseau->setNom($faker->word());
            $typeDeReseau->setLogo($faker->word());
            $manager->persist($typeDeReseau);
            $typesDeReseau[] = $typeDeReseau;
        }
        
        // === 5. Créer les catégories ===
        $categories = [];
        for ($i = 0; $i < 5; $i++) {
            $categorie = new \App\Entity\Categorie();
            $categorie->setLibelle($faker->word());
            $manager->persist($categorie);
            $categories[] = $categorie;
        }
        
        // === 6. Créer les CV ===
        $cvs = [];
        for ($i = 0; $i < 5; $i++) {
            $cv = new \App\Entity\Cv();
            $cv->setTitre($faker->sentence(3));
            $cv->setLeClient($faker->randomElement($users));
            $manager->persist($cv);
            $cvs[] = $cv;
        }
        
        // Flush pour sauvegarder les CV avant les entités qui en dépendent
        $manager->flush();
        
        // === 7. Créer les lieux ===
        for ($i = 0; $i < 5; $i++) {
            $lieu = new \App\Entity\Lieu();
            $lieu->setNom($faker->company());
            $lieu->setLeTypeL($faker->randomElement($typesDeLieu));
            // Si Lieu a besoin d'un CV, ajoutez :
            // $lieu->setLeCv($faker->randomElement($cvs));
            $manager->persist($lieu);
        }
        
        // === 8. Créer les points ===
        for ($i = 0; $i < 5; $i++) {
            $point = new \App\Entity\Point();
            $point->setLibelle($faker->word());
            $point->setLaCate($faker->randomElement($categories));
            $point->setLeCv($faker->randomElement($cvs));
            $manager->persist($point);
        }
        
        // === 9. Créer les réseaux ===
        for ($i = 0; $i < 5; $i++) {
            $reseau = new \App\Entity\Reseau();
            $reseau->setLien($faker->url());
            $reseau->setLeTypeR($faker->randomElement($typesDeReseau));
            $reseau->setProprio($faker->randomElement($users));
            // Si Reseau a besoin d'un CV, ajoutez :
            // $reseau->setLeCv($faker->randomElement($cvs));
            $manager->persist($reseau);
        }
        
        // Flush final pour tout sauvegarder
        $manager->flush();
    }
}