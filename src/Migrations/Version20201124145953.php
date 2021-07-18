<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201124145953 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` CHANGE settlement_id settlement_id VARCHAR(255) DEFAULT NULL, CHANGE warehouse_id warehouse_id VARCHAR(255) DEFAULT NULL, CHANGE street_id street_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` CHANGE settlement_id settlement_id SMALLINT DEFAULT NULL, CHANGE warehouse_id warehouse_id VARCHAR(36) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE street_id street_id VARCHAR(36) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
