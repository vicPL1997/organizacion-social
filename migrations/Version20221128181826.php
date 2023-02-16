<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128181826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sedes (id INT AUTO_INCREMENT NOT NULL, administrador_sede_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, localizacion VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_EAF0B6AB2A76889B (administrador_sede_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sedes ADD CONSTRAINT FK_EAF0B6AB2A76889B FOREIGN KEY (administrador_sede_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sedes DROP FOREIGN KEY FK_EAF0B6AB2A76889B');
        $this->addSql('DROP TABLE sedes');
    }
}
