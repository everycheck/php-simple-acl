<?php

namespace EveryCheck\Acl\Service;

use Doctrine\ORM\EntityManagerInterface;

use EveryCheck\Acl\Annotation\Restricted;
use EveryCheck\Acl\Entity\AccessControlListInterface;
use EveryCheck\Acl\Event\RequestPopulationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\Annotations\Reader;

use UserBundle\Entity\User;

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

        $event->addUser($this->em->getRepository(User::class)->find(1));

        foreach ($event->getAllowedUsers() as $user)
        {
            $this->persistAcl($user,$entity);
        }
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
