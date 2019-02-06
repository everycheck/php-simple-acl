<?php
namespace EveryCheck\SimpleAclBundle\Tests\testProject\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
	public function testHelloWorldAction()
	{

        $this->client = static::createClient();
        $this->client->request(
        	"GET",
        	"/test",
        	[],
        	[],
        	[],
        	[],
        	[]
        );

		$this->assertEquals($this->client->getResponse()->getContent(), "hello world");
	}
}