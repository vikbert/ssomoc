<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211022192929 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE cron_job (id UUID NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(128) NOT NULL, description VARCHAR(128) DEFAULT NULL, enabled BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E6EB8E8ECAEAD4 ON cron_job (command)');
        $this->addSql(
            'CREATE TABLE cron_report (id UUID NOT NULL, due_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished BOOLEAN DEFAULT \'false\' NOT NULL, run_duration DOUBLE PRECISION DEFAULT NULL, exit_code INT DEFAULT NULL, output TEXT DEFAULT NULL, job_id UUID NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN cron_report.due_time IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
    }
}
