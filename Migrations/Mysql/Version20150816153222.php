<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20150816153222 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		// this up() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("CREATE TABLE techdivision_scheduler_domain_model_cron (persistence_object_identifier VARCHAR(40) NOT NULL, name VARCHAR(255) NOT NULL, croncommand VARCHAR(255) NOT NULL, parameter VARCHAR(255) DEFAULT NULL, cronexpression VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_E59D954B5E237E06 (name), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
		$this->addSql("CREATE TABLE techdivision_scheduler_domain_model_task (persistence_object_identifier VARCHAR(40) NOT NULL, cron VARCHAR(40) DEFAULT NULL, status VARCHAR(255) NOT NULL, processid VARCHAR(255) DEFAULT NULL, createtime DATETIME NOT NULL, starttime DATETIME DEFAULT NULL, result VARCHAR(255) DEFAULT NULL, INDEX IDX_A02E4E9617CD00F8 (cron), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
		$this->addSql("CREATE TABLE techdivision_scheduler_domain_model_taskhistory (persistence_object_identifier VARCHAR(40) NOT NULL, status VARCHAR(255) NOT NULL, createtime DATETIME NOT NULL, starttime DATETIME DEFAULT NULL, endtime DATETIME DEFAULT NULL, croncommand VARCHAR(255) NOT NULL, taskname VARCHAR(255) NOT NULL, result VARCHAR(255) DEFAULT NULL, PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
		$this->addSql("ALTER TABLE techdivision_scheduler_domain_model_task ADD CONSTRAINT FK_A02E4E9617CD00F8 FOREIGN KEY (cron) REFERENCES techdivision_scheduler_domain_model_cron (persistence_object_identifier)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		// this down() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE techdivision_scheduler_domain_model_task DROP FOREIGN KEY FK_A02E4E9617CD00F8");
		$this->addSql("DROP TABLE techdivision_scheduler_domain_model_cron");
		$this->addSql("DROP TABLE techdivision_scheduler_domain_model_task");
		$this->addSql("DROP TABLE techdivision_scheduler_domain_model_taskhistory");
	}
}