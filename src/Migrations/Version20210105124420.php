<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210105124420 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item ADD gift_discount JSON DEFAULT NULL COMMENT \'(DC2Type:json_document)\', DROP is_gift');
        $this->addSql(
            <<<'EOF'
UPDATE order_item
SET discounts = (
    SELECT JSON_ARRAY(
                   JSON_OBJECT(
                           '#type', 'App\\Entity\\Value\\OrderItemDiscount',
                           'title', tt.title,
                           'amount', ROUND(tt.amount, 2),
                           'discountRef', ''
                       )) AS discounts
    FROM JSON_TABLE(
                 discounts,
                 "$[*]"
                 COLUMNS (
                     rowid FOR ORDINALITY,
                     title TEXT PATH "$.title",
                     amount FLOAT PATH "$.amount"
                     )
             ) AS tt)

WHERE discounts IS NOT NULL;
EOF
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item ADD is_gift TINYINT(1) DEFAULT \'0\' NOT NULL, DROP gift_discount');
        $this->addSql(
            <<<'EOF'
UPDATE order_item
SET discounts = (
    SELECT JSON_ARRAY(
                   JSON_OBJECT(
                           '#type', 'App\\Entity\\Value\\OrderItemDiscount',
                           'title', tt.title,
                           'amount', ROUND(tt.amount, 2)
                       )) AS discounts
    FROM JSON_TABLE(
                 discounts,
                 "$[*]"
                 COLUMNS (
                     rowid FOR ORDINALITY,
                     title TEXT PATH "$.title",
                     amount FLOAT PATH "$.amount"
                     )
             ) AS tt)

WHERE discounts IS NOT NULL;
EOF
        );
    }
}
