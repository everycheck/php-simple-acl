<?php
namespace EveryCheck\SimpleAclBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SimpleAclExtension extends Extension
{
	public function load(array $config, ContainerBuilder $container)
	{
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);
        $container->setParameter('simple_acl.user_class', $config['user_class']);
	
		$loader = new YamlFileLoader($container, new FileLocator(
			[
				__DIR__ . '/../Resources/config/'
			])
		);
		$loader->load('services.yml');
	}
}