<?php

namespace App\Security\Voter;

use App\Entity\Projet;
use App\Entity\Employe;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjetVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'projet.view' === $attribute && $subject instanceof Projet;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // pas connecté
        if (!$user instanceof Employe) {
            return false;
        }

        // admin => OK pour tout
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Projet $projet */
        $projet = $subject;

        // user => OK seulement si assigné
        return $projet->getEmployes()->contains($user);
    }
}
