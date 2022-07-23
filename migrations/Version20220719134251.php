<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719134251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_picture (id INT AUTO_INCREMENT NOT NULL, game_picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CEE45BDBF FOREIGN KEY (picture_id) REFERENCES game_picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318CEE45BDBF ON game (picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CEE45BDBF');
        $this->addSql('DROP TABLE game_picture');
        $this->addSql('DROP INDEX UNIQ_232B318CEE45BDBF ON game');
        $this->addSql('ALTER TABLE game DROP picture_id');
    }
}
