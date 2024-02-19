<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217102402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hotel ADD image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY fk_idh');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955C1D14EBC FOREIGN KEY (idH) REFERENCES hotel (id_h)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hotel DROP image');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955C1D14EBC');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_idh FOREIGN KEY (idH) REFERENCES hotel (id_h) ON DELETE CASCADE');
    }
}
