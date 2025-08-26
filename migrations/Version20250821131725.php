<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250821131725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD pull_quote_texte LONGTEXT DEFAULT NULL, ADD pull_quote_source VARCHAR(255) DEFAULT NULL, ADD pull_quote_position VARCHAR(12) DEFAULT 'right' NOT NULL, ADD pull_quote_theme VARCHAR(12) DEFAULT 'default' NOT NULL, ADD pull_quote_index INT DEFAULT 2 NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP pull_quote_texte, DROP pull_quote_source, DROP pull_quote_position, DROP pull_quote_theme, DROP pull_quote_index
        SQL);
    }
}
