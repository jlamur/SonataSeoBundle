<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\SeoBundle\Tests\DependencyInjection;

use Sonata\SeoBundle\DependencyInjection\Configuration;
use Sonata\SeoBundle\Tests\Helpers\PHPUnit_Framework_TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultConfiguration()
    {
        $config = $this->processConfiguration([[]]);

        $expected = $this->getDefaultConfiguration();

        $this->assertEquals($expected, $config);
    }

    public function testKeysAreNotNormalized()
    {
        $values = [
            'page' => [
                'head' => ['data-example' => 'abc-123'],
                'metas' => [
                    'http-equiv' => [
                        'Content-Type' => 'text/html; charset=utf-8',
                    ],
                ],
            ],
        ];

        $config = $this->processConfiguration([$values]);

        $expected = array_merge_recursive(
            $this->getDefaultConfiguration(),
            $values
        );

        $this->assertEquals($expected, $config);
    }

    public function testWithYamlConfig()
    {
        $values = Yaml::parse(file_get_contents(__DIR__.'/data/config.yml'), true);

        $config = $this->processConfiguration([$values]);

        $expected = array_merge_recursive(
            $this->getDefaultConfiguration(),
            $values
        );

        $this->assertEquals($expected, $config);

        $this->assertEquals('website', $config['page']['metas']['property']['og:type']);
    }

    private function getDefaultConfiguration()
    {
        return [
            'encoding' => 'UTF-8',
            'page' => [
                'default' => 'sonata.seo.page.default',
                'head' => [],
                'metas' => [],
                'separator' => ' - ',
                'title' => 'Sonata Project',
            ],
            'sitemap' => [
                'doctrine_orm' => [],
                'services' => [],
            ],
        ];
    }

    private function processConfiguration(array $configs)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }
}
