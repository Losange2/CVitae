<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923144900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cv (id INT AUTO_INCREMENT NOT NULL, le_client_id INT NOT NULL, titre VARCHAR(255) NOT NULL, INDEX IDX_B66FFE92C0F37DD6 (le_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, le_type_l_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_2F577D59C7A35A7A (le_type_l_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point (id INT AUTO_INCREMENT NOT NULL, le_cv_id INT NOT NULL, la_cate_id INT NOT NULL, un_lieu_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_B7A5F324CFF65D02 (le_cv_id), INDEX IDX_B7A5F324F132F9E (la_cate_id), INDEX IDX_B7A5F3241C935144 (un_lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reseau (id INT AUTO_INCREMENT NOT NULL, proprio_id INT NOT NULL, le_type_r_id INT NOT NULL, lien VARCHAR(255) NOT NULL, INDEX IDX_CDE52CB86B82600 (proprio_id), INDEX IDX_CDE52CB877657AD6 (le_type_r_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_de_lieu (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_de_reseau (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_de_naissance DATE NOT NULL, telephone VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT FK_B66FFE92C0F37DD6 FOREIGN KEY (le_client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59C7A35A7A FOREIGN KEY (le_type_l_id) REFERENCES type_de_lieu (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324CFF65D02 FOREIGN KEY (le_cv_id) REFERENCES cv (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F324F132F9E FOREIGN KEY (la_cate_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F3241C935144 FOREIGN KEY (un_lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE reseau ADD CONSTRAINT FK_CDE52CB86B82600 FOREIGN KEY (proprio_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reseau ADD CONSTRAINT FK_CDE52CB877657AD6 FOREIGN KEY (le_type_r_id) REFERENCES type_de_reseau (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cv DROP FOREIGN KEY FK_B66FFE92C0F37DD6');
        $this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59C7A35A7A');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324CFF65D02');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F324F132F9E');
        $this->addSql('ALTER TABLE point DROP FOREIGN KEY FK_B7A5F3241C935144');
        $this->addSql('ALTER TABLE reseau DROP FOREIGN KEY FK_CDE52CB86B82600');
        $this->addSql('ALTER TABLE reseau DROP FOREIGN KEY FK_CDE52CB877657AD6');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE cv');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE point');
        $this->addSql('DROP TABLE reseau');
        $this->addSql('DROP TABLE type_de_lieu');
        $this->addSql('DROP TABLE type_de_reseau');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
