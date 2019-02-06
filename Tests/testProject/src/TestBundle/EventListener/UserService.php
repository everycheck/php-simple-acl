<?php
namespace EveryCheck\SimpleAclBundle\tests\testProject\src\TestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Doctrine\ORM\EntityManagerInterface;
use EveryCheck\SimpleAclBundle\Tests\testProject\src\TestBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserService
{
    protected $user;

    public function __construct(EntityManagerInterface $em, $tokenStorage, $session, $eventDispatcher)
    {
        $this->userRepository = $em->getRepository(User::class);
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->eventDispatcher = $eventDispatcher;

        $users = $this->userRepository->findAll();
        if(empty($users))
        {
            $this->user = new User();
            $this->user->setUsername('no-user');
            $em->persist($this->user);
            $em->flush();
        }
        else
        {
            $this->user = $users[0];
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request  = $event->getRequest();

        $username = $request->get('user');
        $user = $this->userRepository->findOneByUsername($username);
        $this->user = empty($user)?$this->user:$user;

        $token = new UsernamePasswordToken($this->user, null, 'main', $this->user->getRoles());
        $this->tokenStorage->setToken($token);

        $this->session->set('_security_main', serialize($token));
        
        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch("security.interactive_login", $event);
    }
}