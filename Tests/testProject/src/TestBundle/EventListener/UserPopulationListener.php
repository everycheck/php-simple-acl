<?php

namespace EveryCheck\SimpleAclBundle\Tests\testProject\src\TestBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use EveryCheck\SimpleAclBundle\Event\RequestPopulationEvent;
use EveryCheck\SimpleAclBundle\Tests\testProject\src\TestBundle\Entity\TestEntity;

class UserPopulationListener
{

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    public function onPostedResquest(RequestPopulationEvent $event)
    {
        if($event->getEntity() instanceof TestEntity)
        {
            $event->addUser($this->tokenStorage->getToken()->getUser());
        }
    }

}