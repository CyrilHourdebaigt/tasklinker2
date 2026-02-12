<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212103311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // 1) On ajoute roles avec une valeur par défaut "[]" afin que les employés déjà présents en base auront des rôles valides
        $this->addSql("ALTER TABLE employe ADD roles JSON NOT NULL DEFAULT '[]'");

        // 2) On ajoute password en nullable pour ne pas casser les lignes déjà existantes, les nouveaux comptes créés via l'inscription auront un vrai password hashé
        $this->addSql('ALTER TABLE employe ADD password VARCHAR(255) DEFAULT NULL');

        // 3) Contrainte d'unicité sur l'email (au niveau base)
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F804D3B9E7927C74 ON employe (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F804D3B9E7927C74');
        $this->addSql('ALTER TABLE employe DROP roles');
        $this->addSql('ALTER TABLE employe DROP password');
    }
}
