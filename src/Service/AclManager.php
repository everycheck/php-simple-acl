<?php

namespace EveryCheck\Acl\Service;

use Doctrine\ORM\EntityManagerInterface;

use EveryCheck\Acl\Annotation\Acl;
use EveryCheck\Acl\Entity\AccessControlListInterface;
use EveryCheck\Acl\Event\RequestPopulationEvent;
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

    private function getAclClassFromAnnotation($entity):string
    {
        $entityReflextionClass = new \ReflectionClass(get_class($entity));
        $annotation = $this->annotationReader->getClassAnnotation($entityReflextionClass,Acl::class );
        if(empty($annotation)) 
        {
            throw new \Exception("No Acl annotation defined", 1);
        }

        $aclClass = $annotation->getClass();

        if(class_exists($aclClass) ==  false)
        {
            throw new \Exception("Class $aclClass does not exist", 1);
        }

        if(new $aclClass() instanceof AccessControlListInterface)
        {
            return $aclClass;
        }
        throw new \Exception("Invalid acl class", 1);
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
