<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429161241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE espece (id INT AUTO_INCREMENT NOT NULL, nom_commun VARCHAR(255) NOT NULL, nom_scientifique VARCHAR(255) NOT NULL, classification LONGTEXT DEFAULT NULL, origine LONGTEXT DEFAULT NULL, repartition_geographique VARCHAR(255) DEFAULT NULL, description_physique LONGTEXT DEFAULT NULL, dimorphisme_sexuel LONGTEXT DEFAULT NULL, alimentation LONGTEXT DEFAULT NULL, taille_minimale_bac INT DEFAULT NULL, temperature_min DOUBLE PRECISION DEFAULT NULL, temperature_max DOUBLE PRECISION DEFAULT NULL, ph_min DOUBLE PRECISION DEFAULT NULL, ph_max DOUBLE PRECISION DEFAULT NULL, gh_min DOUBLE PRECISION DEFAULT NULL, gh_max DOUBLE PRECISION DEFAULT NULL, comportement LONGTEXT DEFAULT NULL, reproduction LONGTEXT DEFAULT NULL, type_espece VARCHAR(50) NOT NULL, biotope VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE espece
        SQL);
    }
}
