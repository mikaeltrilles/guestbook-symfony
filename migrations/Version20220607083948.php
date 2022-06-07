<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607083948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('INSERT INTO admin (username, roles, password , first_name , last_name) VALUES (\'admin\', \'["ROLE_ADMIN"]\', 
\'$2y$13$XJS.MZwSefEtyfWLtb4L6uCcIG/5AutvMnhfo8QkiL8fM1KZzNVaW\', \'Mikael\', \'TRILLES\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP first_name, DROP last_name');
    }
}
