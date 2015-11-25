<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151126092713 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE my_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, role VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_106F60157698A6A (role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE my_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, salt VARCHAR(32) NOT NULL, password VARCHAR(60) NOT NULL, email VARCHAR(60) NOT NULL, is_active TINYINT(1) NOT NULL, password_reset_key VARCHAR(32) DEFAULT NULL, auto_login_key VARCHAR(32) NOT NULL, created_date DATETIME DEFAULT NULL, updated_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4DB4FF1DF85E0677 (username), UNIQUE INDEX UNIQ_4DB4FF1DE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_8F02BF9DA76ED395 (user_id), INDEX IDX_8F02BF9DFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE my_user_login (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ip_address VARCHAR(16) NOT NULL, created_date DATETIME DEFAULT NULL, INDEX IDX_BF4E361BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES my_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES my_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE my_user_login ADD CONSTRAINT FK_BF4E361BA76ED395 FOREIGN KEY (user_id) REFERENCES my_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DFE54D947');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DA76ED395');
        $this->addSql('ALTER TABLE my_user_login DROP FOREIGN KEY FK_BF4E361BA76ED395');
        $this->addSql('DROP TABLE my_group');
        $this->addSql('DROP TABLE my_user');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE my_user_login');
    }
}
