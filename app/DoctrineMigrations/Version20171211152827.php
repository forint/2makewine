<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171211152827 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wineproduct_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title TINYTEXT NOT NULL, grape_variety LONGTEXT NOT NULL, vinification LONGTEXT NOT NULL, taste_description LONGTEXT NOT NULL, serving_temperature VARCHAR(255) NOT NULL, ageing_potential VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_98D88A4E2C2AC5D3 (translatable_id), UNIQUE INDEX wineproduct_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wineproduct_translation ADD CONSTRAINT FK_98D88A4E2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES wine_product (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE wineproduct_translation');
    }
}
