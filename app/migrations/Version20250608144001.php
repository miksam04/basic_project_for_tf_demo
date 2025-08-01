<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608144001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, file_name VARCHAR(191) NOT NULL, INDEX IDX_E01FBE6A4B89032C (post_id), UNIQUE INDEX uq_images_file_name (file_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A4B89032C FOREIGN KEY (post_id) REFERENCES posts (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts CHANGE status status VARCHAR(20) DEFAULT 'draft' NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A4B89032C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE images
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts CHANGE status status VARCHAR(20) NOT NULL
        SQL);
    }
}
