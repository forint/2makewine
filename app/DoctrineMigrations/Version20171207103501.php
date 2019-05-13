<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171207103501 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wine_product ADD wine_recipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wine_product ADD CONSTRAINT FK_1B64CC38C0515EE7 FOREIGN KEY (wine_recipe_id) REFERENCES wine_constructors (id)');
        $this->addSql('CREATE INDEX IDX_1B64CC38C0515EE7 ON wine_product (wine_recipe_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE wine_product DROP FOREIGN KEY FK_1B64CC38C0515EE7');
        $this->addSql('DROP INDEX IDX_1B64CC38C0515EE7 ON wine_product');
        $this->addSql('ALTER TABLE wine_product DROP wine_recipe_id');
    }
}
