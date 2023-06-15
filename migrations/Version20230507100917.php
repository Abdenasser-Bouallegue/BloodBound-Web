<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507100917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD facility_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A7014910 FOREIGN KEY (facility_id) REFERENCES facility (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_FE38F844A7014910 ON appointment (facility_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A76ED395');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A7014910');
        $this->addSql('DROP INDEX IDX_FE38F844A7014910 ON appointment');
        $this->addSql('ALTER TABLE appointment DROP facility_id');
    }
}
