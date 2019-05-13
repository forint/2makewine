<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171213084028 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wine_product_icon (wine_product_id INT NOT NULL, icon_id INT NOT NULL, INDEX IDX_7393535F61A94C00 (wine_product_id), INDEX IDX_7393535F54B9D732 (icon_id), PRIMARY KEY(wine_product_id, icon_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wine_product_icon ADD CONSTRAINT FK_7393535F61A94C00 FOREIGN KEY (wine_product_id) REFERENCES wine_product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wine_product_icon ADD CONSTRAINT FK_7393535F54B9D732 FOREIGN KEY (icon_id) REFERENCES icons (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE wine_product_icon');
    }
}
