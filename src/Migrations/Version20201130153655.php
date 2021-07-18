<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201130153655 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscription_item DROP FOREIGN KEY FK_282735009A1887DC');
        $this->addSql('ALTER TABLE subscription_item ADD is_active TINYINT(1) NOT NULL, ADD start_date DATE NOT NULL, ADD skip_date_from DATE DEFAULT NULL, ADD skip_date_to DATE DEFAULT NULL, ADD interval_days INT NOT NULL');
        $this->addSql('ALTER TABLE subscription_item ADD CONSTRAINT FK_282735009A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription ADD is_default TINYINT(1) NOT NULL, DROP start_date, DROP skip_date_from, DROP skip_date_to, DROP interval_days');
        $this->addSql('ALTER TABLE card ADD type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP type');
        $this->addSql('ALTER TABLE subscription ADD start_date DATE NOT NULL, ADD skip_date_from DATE DEFAULT NULL, ADD skip_date_to DATE DEFAULT NULL, ADD interval_days INT NOT NULL, DROP is_default');
        $this->addSql('ALTER TABLE subscription_item DROP FOREIGN KEY FK_282735009A1887DC');
        $this->addSql('ALTER TABLE subscription_item DROP is_active, DROP start_date, DROP skip_date_from, DROP skip_date_to, DROP interval_days');
        $this->addSql('ALTER TABLE subscription_item ADD CONSTRAINT FK_282735009A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
    }
}
