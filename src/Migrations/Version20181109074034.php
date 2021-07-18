<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181109074034 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shop_order (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(64) DEFAULT NULL, phone_number VARCHAR(32) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, delivery_type INT NOT NULL, payment_type INT NOT NULL, status INT NOT NULL, store_id INT DEFAULT NULL, store_name VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, mail_department_no VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, building VARCHAR(16) DEFAULT NULL, apartment VARCHAR(16) DEFAULT NULL, comment VARCHAR(256) DEFAULT NULL, price_total NUMERIC(10, 0) DEFAULT NULL, discount NUMERIC(10, 0) DEFAULT NULL, shipping_price NUMERIC(10, 0) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, internal_id VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 0) NOT NULL, INDEX IDX_52EA1F098D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES shop_order (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('DROP TABLE shop_order');
        $this->addSql('DROP TABLE order_item');
    }
}
