<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201126111156 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE subscription_item (id INT AUTO_INCREMENT NOT NULL, subscription_id INT NOT NULL, internal_id VARCHAR(255) NOT NULL, quantity SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_282735009A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, card_id INT DEFAULT NULL, project VARCHAR(25) NOT NULL, user_ref VARCHAR(255) NOT NULL, status SMALLINT DEFAULT 0 NOT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, middle_name VARCHAR(64) DEFAULT NULL, phone VARCHAR(32) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, delivery_type SMALLINT NOT NULL, delivery_branch VARCHAR(255) DEFAULT NULL, delivery_shop VARCHAR(255) DEFAULT NULL, delivery_carrier SMALLINT DEFAULT NULL, payment_type SMALLINT NOT NULL, region VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, street_type SMALLINT DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, building VARCHAR(16) DEFAULT NULL, apartment VARCHAR(16) DEFAULT NULL, is_active TINYINT(1) NOT NULL, start_date DATE NOT NULL, skip_date_from DATE DEFAULT NULL, skip_date_to DATE DEFAULT NULL, interval_days INT NOT NULL, settlement_id VARCHAR(255) DEFAULT NULL, warehouse_id VARCHAR(36) DEFAULT NULL, street_id VARCHAR(36) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A3C664D34ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription_item ADD CONSTRAINT FK_282735009A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D34ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE card CHANGE life_time life_time DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F52993989A1887DC ON `order` (subscription_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription_item DROP FOREIGN KEY FK_282735009A1887DC');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989A1887DC');
        $this->addSql('DROP TABLE subscription_item');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('ALTER TABLE card CHANGE life_time life_time DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_F52993989A1887DC ON `order`');
        $this->addSql('ALTER TABLE `order` DROP subscription_id');
    }
}
