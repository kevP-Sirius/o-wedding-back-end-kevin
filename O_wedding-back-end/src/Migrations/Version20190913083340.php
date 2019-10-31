<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913083340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guest ADD type_id INT DEFAULT NULL, DROP type');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_ACB79A35C54C8C93 ON guest (type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35C54C8C93');
        $this->addSql('DROP INDEX IDX_ACB79A35C54C8C93 ON guest');
        $this->addSql('ALTER TABLE guest ADD type VARCHAR(64) DEFAULT NULL COLLATE utf8_unicode_ci, DROP type_id');
    }
}
