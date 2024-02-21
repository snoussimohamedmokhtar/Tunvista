<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221095637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce CHANGE type_a type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement CHANGE date_deb date_debut DATE NOT NULL, CHANGE type_e type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce CHANGE type type_a VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement CHANGE date_debut date_deb DATE NOT NULL, CHANGE type type_e VARCHAR(255) NOT NULL');
    }
}
