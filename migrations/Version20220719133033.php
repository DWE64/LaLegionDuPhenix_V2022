<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719133033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profil_picture DROP FOREIGN KEY FK_93CF80D8A76ED395');
        $this->addSql('DROP INDEX UNIQ_93CF80D8A76ED395 ON profil_picture');
        $this->addSql('ALTER TABLE profil_picture DROP user_id');
        $this->addSql('ALTER TABLE user ADD profil_picture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D583E641 FOREIGN KEY (profil_picture_id) REFERENCES profil_picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D583E641 ON user (profil_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profil_picture ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profil_picture ADD CONSTRAINT FK_93CF80D8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_93CF80D8A76ED395 ON profil_picture (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D583E641');
        $this->addSql('DROP INDEX UNIQ_8D93D649D583E641 ON user');
        $this->addSql('ALTER TABLE user DROP profil_picture_id');
    }
}
