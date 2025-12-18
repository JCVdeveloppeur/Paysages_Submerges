<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126163448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE plante (id INT AUTO_INCREMENT NOT NULL, nom_commun VARCHAR(255) DEFAULT NULL, nom_scientifique VARCHAR(255) DEFAULT NULL, famille VARCHAR(255) DEFAULT NULL, origine VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, eclairage VARCHAR(100) NOT NULL, croissance VARCHAR(100) NOT NULL, hauteur_max INT NOT NULL, position_aquarium VARCHAR(100) NOT NULL, difficulte VARCHAR(100) NOT NULL, ph_min DOUBLE PRECISION DEFAULT NULL, ph_max DOUBLE PRECISION DEFAULT NULL, temp_min DOUBLE PRECISION DEFAULT NULL, temp_max DOUBLE PRECISION DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, no VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE plante
        SQL);
    }
}
