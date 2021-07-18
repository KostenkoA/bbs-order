<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190128173411 extends AbstractMigration
{
    public function __construct(Version $version)
    {
        parent::__construct($version);
    }


    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('alter table `order` change `number` `hash` varchar(36) not null');
        $this->addSql('ALTER TABLE `order` RENAME INDEX UNIQ_F529939896901F54 TO UNIQ_F5299398D1B862B8');
        $this->addSql('ALTER TABLE `order` ADD number VARCHAR(12) NOT NULL');

        $this->addSql(
            <<<EOF
CREATE TRIGGER order_number_set
  BEFORE INSERT
  ON `order`
  FOR EACH ROW
BEGIN
  SET NEW.number = CONVERT(DATE_FORMAT(NEW.created_at, '%Y%m%d'), UNSIGNED INTEGER) * 10000 + (
    SELECT count(*)
    FROM `order` AS o
    WHERE o.created_at >= DATE_FORMAT(NEW.created_at, '%Y-%m-%d 00:00:00')
      AND o.created_at <= DATE_FORMAT(NEW.created_at, '%Y-%m-%d 23:59:59')
  ) + 1;
END;
EOF
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE `order` DROP number');

        $this->addSql('ALTER TABLE `order` change `hash` `number` varchar(36) not null');
        $this->addSql('ALTER TABLE `order` RENAME INDEX UNIQ_F5299398D1B862B8 TO UNIQ_F529939896901F54 ');
        $this->addSql('DROP TRIGGER IF EXISTS order_number_set');
    }

    public function postUp(Schema $schema)
    {
        $this->write('STARTING Converting exist order`s numbers');
        $this->connection->executeUpdate(
            <<<EOF
UPDATE `order` AS o
  INNER JOIN (
    SELECT o1.id,
           (
             SELECT COUNT(*)
             FROM `order` AS o2
             WHERE DATE(o1.created_at) = DATE(o2.created_at)
               AND (o1.created_at > o2.created_at
               OR (o1.created_at = o2.created_at AND o1.id > o2.id)
               )
           ) AS today_count
    FROM `order` AS o1
  ) AS o1 ON o.id = o1.id
SET number = (SELECT CONVERT(DATE_FORMAT(created_at, '%Y%m%d'), UNSIGNED INTEGER) * 10000) + o1.today_count + 1
EOF
        );
        $this->write('END Converting exist order`s numbers');
    }
}
