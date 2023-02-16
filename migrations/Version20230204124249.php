<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204124249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proyectos ADD activo VARCHAR(50) NOT NULL');
        $this->addSql('DROP INDEX email_2 ON user');
        $this->addSql('DROP INDEX email ON user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proyectos DROP activo');
        $this->addSql('CREATE UNIQUE INDEX email_2 ON user (email)');
        $this->addSql('CREATE INDEX email ON user (email)');
    }
}
