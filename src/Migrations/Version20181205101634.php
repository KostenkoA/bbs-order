<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181205101634 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` CHANGE number number INT DEFAULT NULL, CHANGE price price NUMERIC(10, 0) DEFAULT \'0\' NOT NULL, CHANGE delivery_price delivery_price NUMERIC(10, 0) DEFAULT \'0\' NOT NULL, CHANGE discount_amount discount_amount NUMERIC(10, 0) DEFAULT \'0\' NOT NULL, CHANGE voucher_amount voucher_amount NUMERIC(10, 0) DEFAULT \'0\' NOT NULL, CHANGE cost cost NUMERIC(10, 0) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` CHANGE number number INT NOT NULL, CHANGE price price NUMERIC(10, 0) NOT NULL, CHANGE delivery_price delivery_price NUMERIC(10, 0) NOT NULL, CHANGE discount_amount discount_amount NUMERIC(10, 0) NOT NULL, CHANGE voucher_amount voucher_amount NUMERIC(10, 0) NOT NULL, CHANGE cost cost NUMERIC(10, 0) NOT NULL');
    }
}
