<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410114627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal_race (animal_id INT NOT NULL, race_id INT NOT NULL, INDEX IDX_9D42FA9B8E962C16 (animal_id), INDEX IDX_9D42FA9B6E59D40D (race_id), PRIMARY KEY(animal_id, race_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal_race ADD CONSTRAINT FK_9D42FA9B8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_race ADD CONSTRAINT FK_9D42FA9B6E59D40D FOREIGN KEY (race_id) REFERENCES race (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal ADD veterinary_report_id INT DEFAULT NULL, ADD habitat_id INT NOT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F4CAB118 FOREIGN KEY (veterinary_report_id) REFERENCES rapport_veterinaire (id)');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6AAB231F4CAB118 ON animal (veterinary_report_id)');
        $this->addSql('CREATE INDEX IDX_6AAB231FAFFE2D26 ON animal (habitat_id)');
        $this->addSql('ALTER TABLE image ADD habitat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('CREATE INDEX IDX_C53D045FAFFE2D26 ON image (habitat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal_race DROP FOREIGN KEY FK_9D42FA9B8E962C16');
        $this->addSql('ALTER TABLE animal_race DROP FOREIGN KEY FK_9D42FA9B6E59D40D');
        $this->addSql('DROP TABLE animal_race');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F4CAB118');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FAFFE2D26');
        $this->addSql('DROP INDEX UNIQ_6AAB231F4CAB118 ON animal');
        $this->addSql('DROP INDEX IDX_6AAB231FAFFE2D26 ON animal');
        $this->addSql('ALTER TABLE animal DROP veterinary_report_id, DROP habitat_id');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FAFFE2D26');
        $this->addSql('DROP INDEX IDX_C53D045FAFFE2D26 ON image');
        $this->addSql('ALTER TABLE image DROP habitat_id');
    }
}
