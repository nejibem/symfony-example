<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151126093844 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `my_user` VALUES (1,'root','f5eb51e7383b1fadf29cb54b8d95abc9','\$2y$13\$f5eb51e7383b1fadf29cbufmVfk2aOJ2Q37/GAuM/VnekmQ20KVdy','root@some-domain.com',1,NULL,'98d787a15413eed69b05717ae3ef291e','2015-11-26 09:27:59',NULL)");
        $this->addSql("INSERT INTO `user_group` VALUES (1,2)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM my_user where id=1");
        $this->addSql("DELETE FROM user_group where user_id=1");
    }
}