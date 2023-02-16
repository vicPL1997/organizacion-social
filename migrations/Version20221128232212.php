<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128232212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gasto (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, proyecto_id INT NOT NULL, importe NUMERIC(10, 2) NOT NULL, descripcion VARCHAR(1000) NOT NULL, INDEX IDX_AE43DA143397707A (categoria_id), INDEX IDX_AE43DA14F625D1BA (proyecto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gasto ADD CONSTRAINT FK_AE43DA143397707A FOREIGN KEY (categoria_id) REFERENCES categoria_gasto (id)');
        $this->addSql('ALTER TABLE gasto ADD CONSTRAINT FK_AE43DA14F625D1BA FOREIGN KEY (proyecto_id) REFERENCES proyectos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gasto DROP FOREIGN KEY FK_AE43DA143397707A');
        $this->addSql('ALTER TABLE gasto DROP FOREIGN KEY FK_AE43DA14F625D1BA');
        $this->addSql('DROP TABLE gasto');
    }
}
