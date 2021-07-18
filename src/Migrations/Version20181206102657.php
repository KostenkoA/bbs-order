<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181206102657 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` CHANGE number number VARCHAR(36) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F529939896901F54 ON `order` (number)');
        $this->addSql('ALTER TABLE order_item CHANGE site_presentation size_presentation JSON NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD length_presentation JSON DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_F529939896901F54 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE number number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item CHANGE size_presentation site_presentation JSON NOT NULL');
        $this->addSql('ALTER TABLE order_item DROP length_presentation');
    }
}
