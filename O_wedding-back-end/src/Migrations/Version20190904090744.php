<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190904090744 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_provider (project_id INT NOT NULL, provider_id INT NOT NULL, INDEX IDX_AF8AF847166D1F9C (project_id), INDEX IDX_AF8AF847A53A8AA (provider_id), PRIMARY KEY(project_id, provider_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_guest (project_id INT NOT NULL, guest_id INT NOT NULL, INDEX IDX_BFE293AB166D1F9C (project_id), INDEX IDX_BFE293AB9A4AA658 (guest_id), PRIMARY KEY(project_id, guest_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_provider ADD CONSTRAINT FK_AF8AF847166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_provider ADD CONSTRAINT FK_AF8AF847A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_guest ADD CONSTRAINT FK_BFE293AB166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_guest ADD CONSTRAINT FK_BFE293AB9A4AA658 FOREIGN KEY (guest_id) REFERENCES guest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD user_id INT DEFAULT NULL, ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EEA76ED395 ON project (user_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEAE80F5DF ON project (department_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE project_provider');
        $this->addSql('DROP TABLE project_guest');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEAE80F5DF');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EEA76ED395 ON project');
        $this->addSql('DROP INDEX IDX_2FB3D0EEAE80F5DF ON project');
        $this->addSql('ALTER TABLE project DROP user_id, DROP department_id');
    }
}
