<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507100548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A0A76ED395');
        $this->addSql('DROP INDEX IDX_31E581A0A76ED395 ON donation');
        $this->addSql('ALTER TABLE donation DROP description, DROP don_location, DROP email, DROP phone_number, DROP donation_date, CHANGE user_id appointment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31E581A0E5B533F9 ON donation (appointment_id)');
        $this->addSql('ALTER TABLE facility ADD starting_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD ending_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A0E5B533F9');
        $this->addSql('DROP INDEX UNIQ_31E581A0E5B533F9 ON donation');
        $this->addSql('ALTER TABLE donation ADD description VARCHAR(255) NOT NULL, ADD don_location VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD phone_number BIGINT NOT NULL, ADD donation_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', CHANGE appointment_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_31E581A0A76ED395 ON donation (user_id)');
        $this->addSql('ALTER TABLE facility DROP starting_time, DROP ending_time');
    }
}
