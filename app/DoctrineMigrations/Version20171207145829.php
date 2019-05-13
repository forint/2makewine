<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171207145829 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vineyard_wine DROP FOREIGN KEY FK_F02A7BC328A2BD76');
        $this->addSql('ALTER TABLE wine_translation DROP FOREIGN KEY FK_3FF3D0AF2C2AC5D3');
        $this->addSql('ALTER TABLE wine DROP FOREIGN KEY FK_560C646833B92F39');
        $this->addSql('DROP TABLE vineyard_wine');
        $this->addSql('DROP TABLE wine');
        $this->addSql('DROP TABLE wine_translation');
        $this->addSql('DROP TABLE winelabels');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vineyard_wine (vineyard_id INT NOT NULL, wine_id INT NOT NULL, INDEX IDX_F02A7BC3484674D1 (vineyard_id), INDEX IDX_F02A7BC328A2BD76 (wine_id), PRIMARY KEY(vineyard_id, wine_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wine (id INT AUTO_INCREMENT NOT NULL, label_id INT DEFAULT NULL, wine_constructor_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_560C64688F9A1937 (wine_constructor_id), INDEX IDX_560C646833B92F39 (label_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wine_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX wine_translation_unique_translation (translatable_id, locale), INDEX IDX_3FF3D0AF2C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE winelabels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, image VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vineyard_wine ADD CONSTRAINT FK_F02A7BC328A2BD76 FOREIGN KEY (wine_id) REFERENCES wine (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vineyard_wine ADD CONSTRAINT FK_F02A7BC3484674D1 FOREIGN KEY (vineyard_id) REFERENCES vineyards (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wine ADD CONSTRAINT FK_560C646833B92F39 FOREIGN KEY (label_id) REFERENCES winelabels (id)');
        $this->addSql('ALTER TABLE wine ADD CONSTRAINT FK_560C64688F9A1937 FOREIGN KEY (wine_constructor_id) REFERENCES wine_constructors (id)');
        $this->addSql('ALTER TABLE wine_translation ADD CONSTRAINT FK_3FF3D0AF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES wine (id) ON DELETE CASCADE');
    }
}
