<?php
namespace EveryCheck\SimpleAclBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('simple_acl');

		$rootNode
		    //    ->addDefaultsIfNotSet()
			->children()
					->scalarNode('user_class')
						->isRequired()
			//			->defaultValue('UserBundle\Entity\User')
					->end()
				->end()
			->end()
		;

		return $treeBuilder;
	}				
}

?>