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

		return new Response("hello world", 200, ["CONTENT-TYPE"=>"application/json"]);
	}
}