<?php

namespace Example\Migrations\TestAntiMattr\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140822185744 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Simple Migration Example created using a template";
    }

    public function up(Database $db)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $collectionName = 'simple_1';
        $collection  = $db->selectCollection($collectionName);
        $this->analyze($collection);
        $collection->ensureIndex(['pk' => ['cpf' => 1]]);
        $collection->insert(['name'=>'Giant Factory','age'=>25]);

    }

    public function down(Database $db)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $collectionName = 'simple_1';
        $collection  = $db->selectCollection($collectionName);
        $this->analyze($collection);
        $collection->deleteIndex(['pk' => ['cpf' => 1]]);
        $collection->remove(['name'=>'Giant Factory','age'=>25]);

    }
}
