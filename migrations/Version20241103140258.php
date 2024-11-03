<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103140258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, sum VARCHAR(7) DEFAULT \'1.00\' NOT NULL, service VARCHAR(127) DEFAULT \'\' NOT NULL, email VARCHAR(63) DEFAULT \'\' NOT NULL, phone VARCHAR(31) DEFAULT \'\' NOT NULL, text LONGTEXT DEFAULT \'\' NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', tracking_id VARCHAR(127) NOT NULL, params JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_6D28840D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products CHANGE price price INT NOT NULL, CHANGE status status SMALLINT NOT NULL, CHANGE category category VARCHAR(255) NOT NULL, CHANGE tag tag VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D4584665A');
        $this->addSql('DROP TABLE payment');
        $this->addSql('ALTER TABLE products CHANGE price price INT DEFAULT 750 NOT NULL, CHANGE status status TINYINT(1) DEFAULT 1 NOT NULL, CHANGE category category VARCHAR(255) DEFAULT \'general\' NOT NULL, CHANGE tag tag VARCHAR(255) DEFAULT \'\' NOT NULL, CHANGE description description TEXT DEFAULT \'\' NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
