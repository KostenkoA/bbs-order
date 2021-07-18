<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190722133816 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item ADD display_article VARCHAR(255) NOT NULL, ADD category JSON NOT NULL, ADD folder_category JSON NOT NULL, ADD brand JSON NOT NULL, DROP description, DROP short_description, DROP short_description_ukr, DROP height_presentation, DROP length_presentation');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item ADD description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD short_description_ukr VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD height_presentation JSON DEFAULT NULL, ADD length_presentation JSON DEFAULT NULL, DROP category, DROP folder_category, DROP brand, CHANGE display_article short_description VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
