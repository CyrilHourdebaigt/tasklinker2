<?php

namespace App\Factory;

use App\Entity\Employe;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class EmployeFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return Employe::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'prenom' => self::faker()->firstName(),
            'nom' => self::faker()->lastName(),
            'email' => self::faker()->unique()->safeEmail(),
            'dateEntree' => self::faker()->dateTimeBetween('-3 years', 'now'),
            'statut' => self::faker()->randomElement(['CDI', 'CDD', 'Freelance']),
            'roles' => [],
            'password' => 'password', // sera hashé après
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(Employe $employe): void {
                $employe->setPassword(
                    $this->passwordHasher->hashPassword($employe, $employe->getPassword())
                );
            });
    }
}