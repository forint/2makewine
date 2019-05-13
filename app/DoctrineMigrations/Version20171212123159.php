<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171212123159 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vineyards ADD winemaker_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vineyards ADD CONSTRAINT FK_C1149CF3D4BC772B FOREIGN KEY (winemaker_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_C1149CF3D4BC772B ON vineyards (winemaker_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vineyards DROP FOREIGN KEY FK_C1149CF3D4BC772B');
        $this->addSql('DROP INDEX IDX_C1149CF3D4BC772B ON vineyards');
        $this->addSql('ALTER TABLE vineyards DROP winemaker_id');
    }
}
