<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240922145057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit_sous_categorie (produit_id INT NOT NULL, sous_categorie_id INT NOT NULL, INDEX IDX_33319C0EF347EFB (produit_id), INDEX IDX_33319C0E365BF48 (sous_categorie_id), PRIMARY KEY(produit_id, sous_categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_sous_categorie ADD CONSTRAINT FK_33319C0EF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_sous_categorie ADD CONSTRAINT FK_33319C0E365BF48 FOREIGN KEY (sous_categorie_id) REFERENCES sous_categorie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_sous_categorie DROP FOREIGN KEY FK_33319C0EF347EFB');
        $this->addSql('ALTER TABLE produit_sous_categorie DROP FOREIGN KEY FK_33319C0E365BF48');
        $this->addSql('DROP TABLE produit_sous_categorie');
    }
}
