<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021151704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, publication_id INT DEFAULT NULL, auteur_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, likes INT NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_67F068BC38B217A7 (publication_id), INDEX IDX_67F068BC60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire_user (commentaire_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_646B121BBA9CD190 (commentaire_id), INDEX IDX_646B121BA76ED395 (user_id), PRIMARY KEY(commentaire_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation_user (conversation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5AECB5559AC0396 (conversation_id), INDEX IDX_5AECB555A76ED395 (user_id), PRIMARY KEY(conversation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, conversation_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_B6BD307FFB88E14F (utilisateur_id), INDEX IDX_B6BD307F9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(50) NOT NULL, message VARCHAR(255) NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, contenu VARCHAR(255) DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, likes INT NOT NULL, INDEX IDX_AF3C677960BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, profil_picture VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_publication (user_id INT NOT NULL, publication_id INT NOT NULL, INDEX IDX_627AEECA76ED395 (user_id), INDEX IDX_627AEEC38B217A7 (publication_id), PRIMARY KEY(user_id, publication_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire_user ADD CONSTRAINT FK_646B121BBA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire_user ADD CONSTRAINT FK_646B121BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB5559AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_user ADD CONSTRAINT FK_5AECB555A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C677960BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_publication ADD CONSTRAINT FK_627AEECA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_publication ADD CONSTRAINT FK_627AEEC38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC38B217A7');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC60BB6FE6');
        $this->addSql('ALTER TABLE commentaire_user DROP FOREIGN KEY FK_646B121BBA9CD190');
        $this->addSql('ALTER TABLE commentaire_user DROP FOREIGN KEY FK_646B121BA76ED395');
        $this->addSql('ALTER TABLE conversation_user DROP FOREIGN KEY FK_5AECB5559AC0396');
        $this->addSql('ALTER TABLE conversation_user DROP FOREIGN KEY FK_5AECB555A76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FFB88E14F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C677960BB6FE6');
        $this->addSql('ALTER TABLE user_publication DROP FOREIGN KEY FK_627AEECA76ED395');
        $this->addSql('ALTER TABLE user_publication DROP FOREIGN KEY FK_627AEEC38B217A7');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE commentaire_user');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE conversation_user');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_publication');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
