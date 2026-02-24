<?php

namespace App\DataFixtures;

use App\Factory\EmployeFactory;
use App\Factory\ProjetFactory;
use App\Factory\TacheFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1) Employés
        EmployeFactory::createMany(10);

        // 2) ADMIN
        EmployeFactory::createOne([
            'prenom' => 'Elise',
            'nom' => 'Hor',
            'email' => 'elise@example.fr',
            'statut' => 'CDI',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'elise',
        ]);

        // 3) Projets
        $projets = ProjetFactory::createMany(5);

        // 4) Tâches : 10 tâches par projet, avec statuts variés
        foreach ($projets as $projet) {
            TacheFactory::createMany(4, [
                'projet' => $projet,
                'statut' => 'To Do',
                'employe' => null
            ]);
            TacheFactory::createMany(3, [
                'projet' => $projet,
                'statut' => 'Doing',
                'employe' => null
            ]);
            TacheFactory::createMany(3, [
                'projet' => $projet,
                'statut' => 'Done',
                'employe' => null
            ]);
        }

        $manager->flush();
    }
}
