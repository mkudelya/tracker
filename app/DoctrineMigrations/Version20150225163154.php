<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150225163154 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, fullname VARCHAR(255) NOT NULL, avatar LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, summary LONGTEXT NOT NULL, code VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5C93B3A477153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE project_members (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D3BEDE9A166D1F9C (project_id), INDEX IDX_D3BEDE9AA76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE activities (id INT AUTO_INCREMENT NOT NULL, issue_id INT DEFAULT NULL, user_id INT DEFAULT NULL, project_id INT DEFAULT NULL, body LONGTEXT NOT NULL, created DATETIME NOT NULL, type INT DEFAULT 0 NOT NULL, INDEX IDX_B5F1AFE55E7AA58C (issue_id), INDEX IDX_B5F1AFE5A76ED395 (user_id), INDEX IDX_B5F1AFE5166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE issues (id INT AUTO_INCREMENT NOT NULL, reporter_id INT DEFAULT NULL, assignee_id INT DEFAULT NULL, project_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, summary LONGTEXT NOT NULL, code VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(15) NOT NULL, priority INT DEFAULT 0 NOT NULL, status VARCHAR(20) NOT NULL, resolution VARCHAR(20) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_DA7D7F8377153098 (code), INDEX IDX_DA7D7F83E1CFE6F5 (reporter_id), INDEX IDX_DA7D7F8359EC7D60 (assignee_id), INDEX IDX_DA7D7F83166D1F9C (project_id), INDEX IDX_DA7D7F83727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE issue_collaborators (issue_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_93B721895E7AA58C (issue_id), INDEX IDX_93B72189A76ED395 (user_id), PRIMARY KEY(issue_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, issue_id INT DEFAULT NULL, body LONGTEXT NOT NULL, created DATETIME NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A5E7AA58C (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE55E7AA58C FOREIGN KEY (issue_id) REFERENCES issues (id)');

        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');

        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');

        $this->addSql('ALTER TABLE issues ADD CONSTRAINT FK_DA7D7F83E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES users (id)');

        $this->addSql('ALTER TABLE issues ADD CONSTRAINT FK_DA7D7F8359EC7D60 FOREIGN KEY (assignee_id) REFERENCES users (id)');

        $this->addSql('ALTER TABLE issues ADD CONSTRAINT FK_DA7D7F83166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');

        $this->addSql('ALTER TABLE issues ADD CONSTRAINT FK_DA7D7F83727ACA70 FOREIGN KEY (parent_id) REFERENCES issues (id)');

        $this->addSql('ALTER TABLE issue_collaborators ADD CONSTRAINT FK_93B721895E7AA58C FOREIGN KEY (issue_id) REFERENCES issues (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE issue_collaborators ADD CONSTRAINT FK_93B72189A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');

        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A5E7AA58C FOREIGN KEY (issue_id) REFERENCES issues (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9AA76ED395');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5A76ED395');
        $this->addSql('ALTER TABLE issues DROP FOREIGN KEY FK_DA7D7F83E1CFE6F5');
        $this->addSql('ALTER TABLE issues DROP FOREIGN KEY FK_DA7D7F8359EC7D60');
        $this->addSql('ALTER TABLE issue_collaborators DROP FOREIGN KEY FK_93B72189A76ED395');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9A166D1F9C');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5166D1F9C');
        $this->addSql('ALTER TABLE issues DROP FOREIGN KEY FK_DA7D7F83166D1F9C');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE55E7AA58C');
        $this->addSql('ALTER TABLE issues DROP FOREIGN KEY FK_DA7D7F83727ACA70');
        $this->addSql('ALTER TABLE issue_collaborators DROP FOREIGN KEY FK_93B721895E7AA58C');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A5E7AA58C');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE issues');
        $this->addSql('DROP TABLE issue_collaborators');
        $this->addSql('DROP TABLE comments');
    }
}
