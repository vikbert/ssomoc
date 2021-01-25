<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210122142954 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE `user` (id VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', auth_token VARCHAR(255) DEFAULT NULL, access_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user`');
    }
}
