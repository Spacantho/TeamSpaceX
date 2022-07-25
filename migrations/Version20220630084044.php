<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630084044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E7927C74E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_confirmation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, email_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_1D2EF46FA76ED395 (user_id), INDEX IDX_1D2EF46FA832C1C9 (email_id), UNIQUE INDEX email_confirmation_unique (user_id, email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_indication (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, email_id INT NOT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_7DE39708A76ED395 (user_id), INDEX IDX_7DE39708A832C1C9 (email_id), UNIQUE INDEX indication_email_unique (user_id, email_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mot_de_passe (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, valeur VARCHAR(255) NOT NULL, memo VARCHAR(255) DEFAULT NULL, INDEX IDX_398F0C51A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pseudo (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, pseudo VARCHAR(255) NOT NULL, INDEX IDX_86CC499DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_confirmation ADD CONSTRAINT FK_1D2EF46FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email_confirmation ADD CONSTRAINT FK_1D2EF46FA832C1C9 FOREIGN KEY (email_id) REFERENCES email (id)');
        $this->addSql('ALTER TABLE email_indication ADD CONSTRAINT FK_7DE39708A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email_indication ADD CONSTRAINT FK_7DE39708A832C1C9 FOREIGN KEY (email_id) REFERENCES email (id)');
        $this->addSql('ALTER TABLE mot_de_passe ADD CONSTRAINT FK_398F0C51A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pseudo ADD CONSTRAINT FK_86CC499DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_confirmation DROP FOREIGN KEY FK_1D2EF46FA832C1C9');
        $this->addSql('ALTER TABLE email_indication DROP FOREIGN KEY FK_7DE39708A832C1C9');
        $this->addSql('ALTER TABLE email_confirmation DROP FOREIGN KEY FK_1D2EF46FA76ED395');
        $this->addSql('ALTER TABLE email_indication DROP FOREIGN KEY FK_7DE39708A76ED395');
        $this->addSql('ALTER TABLE mot_de_passe DROP FOREIGN KEY FK_398F0C51A76ED395');
        $this->addSql('ALTER TABLE pseudo DROP FOREIGN KEY FK_86CC499DA76ED395');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE email_confirmation');
        $this->addSql('DROP TABLE email_indication');
        $this->addSql('DROP TABLE mot_de_passe');
        $this->addSql('DROP TABLE pseudo');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
