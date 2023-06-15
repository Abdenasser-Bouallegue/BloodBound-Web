<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230506121626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6A7014910');
        $this->addSql('DROP TABLE planning');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, facility_id INT DEFAULT NULL, weekday DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', hourtime TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\', INDEX IDX_D499BFF6A7014910 (facility_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6A7014910 FOREIGN KEY (facility_id) REFERENCES facility (id) ON DELETE CASCADE');
    }
}
