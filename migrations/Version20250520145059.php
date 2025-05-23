<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520145059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(100) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, keywords VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, texte VARCHAR(255) DEFAULT NULL, content CLOB DEFAULT NULL, is_published BOOLEAN NOT NULL, is_archived BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_23A0E66F675F31B ON article (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE block (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(80) NOT NULL, content VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE block_article (block_id INTEGER NOT NULL, article_id INTEGER NOT NULL, PRIMARY KEY(block_id, article_id), CONSTRAINT FK_7819FA98E9ED820C FOREIGN KEY (block_id) REFERENCES block (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7819FA987294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7819FA98E9ED820C ON block_article (block_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7819FA987294869C ON block_article (article_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, article_id INTEGER NOT NULL, author_id INTEGER NOT NULL, content CLOB NOT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , is_moderated BOOLEAN NOT NULL, is_published BOOLEAN NOT NULL, CONSTRAINT FK_9474526C7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526C7294869C ON comment (article_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE site_map_link (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , is_active BOOLEAN NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , warning_count INTEGER NOT NULL, is_banned BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE article
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE block
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE block_article
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_map_link
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
