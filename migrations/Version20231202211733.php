<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202211733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('CREATE TABLE IF NOT EXISTS `_log_time` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `project_key` VARCHAR(127) NOT NULL,
            `date` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `hours` FLOAT UNSIGNED NOT NULL,
            `comment` TEXT NOT NULL,
            `repaid` TINYINT(4) NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');

        $this->addSql("CREATE TABLE IF NOT EXISTS `feedback` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            `type` VARCHAR(255) NOT NULL DEFAULT 'appeal', 
            `name` VARCHAR(255) NOT NULL, 
            `email` VARCHAR(255) NOT NULL, 
            `text` TEXT NOT NULL, 
            `browser` VARCHAR(255) DEFAULT NULL, 
            `ip_addr` VARCHAR(255) DEFAULT NULL, 
            `page` VARCHAR(255) DEFAULT NULL, 
            `product` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        $this->addSql("CREATE TABLE IF NOT EXISTS `posts` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `type` VARCHAR(255) DEFAULT 'project',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        $this->addSql("CREATE TABLE IF NOT EXISTS `posts_i18n` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `post_id` INT(11) UNSIGNED NOT NULL,
            `lang` VARCHAR(255) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `text` TEXT DEFAULT '',
            `excerpt` TEXT DEFAULT '',
            `tags` VARCHAR(255) DEFAULT '',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`)
        )");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE _log_time');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE posts_i18n');
    }
}
