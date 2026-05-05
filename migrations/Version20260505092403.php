<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505092403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "character" ADD building_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "character" ADD CONSTRAINT FK_937AB0344D2A7E12 FOREIGN KEY (building_id) REFERENCES "building" (id)');
        $this->addSql('CREATE INDEX IDX_937AB0344D2A7E12 ON "character" (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "character" DROP CONSTRAINT FK_937AB0344D2A7E12');
        $this->addSql('DROP INDEX IDX_937AB0344D2A7E12');
        $this->addSql('ALTER TABLE "character" DROP building_id');
    }
}
