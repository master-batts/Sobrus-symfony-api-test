<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927055420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_article ADD author_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE blog_article ADD CONSTRAINT FK_EECCB3E569CCBE9A FOREIGN KEY (author_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EECCB3E569CCBE9A ON blog_article (author_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_article DROP FOREIGN KEY FK_EECCB3E569CCBE9A');
        $this->addSql('DROP INDEX IDX_EECCB3E569CCBE9A ON blog_article');
        $this->addSql('ALTER TABLE blog_article DROP author_id_id');
    }
}
