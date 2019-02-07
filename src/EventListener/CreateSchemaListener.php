<?php

namespace EveryCheck\SimpleAclBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

use EveryCheck\SimpleAclBundle\Service\AclManager;


class CreateSchemaListener implements EventSubscriber
{

    protected $createdTable = [];
    protected $userTableName = '';

    public function __construct(AclManager $manager,$userClass)
    {
        $this->manager = $manager;
        $this->userClass = $userClass;
    }

    public function getSubscribedEvents()
    {
        return array(
            ToolEvents::postGenerateSchemaTable,
            ToolEvents::postGenerateSchema,
        );
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $eventArgs)
    {
        $this->setupUserTable($eventArgs);
        if($this->manager->isRegisterForAcl($eventArgs->getClassMetadata()->name) == false)
        {
           return;
        }

        $schema = $eventArgs->getSchema();
        $entityTable = $eventArgs->getClassTable();
        $aclTable = $schema->createTable('acl_'.$entityTable->getName());

        $aclTable->addColumn('id', 'integer', array(
            'autoincrement' => true,
        ));        
        $aclTable->setPrimaryKey(array('id'));
        $aclTable->addColumn("user_id", "integer");
        $aclTable->addColumn("entity_id", "integer");
        $aclTable->addForeignKeyConstraint($entityTable, array("entity_id"), array("id"));

        $this->createdTable[] = $aclTable->getName();
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $eventArgs)
    {  
        if(empty($this->userTableName))
        {  
            throw new \Exception("Error no user table found", 1);
        }

        $schema = $eventArgs->getSchema();
        $userTable = $schema->getTable($this->userTableName);
        foreach ( $this->createdTable as $tableName)
        {
            $aclTable = $schema->getTable($tableName);
            $aclTable->addForeignKeyConstraint($userTable, array("user_id"), array("id"));
        }
    }

    public function setupUserTable(GenerateSchemaTableEventArgs $eventArgs)
    {
        if( $this->userClass != $eventArgs->getClassMetadata()->name )
        {
            return;
        }

        if(empty($this->userTableName) == false)
        {
            throw new \Exception("Error cannot declarer multiple user table", 1);
        }

        $this->userTableName = $eventArgs->getClassTable()->getName();
    }
}