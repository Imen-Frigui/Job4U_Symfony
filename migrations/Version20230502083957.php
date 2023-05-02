<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502083957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE captcha (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, lien_image_captcha VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, id_poste INT DEFAULT NULL, id_user INT DEFAULT NULL, description VARCHAR(500) NOT NULL, date VARCHAR(100) NOT NULL, INDEX IDX_67F068BC920C4E9B (id_poste), INDEX IDX_67F068BC6B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE domaine (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(250) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, event_category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, location VARCHAR(255) NOT NULL, img VARCHAR(250) NOT NULL, INDEX IDX_3BAE0AA761220EA6 (creator_id), INDEX IDX_3BAE0AA7B9CF4E62 (event_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_category (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likez (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_poste INT DEFAULT NULL, INDEX IDX_3016F6D96B3CA4B (id_user), INDEX IDX_3016F6D9920C4E9B (id_poste), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, event_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, has_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CA71F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, event_id INT NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_D79F6B11A76ED395 (user_id), INDEX IDX_D79F6B1171F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poste (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, titre VARCHAR(200) NOT NULL, description VARCHAR(500) NOT NULL, img VARCHAR(250) NOT NULL, domaine VARCHAR(250) NOT NULL, date VARCHAR(250) NOT NULL, INDEX IDX_7C890FAB6B3CA4B (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postulation (id_pos INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, adresse VARCHAR(255) NOT NULL, email VARCHAR(50) NOT NULL, date_pos DATE NOT NULL, INDEX IDX_DA7D4E9B61220EA6 (creator_id), PRIMARY KEY(id_pos)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_poste INT DEFAULT NULL, note INT NOT NULL, INDEX IDX_D88926226B3CA4B (id_user), INDEX IDX_D8892622920C4E9B (id_poste), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, id_poste INT DEFAULT NULL, type VARCHAR(250) NOT NULL, description VARCHAR(500) NOT NULL, INDEX IDX_C42F77846B3CA4B (id_user), INDEX IDX_C42F7784920C4E9B (id_poste), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE societe (id INT AUTO_INCREMENT NOT NULL, adresse VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, sos_image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thumbnail (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', name VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(180) NOT NULL, image VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, FULLTEXT INDEX IDX_1483A5E954231355F75B2554 (Nom, Role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC920C4E9B FOREIGN KEY (id_poste) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA761220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7B9CF4E62 FOREIGN KEY (event_category_id) REFERENCES event_category (id)');
        $this->addSql('ALTER TABLE likez ADD CONSTRAINT FK_3016F6D96B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE likez ADD CONSTRAINT FK_3016F6D9920C4E9B FOREIGN KEY (id_poste) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE poste ADD CONSTRAINT FK_7C890FAB6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE postulation ADD CONSTRAINT FK_DA7D4E9B61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926226B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622920C4E9B FOREIGN KEY (id_poste) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77846B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784920C4E9B FOREIGN KEY (id_poste) REFERENCES poste (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE reclamation ADD id_user_id INT NOT NULL, ADD message VARCHAR(255) NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD statut VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640479F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE60640479F37AE5 ON reclamation (id_user_id)');
        $this->addSql('ALTER TABLE reponse ADD id_reclamation_id INT NOT NULL, ADD message_rep VARCHAR(255) NOT NULL, ADD date_rep DATE NOT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7100D1FDF FOREIGN KEY (id_reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC7100D1FDF ON reponse (id_reclamation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F61220EA6');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640479F37AE5');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC920C4E9B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC6B3CA4B');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA761220EA6');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7B9CF4E62');
        $this->addSql('ALTER TABLE likez DROP FOREIGN KEY FK_3016F6D96B3CA4B');
        $this->addSql('ALTER TABLE likez DROP FOREIGN KEY FK_3016F6D9920C4E9B');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA71F7E88B');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11A76ED395');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1171F7E88B');
        $this->addSql('ALTER TABLE poste DROP FOREIGN KEY FK_7C890FAB6B3CA4B');
        $this->addSql('ALTER TABLE postulation DROP FOREIGN KEY FK_DA7D4E9B61220EA6');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926226B3CA4B');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622920C4E9B');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77846B3CA4B');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784920C4E9B');
        $this->addSql('DROP TABLE captcha');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE domaine');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_category');
        $this->addSql('DROP TABLE likez');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE poste');
        $this->addSql('DROP TABLE postulation');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE societe');
        $this->addSql('DROP TABLE thumbnail');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FBCF5E72D');
        $this->addSql('DROP INDEX IDX_CE60640479F37AE5 ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP id_user_id, DROP message, DROP type, DROP statut');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7100D1FDF');
        $this->addSql('DROP INDEX IDX_5FB6DEC7100D1FDF ON reponse');
        $this->addSql('ALTER TABLE reponse DROP id_reclamation_id, DROP message_rep, DROP date_rep');
    }
}
