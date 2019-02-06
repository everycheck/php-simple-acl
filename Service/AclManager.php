<?php

namespace EveryCheck\SimpleAclBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

use EveryCheck\SimpleAclBundle\Annotation\Restricted;
use EveryCheck\SimpleAclBundle\Entity\AccessControlListInterface;
use EveryCheck\SimpleAclBundle\Event\RequestPopulationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Annotations\Reader;

class AclManager
{

	public function __construct(EntityManagerInterface $em,EventDispatcherInterface $eventDispatcher,Reader $annotationReader)
	{
		$this->em               = $em;
        $this->eventDispatcher  = $eventDispatcher;
        $this->annotationReader = $annotationReader;
	}


    public function isRegisterForAcl($entityClass)
    {
        $entityReflextionClass = new \ReflectionClass($entityClass);
        $annotation = $this->annotationReader->getClassAnnotation($entityReflextionClass,Restricted::class );
        return empty($annotation) == false;
    }

	public function updateAclOf($entity)
	{
        if($this->isRegisterForAcl(get_class($entity)) == false)
        {
            throw new \Exception("No Acl annotation defined", 1);
        }

        $this->clearAclOf($entity);

        $event = new RequestPopulationEvent($entity);
        $this->eventDispatcher->dispatch(RequestPopulationEvent::NAME,$event);

        foreach ($event->getAllowedUsers() as $user)
        {
            $this->persistAcl($user,$entity);
        }
	}

    public function hasAcces($user,$entity) : bool
    {
        $entityTableName = $this->em->getClassMetadata(get_class($entity))->getTableName();

        $connection = $this->em->getConnection();
        $queryBuilder = $connection->createQueryBuilder();
        
        $queryBuilder
            ->select('*')
            ->from('acl_'.$entityTableName)
            ->where('user_id = :userid')
            ->andWhere('entity_id = :entityid')
            ->setParameter('userid' , $user->getId())
            ->setParameter('entityid' , $entity->getId())
        ;

        $statement = $queryBuilder->execute();
        $result = $statement->fetchAll();

        return count($result) > 0;
    }


    protected function persistAcl($user,$entity)
    {
        $entityTableName = $this->em->getClassMetadata(get_class($entity))->getTableName();
        $data = [
            'user_id' => $user->getId(),
            'entity_id' => $entity->getId(),
        ];
        $connection = $this->em->getConnection();
        $connection->insert('acl_'.$entityTableName,$data);
    }

    public function removeAccess($user,$entity)
    {
        $entityTableName = $this->em->getClassMetadata(get_class($entity))->getTableName();
        $data = [
            'user_id' => $user->getId(),
            'entity_id' => $entity->getId(),
        ];
        $connection = $this->em->getConnection();
        $connection->delete('acl_'.$entityTableName,$data);
    }

    protected function clearAclOf($entity)
    {       
        $entityTableName = $this->em->getClassMetadata(get_class($entity))->getTableName();
        $data = [
            'entity_id' => $entity->getId()
        ];
        $connection = $this->em->getConnection();
        $connection->delete('acl_'.$entityTableName,$data);
    }
}
