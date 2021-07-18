<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181203142104 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, user_ref VARCHAR(255) DEFAULT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, middle_name VARCHAR(64) DEFAULT NULL, phone VARCHAR(32) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, delivery_type SMALLINT NOT NULL, delivery_branch VARCHAR(255) DEFAULT NULL, delivery_carrier SMALLINT DEFAULT NULL, payment_type SMALLINT NOT NULL, status INT NOT NULL, region VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, street_type SMALLINT DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, building VARCHAR(16) DEFAULT NULL, apartment VARCHAR(16) DEFAULT NULL, comment VARCHAR(256) DEFAULT NULL, price NUMERIC(10, 0) NOT NULL, delivery_price NUMERIC(10, 0) NOT NULL, discount_amount NUMERIC(10, 0) NOT NULL, voucher_amount NUMERIC(10, 0) NOT NULL, cost NUMERIC(10, 0) NOT NULL, call_back TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status_history (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_471AD77E8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_status_history ADD CONSTRAINT FK_471AD77E8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('DROP TABLE shop_order');
        $this->addSql('ALTER TABLE order_item ADD title VARCHAR(255) NOT NULL, ADD title_ukr VARCHAR(255) NOT NULL, ADD short_description VARCHAR(255) NOT NULL, ADD short_description_ukr VARCHAR(255) NOT NULL, ADD quantity SMALLINT NOT NULL, ADD total_price NUMERIC(10, 0) NOT NULL, ADD site_presentation JSON NOT NULL, ADD color_presentation JSON DEFAULT NULL, ADD height_presentation JSON DEFAULT NULL, ADD age_category JSON DEFAULT NULL, ADD images JSON DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_status_history DROP FOREIGN KEY FK_471AD77E8D9F6D38');
        $this->addSql('CREATE TABLE shop_order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_unicode_ci, phone_number VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, delivery_type INT NOT NULL, payment_type INT NOT NULL, status INT NOT NULL, store_id INT DEFAULT NULL, store_name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, city VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, mail_department_no VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, street VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, building VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_unicode_ci, apartment VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_unicode_ci, comment VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_unicode_ci, price_total NUMERIC(10, 0) DEFAULT NULL, discount NUMERIC(10, 0) DEFAULT NULL, shipping_price NUMERIC(10, 0) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_status_history');
        $this->addSql('ALTER TABLE order_item DROP title, DROP title_ukr, DROP short_description, DROP short_description_ukr, DROP quantity, DROP total_price, DROP site_presentation, DROP color_presentation, DROP height_presentation, DROP age_category, DROP images, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES shop_order (id)');
    }
}
