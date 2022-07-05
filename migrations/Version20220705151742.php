<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705151742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, game_master_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, min_game_place INT DEFAULT NULL, max_game_place INT DEFAULT NULL, assigned_place INT DEFAULT NULL, game_status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', game_master_commentary LONGTEXT DEFAULT NULL, INDEX IDX_232B318CC1151A13 (game_master_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil_picture (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, profil_picture VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_93CF80D8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_user_in_game (id INT AUTO_INCREMENT NOT NULL, is_present TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_user_in_game_user (status_user_in_game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_25CC76E618010EF2 (status_user_in_game_id), INDEX IDX_25CC76E6A76ED395 (user_id), PRIMARY KEY(status_user_in_game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_user_in_game_game (status_user_in_game_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_8B74912318010EF2 (status_user_in_game_id), INDEX IDX_8B749123E48FD905 (game_id), PRIMARY KEY(status_user_in_game_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC1151A13 FOREIGN KEY (game_master_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil_picture ADD CONSTRAINT FK_93CF80D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE status_user_in_game_user ADD CONSTRAINT FK_25CC76E618010EF2 FOREIGN KEY (status_user_in_game_id) REFERENCES status_user_in_game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE status_user_in_game_user ADD CONSTRAINT FK_25CC76E6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE status_user_in_game_game ADD CONSTRAINT FK_8B74912318010EF2 FOREIGN KEY (status_user_in_game_id) REFERENCES status_user_in_game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE status_user_in_game_game ADD CONSTRAINT FK_8B749123E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(255) DEFAULT NULL, ADD firstname VARCHAR(255) DEFAULT NULL, ADD birthday DATETIME DEFAULT NULL, ADD postal_code VARCHAR(255) DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD adress VARCHAR(255) DEFAULT NULL, ADD member_status VARCHAR(255) DEFAULT NULL, ADD member_seniority VARCHAR(255) DEFAULT NULL, ADD association_registration_date DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_association_member TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE status_user_in_game_game DROP FOREIGN KEY FK_8B749123E48FD905');
        $this->addSql('ALTER TABLE status_user_in_game_user DROP FOREIGN KEY FK_25CC76E618010EF2');
        $this->addSql('ALTER TABLE status_user_in_game_game DROP FOREIGN KEY FK_8B74912318010EF2');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE profil_picture');
        $this->addSql('DROP TABLE status_user_in_game');
        $this->addSql('DROP TABLE status_user_in_game_user');
        $this->addSql('DROP TABLE status_user_in_game_game');
        $this->addSql('ALTER TABLE user DROP name, DROP firstname, DROP birthday, DROP postal_code, DROP city, DROP adress, DROP member_status, DROP member_seniority, DROP association_registration_date, DROP created_at, DROP updated_at, DROP is_association_member');
    }
}
