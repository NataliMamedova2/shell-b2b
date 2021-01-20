<?php

namespace MailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mailer');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('from_email')->end()
                ->scalarNode('from_name')->end()
                ->arrayNode('templates')
                    ->prototype('array')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
