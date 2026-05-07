<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260507094359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "character" RENAME COLUMN name TO gls_name');
        $this->addSql('ALTER TABLE "character" RENAME COLUMN intelligence TO gls_intelligence');
        $this->addSql('ALTER TABLE "character" RENAME COLUMN creation TO gls_creation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "character" RENAME COLUMN gls_name TO name');
        $this->addSql('ALTER TABLE "character" RENAME COLUMN gls_intelligence TO intelligence');
        $this->addSql('ALTER TABLE "character" RENAME COLUMN gls_creation TO creation');
    }
}
