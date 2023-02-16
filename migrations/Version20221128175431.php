<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128175431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD nombre VARCHAR(255) NOT NULL, ADD apellidos VARCHAR(255) NOT NULL, ADD sexo VARCHAR(50) NOT NULL, ADD edad INT NOT NULL, ADD fecha_nacimiento DATETIME NOT NULL, ADD nacionalidad VARCHAR(255) NOT NULL, ADD imagen VARCHAR(255) DEFAULT NULL, ADD estado_civil VARCHAR(100) NOT NULL, ADD discapacidad VARCHAR(255) DEFAULT NULL, ADD fecha_alta DATETIME NOT NULL, ADD fecha_baja DATETIME DEFAULT NULL, ADD documentacion_legal VARCHAR(50) DEFAULT NULL, ADD rol VARCHAR(255) NOT NULL, ADD comunidad_autonoma VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP nombre, DROP apellidos, DROP sexo, DROP edad, DROP fecha_nacimiento, DROP nacionalidad, DROP imagen, DROP estado_civil, DROP discapacidad, DROP fecha_alta, DROP fecha_baja, DROP documentacion_legal, DROP rol, DROP comunidad_autonoma');
    }
}
