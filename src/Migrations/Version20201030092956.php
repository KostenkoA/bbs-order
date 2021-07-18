<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201030092956 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` ADD certificates JSON DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, DROP voucher_amount, CHANGE discount_amount bonus_discount_amount NUMERIC(10, 0) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD discount_amount NUMERIC(10, 0) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD discount_amount NUMERIC(10, 0) NOT NULL, ADD cost NUMERIC(10, 0) NOT NULL, ADD discounts JSON DEFAULT NULL COMMENT \'(DC2Type:json_document)\', ADD is_gift TINYINT(1) DEFAULT \'0\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item DROP discount_amount, DROP cost, DROP discounts, DROP is_gift');
        $this->addSql('ALTER TABLE `order` DROP discount_amount');
        $this->addSql('ALTER TABLE `order` DROP certificates, CHANGE created_at created_at DATETIME DEFAULT NULL, ADD voucher_amount NUMERIC(10, 0) DEFAULT \'0\' NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE bonus_discount_amount discount_amount NUMERIC(10, 0) DEFAULT \'0\' NOT NULL');
    }
}
