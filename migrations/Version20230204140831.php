<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204140831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingreso (id INT AUTO_INCREMENT NOT NULL, proyecto_id INT DEFAULT NULL, tipo VARCHAR(255) NOT NULL, nombre_emisor VARCHAR(255) DEFAULT NULL, cantidad INT NOT NULL, INDEX IDX_CC9B241FF625D1BA (proyecto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ingreso ADD CONSTRAINT FK_CC9B241FF625D1BA FOREIGN KEY (proyecto_id) REFERENCES proyectos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingreso DROP FOREIGN KEY FK_CC9B241FF625D1BA');
        $this->addSql('DROP TABLE ingreso');
    }
}
