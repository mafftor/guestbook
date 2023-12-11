<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231211222725 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE guestbook (
          id INT AUTO_INCREMENT NOT NULL,
          author_id INT NOT NULL,
          text LONGTEXT NOT NULL,
          image VARCHAR(255) DEFAULT NULL,
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          is_approved TINYINT(1) NOT NULL,
          INDEX IDX_88704138F675F31B (author_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (
          id INT AUTO_INCREMENT NOT NULL,
          email VARCHAR(180) NOT NULL,
          roles JSON NOT NULL,
          password VARCHAR(255) NOT NULL,
          UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE
          guestbook
        ADD
          CONSTRAINT FK_88704138F675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE guestbook DROP FOREIGN KEY FK_88704138F675F31B');
        $this->addSql('DROP TABLE guestbook');
        $this->addSql('DROP TABLE `user`');
    }
}
