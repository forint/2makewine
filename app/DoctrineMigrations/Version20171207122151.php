<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171207122151 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wine_product ADD vineyard_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wine_product ADD CONSTRAINT FK_1B64CC38484674D1 FOREIGN KEY (vineyard_id) REFERENCES vineyards (id)');
        $this->addSql('CREATE INDEX IDX_1B64CC38484674D1 ON wine_product (vineyard_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wine_product DROP FOREIGN KEY FK_1B64CC38484674D1');
        $this->addSql('DROP INDEX IDX_1B64CC38484674D1 ON wine_product');
        $this->addSql('ALTER TABLE wine_product DROP vineyard_id');
    }
}
