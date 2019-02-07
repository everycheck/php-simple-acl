<?php
namespace EveryCheck\SimpleAclBundle\Event;
 
use Symfony\Component\EventDispatcher\Event;
 
class RequestPopulationEvent extends Event
{

	const NAME = 'acl_event.request_population';

    private $entity = null;
    private $users = null;

    public function __construct($entity)
    {
    	$this->entity = $entity;
        $this->users = [];
    }
  
    public function getEntity()
    {
        return $this->entity;
    }

    public function addUser($user)
    {
        $this->users[] = $user;
        return $this;
    }

    public function getAllowedUsers()
    {
        return $this->users;
    }

}