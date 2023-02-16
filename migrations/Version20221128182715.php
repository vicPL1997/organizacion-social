<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128182715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proyectos_user (proyectos_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A2336351CC33C266 (proyectos_id), INDEX IDX_A2336351A76ED395 (user_id), PRIMARY KEY(proyectos_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proyectos_user ADD CONSTRAINT FK_A2336351CC33C266 FOREIGN KEY (proyectos_id) REFERENCES proyectos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE proyectos_user ADD CONSTRAINT FK_A2336351A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE proyectos ADD fecha_final DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proyectos_user DROP FOREIGN KEY FK_A2336351CC33C266');
        $this->addSql('ALTER TABLE proyectos_user DROP FOREIGN KEY FK_A2336351A76ED395');
        $this->addSql('DROP TABLE proyectos_user');
        $this->addSql('ALTER TABLE proyectos DROP fecha_final');
    }
}
