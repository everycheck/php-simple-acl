<?php

namespace EveryCheck\SimpleAclBundle\Tests\testProject\src\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EveryCheck\SimpleAclBundle\Tests\testProject\src\TestBundle\Entity\TestEntity;

/**
 * 
 */
class TestController extends Controller
{
	/**
	 * @Route("/test", name="test_route", methods={"GET"})
	 */
	public function helloWorldAction()
	{
		$this->get('acl_doctrine_subscriber');

		$entity = new TestEntity();
		$entity->setName("tests");
		$this->get('doctrine.orm.entity_manager')->persist($entity);
		$this->get('doctrine.orm.entity_manager')->flush();

		$this->get('simple_acl')->updateAclOf($entity);

		$user = $this->get('security.token_storage')->getToken()->getUser();

		if($this->get('simple_acl')->hasAcces($user,$entity) ==false)
		{
			throw new \Exception("This should never happend", 1);
		}

		$entities = $this->get('simple_acl')->fetchAllEntityAccessible($user,TestEntity::class);

		if(count($entities) == 0 )
		{
			throw new \Exception("This should never happend", 1);
		}
		
        var_dump($entities);

		foreach ($entities as $entity)
		{
			if(($entity instanceof TestEntity) == false)
			{
				throw new \Exception("This should never happend", 1);
			}
		}

		return new Response("hello world", 200, ["CONTENT-TYPE"=>"application/json"]);
	}
}