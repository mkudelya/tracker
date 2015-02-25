<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150225163855 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO users (id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, locked, expired, expires_at, confirmation_token, password_requested_at, roles, credentials_expired, credentials_expire_at, fullname, avatar) VALUES (1, 'admin', 'admin', 'admin@gmail.com', 'admin@gmail.com', 1, 'cflqbkmyqjw4s0cw848wcgc08os0cc8', 'VUc/UVlX331u/NihZqqsfKiyNmFtSZ7i0dZDtjtR+xeRW30W2yWhLo+m7RPGPrsCuF9cVvhoOATVqlk1GWJ9YQ==', '2015-02-25 16:21:58', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:18:\"ROLE_ADMINISTRATOR\";}', 0, NULL, 'Mike Kudelya', NULL);");
        $this->addSql("INSERT INTO users (id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, locked, expired, expires_at, confirmation_token, password_requested_at, roles, credentials_expired, credentials_expire_at, fullname, avatar) VALUES (2, 'manager', 'manager', 'manager@gmail.com', 'manager@gmail.com', 1, 'n7e5p3zi280w4coc8080go080gskgkw', 'gj6+6UczqAho7R5E7A0W89c/aLaQC1Qr5HwevBqIaI6fHP1OA0rbvOGAqwkAdE+vGCp28YJIT0NnZWrdKSxMhg==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:12:\"ROLE_MANAGER\";}', 0, NULL, 'Manager', NULL);");
        $this->addSql("INSERT INTO users (id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, locked, expired, expires_at, confirmation_token, password_requested_at, roles, credentials_expired, credentials_expire_at, fullname, avatar) VALUES (3, 'operator', 'operator', 'operator@gmail.com', 'operator@gmail.com', 1, 'dz6qjp7gq2o08sww4g4og40g8k8gwwo', 'gPoT+EB3cdOTmJ+DeGRRh8IMD77n6oZJWz7phCE/eyfOXnp3JHrh8xskoxUYoNpM1TKx8PZVr8kGCbI34sIRjw==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:13:\"ROLE_OPERATOR\";}', 0, NULL, 'Operator', NULL);");
        $this->addSql("INSERT INTO projects (id, label, summary, code) VALUES (1, 'Training Project Label', 'Training Tracking Project', 'TTP');");
        $this->addSql("INSERT INTO projects (id, label, summary, code) VALUES (2, 'Oro training', 'Oro training project', 'OROTRAINING');");
        $this->addSql("INSERT INTO project_members (project_id, user_id) VALUES (1, 1);");
        $this->addSql("INSERT INTO project_members (project_id, user_id) VALUES (2, 1);");
        $this->addSql("INSERT INTO project_members (project_id, user_id) VALUES (2, 2);");
        $this->addSql("INSERT INTO project_members (project_id, user_id) VALUES (2, 3);");
        $this->addSql("INSERT INTO issues (id, reporter_id, assignee_id, project_id, parent_id, summary, code, description, type, priority, status, resolution, created, updated) VALUES (1, 1, 1, 1, NULL, 'Training Prepare Task', 'TPT1', 'Prepare tracking to demo', 'Task', 3, 'Open', NULL, '2015-02-25 16:53:02', '2015-02-25 17:04:47');");
        $this->addSql("INSERT INTO issues (id, reporter_id, assignee_id, project_id, parent_id, summary, code, description, type, priority, status, resolution, created, updated) VALUES (2, 1, 1, 1, NULL, 'Demo data story', 'DDS', 'demo for training story', 'story', 2, 'Open', NULL, '2015-02-25 16:54:14', '2015-02-25 17:06:47');");
        $this->addSql("INSERT INTO issues (id, reporter_id, assignee_id, project_id, parent_id, summary, code, description, type, priority, status, resolution, created, updated) VALUES (3, 1, 1, 1, 2, 'User creating', 'UC1', 'create users', 'Task', 1, 'In Progress', NULL, '2015-02-25 16:54:49', '2015-02-25 17:08:43');");
        $this->addSql("INSERT INTO issues (id, reporter_id, assignee_id, project_id, parent_id, summary, code, description, type, priority, status, resolution, created, updated) VALUES (4, 1, 1, 1, 2, 'Create permissions', 'CP', 'Create permissions', 'Task', 2, 'Open', NULL, '2015-02-25 16:55:37', '2015-02-25 17:05:53');");
        $this->addSql("INSERT INTO issues (id, reporter_id, assignee_id, project_id, parent_id, summary, code, description, type, priority, status, resolution, created, updated) VALUES (5, 1, 3, 2, NULL, 'Documentation', 'DOCS', 'Reading documentation', 'Task', 1, 'Open', NULL, '2015-02-25 17:23:24', '2015-02-25 17:23:24');");
        $this->addSql("INSERT INTO issues (id, reporter_id, assignee_id, project_id, parent_id, summary, code, description, type, priority, status, resolution, created, updated) VALUES (6, 1, 3, 2, NULL, 'Adding comments', 'AC1', 'Adding comments to code', 'Task', 2, 'Open', NULL, '2015-02-25 17:25:57', '2015-02-25 17:25:57');");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (1, 1);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (1, 2);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (2, 1);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (2, 2);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (3, 1);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (4, 1);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (4, 2);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (5, 1);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (5, 3);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (6, 1);");
        $this->addSql("INSERT INTO issue_collaborators (issue_id, user_id) VALUES (6, 3);");
        $this->addSql("INSERT INTO comments (id, user_id, issue_id, body, created) VALUES (1, 2, 1, 'Please, create database schema', '2015-02-25 17:04:47');");
        $this->addSql("INSERT INTO comments (id, user_id, issue_id, body, created) VALUES (2, 2, 4, \"This task isn\'t necessary\", '2015-02-25 17:05:53');");
        $this->addSql("INSERT INTO comments (id, user_id, issue_id, body, created) VALUES (3, 2, 2, 'Please start UC1', '2015-02-25 17:06:47');");
        $this->addSql("INSERT INTO comments (id, user_id, issue_id, body, created) VALUES (4, 1, 5, 'This task is open!', '2015-02-25 17:24:15');");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (1, 1, 1, 1, '', '2015-02-25 16:53:02', 1);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (2, 2, 1, 1, '', '2015-02-25 16:54:14', 1);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (3, 3, 1, 1, '', '2015-02-25 16:54:49', 1);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (4, 4, 1, 1, '', '2015-02-25 16:55:37', 1);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (5, 1, 2, 1, '', '2015-02-25 17:04:47', 3);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (6, 4, 2, 1, '', '2015-02-25 17:05:53', 3);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (7, 2, 2, 1, '', '2015-02-25 17:06:47', 3);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (8, 3, 2, 1, 'In Progress', '2015-02-25 17:08:43', 2);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (9, 5, 1, 2, '', '2015-02-25 17:23:24', 1);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (10, 5, 1, 2, '', '2015-02-25 17:24:15', 3);");
        $this->addSql("INSERT INTO activities (id, issue_id, user_id, project_id, body, created, type) VALUES (11, 6, 1, 2, '', '2015-02-25 17:25:57', 1);");
    }

    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM activities');
        $this->addSql('DELETE FROM comments');
        $this->addSql('DELETE FROM issue_collaborators');
        $this->addSql('DELETE FROM issues WHERE id=3');
        $this->addSql('DELETE FROM issues WHERE id=4');
        $this->addSql('DELETE FROM issues');
        $this->addSql('DELETE FROM project_members');
        $this->addSql('DELETE FROM projects');
        $this->addSql('DELETE FROM users');
    }
}
