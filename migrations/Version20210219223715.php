<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210219223715 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE parcel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE parcel (id INT NOT NULL, rate_id INT NOT NULL, name VARCHAR(255) NOT NULL, weight NUMERIC(10, 2) DEFAULT NULL, volume NUMERIC(10, 7) DEFAULT NULL, value INT DEFAULT NULL, updated_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C99B5D60BC999F9F ON parcel (rate_id)');
        $this->addSql('ALTER TABLE parcel ADD CONSTRAINT FK_C99B5D60BC999F9F FOREIGN KEY (rate_id) REFERENCES rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE parcel_id_seq CASCADE');
        $this->addSql('DROP TABLE parcel');
    }
}
