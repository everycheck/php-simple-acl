<?php
namespace EveryCheck\SimpleAclBundle\Tests\testProject\src\TestBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * User
 *
 * @ORM\Entity
 * @ORM\Table(name="s_user")
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")  
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=255, unique=true)  
     */
    private $username;


    public function getId()
    {
        return $this->id;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return [];
    }

    public function getPassword()
    {
        return $this->username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function eraseCredentials()
    {
        
    }
}
