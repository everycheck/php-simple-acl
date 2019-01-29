<?php
namespace Everycheck\Acl\Event;
 
use Symfony\Component\EventDispatcher\Event;
 
class RequestPopulationEvent extends Event
{

	const NAME = 'acl_event.request_population';

    private $study = null;
    private $users = null;

    public function __construct(Study $study)
    {
    	$this->study = $study;
        $this->users = [];
    }
  
    public function getStudy()
    {
        return $this->study;
    }

    public function addUser(OwnerInterface $user)
    {
        $this->users[] = $user;
        return $this;
    }

    public function getAllowedUsers()
    {
        return $this->users;
    }

}