<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128182503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proyectos (id INT AUTO_INCREMENT NOT NULL, sede_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, zona_actuacion VARCHAR(100) NOT NULL, total_gasto INT NOT NULL, personal_vinculado INT NOT NULL, total_participantes INT NOT NULL, total_voluntarios INT NOT NULL, fecha_inicio DATETIME NOT NULL, INDEX IDX_A9DC1621E19F41BF (sede_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proyectos ADD CONSTRAINT FK_A9DC1621E19F41BF FOREIGN KEY (sede_id) REFERENCES sedes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proyectos DROP FOREIGN KEY FK_A9DC1621E19F41BF');
        $this->addSql('DROP TABLE proyectos');
    }
}
