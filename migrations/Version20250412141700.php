<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412141700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE contextos (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, code_translate VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE plantillas (id INT AUTO_INCREMENT NOT NULL, idcontext_id INT NOT NULL, code VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_E91A52B7A6A9DCF5 (idcontext_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE variables (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE variables_contextos (variables_id INT NOT NULL, contextos_id INT NOT NULL, INDEX IDX_5EF71426ED82107C (variables_id), INDEX IDX_5EF71426A98330FB (contextos_id), PRIMARY KEY(variables_id, contextos_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plantillas ADD CONSTRAINT FK_E91A52B7A6A9DCF5 FOREIGN KEY (idcontext_id) REFERENCES contextos (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variables_contextos ADD CONSTRAINT FK_5EF71426ED82107C FOREIGN KEY (variables_id) REFERENCES variables (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variables_contextos ADD CONSTRAINT FK_5EF71426A98330FB FOREIGN KEY (contextos_id) REFERENCES contextos (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE plantillas DROP FOREIGN KEY FK_E91A52B7A6A9DCF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variables_contextos DROP FOREIGN KEY FK_5EF71426ED82107C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE variables_contextos DROP FOREIGN KEY FK_5EF71426A98330FB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contextos
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE plantillas
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE variables
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE variables_contextos
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
