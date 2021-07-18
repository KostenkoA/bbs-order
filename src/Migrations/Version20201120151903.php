<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201120151903 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, hash VARCHAR(36) NOT NULL, user_ref VARCHAR(255) NOT NULL, project VARCHAR(255) NOT NULL, method SMALLINT NOT NULL, is_verified TINYINT(1) DEFAULT \'0\' NOT NULL, mask VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, life_time DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_161498D3D1B862B8 (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD card_id INT DEFAULT NULL, ADD type SMALLINT DEFAULT NULL, ADD refund_status SMALLINT DEFAULT NULL, CHANGE order_id order_id INT DEFAULT NULL');
        $this->addSql('UPDATE payment SET type=0');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D4ACC9A20 ON payment (card_id)');
        $this->addSql('ALTER TABLE order_item CHANGE is_gift is_gift TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D4ACC9A20');
        $this->addSql('DROP TABLE card');
        $this->addSql('ALTER TABLE order_item CHANGE is_gift is_gift TINYINT(1) DEFAULT \'0\'');
        $this->addSql('DROP INDEX IDX_6D28840D4ACC9A20 ON payment');
        $this->addSql('ALTER TABLE payment DROP card_id, DROP type, DROP refund_status, CHANGE order_id order_id INT NOT NULL');
    }
}
