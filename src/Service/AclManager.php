<?php

namespace EveryCheck\Acl\Service;

use Doctrine\ORM\EntityManagerInterface;

use EveryCheck\Acl\Annotation\Acl;
use EveryCheck\Acl\Entity\AccessControlListInterface;
use EveryCheck\Acl\Event\RequestPopulationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface

class AclManager
{

	public function __construct(EntityManagerInterface $em,EventDispatcherInterface $eventDispatcher)
	{
		$this->em              = $em;
        $this->eventDispatcher = $eventDispatcher;
	}

    private function getAclClassFromAnnotation($entity):string
    {
        $entityReflextionClass = new \ReflectionClass(get_class($entity));
        $annotation = $this->annotationReader->getClassAnnotation($entityReflextionClass,Acl::class );
        if(empty($annotation)) 
        {
            throw new Exception("No Acl annotation defined", 1);
        }

        $aclClass = $annotation->getClass();

        if($aclClass instanceof AccessControlListInterface)
        {
            return $aclClass;
        }
        throw new Exception("Invalid acl class", 1);
    }

	public function updateAclOf($entity)
	{
        $aclClass = $this->getAclClassFromAnnotation($entity);

        $this->clearAclOf($aclClass,$entity);

        $event = new RequestPopulationEvent($entity);
        $this->eventDispatcher->dispatch(RequestPopulationEvent::NAME,$event);

        foreach ($event->getAllowedUsers() as $user)
        {
            $acl = new $aclClass();
            $acl->setUser($user);
            $acl->setEntity($entity);
            $this->em->persist($acl);
        }
	}

    protected function clearAclOf($aclClass,$entity)
    {       
        $acls = $this->em->getRepository($aclClass)->findByEntity($entity);

        foreach($acls as $acl)
        {
            $this->em->remove($acl);
        }
    }
}
