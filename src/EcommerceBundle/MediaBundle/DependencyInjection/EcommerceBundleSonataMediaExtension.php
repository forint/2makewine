<?php

namespace EcommerceBundle\MediaBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EcommerceBundleSonataMediaExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $bundleConfigs;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $configs = [ $this->bundleConfigs['EcommerceBundleSonataMediaBundle'] ];

        $config = $processor->processConfiguration($configuration, $configs);

        /* $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
         $loader->load('provider.xml');
         $loader->load('media.xml');
         $loader->load('twig.xml');
         $loader->load('security.xml');
         $loader->load('extra.xml');
         $loader->load('form.xml');
         $loader->load('gaufrette.xml');*/

        // NEXT_MAJOR: Remove Following lines

       /* $this->configureParameterClass($container, $config);
        $this->configureExtra($container, $config);
        $this->configureBuzz($container, $config);
        $this->configureProviders($container, $config);
        $this->configureAdapters($container, $config);
        $this->configureResizers($container, $config);
        $this->configureClassesToCompile();*/
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureProviders(ContainerBuilder $container, array $config)
    {
        $container->getDefinition('sonata.media.provider.image')
            ->replaceArgument(5, array_map('strtolower', $config['providers']['image']['allowed_extensions']))
            ->replaceArgument(6, $config['providers']['image']['allowed_mime_types'])
            ->replaceArgument(7, new Reference($config['providers']['image']['adapter']))
        ;

        $container->getDefinition('sonata.media.provider.file')
            ->replaceArgument(5, $config['providers']['file']['allowed_extensions'])
            ->replaceArgument(6, $config['providers']['file']['allowed_mime_types'])
        ;

        $container->getDefinition('sonata.media.provider.youtube')->replaceArgument(7, $config['providers']['youtube']['html5']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureBuzz(ContainerBuilder $container, array $config)
    {
        $container->getDefinition('sonata.media.buzz.browser')
            ->replaceArgument(0, new Reference($config['buzz']['connector']));

        foreach ([
            'sonata.media.buzz.connector.curl',
            'sonata.media.buzz.connector.file_get_contents',
        ] as $connector) {
            $container->getDefinition($connector)
                ->addMethodCall('setIgnoreErrors', [$config['buzz']['client']['ignore_errors']])
                ->addMethodCall('setMaxRedirects', [$config['buzz']['client']['max_redirects']])
                ->addMethodCall('setTimeout', [$config['buzz']['client']['timeout']])
                ->addMethodCall('setVerifyPeer', [$config['buzz']['client']['verify_peer']])
                ->addMethodCall('setProxy', [$config['buzz']['client']['proxy']]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureParameterClass(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.media.admin.media.entity', $config['class']['media']);
        $container->setParameter('sonata.media.admin.gallery.entity', $config['class']['gallery']);
        $container->setParameter('sonata.media.admin.gallery_has_media.entity', $config['class']['gallery_has_media']);

        $container->setParameter('sonata.media.media.class', $config['class']['media']);
        $container->setParameter('sonata.media.gallery.class', $config['class']['gallery']);

        $container->getDefinition('sonata.media.form.type.media')->replaceArgument(1, $config['class']['media']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['media'], 'mapOneToMany', [
            'fieldName' => 'galleryHasMedias',
            'targetEntity' => $config['class']['gallery_has_media'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'media',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['gallery_has_media'], 'mapManyToOne', [
            'fieldName' => 'gallery',
            'targetEntity' => $config['class']['gallery'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'galleryHasMedias',
            'joinColumns' => [
                [
                    'name' => 'gallery_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['gallery_has_media'], 'mapManyToOne', [
            'fieldName' => 'media',
            'targetEntity' => $config['class']['media'],
            'cascade' => [
                 'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'galleryHasMedias',
            'joinColumns' => [
                [
                    'name' => 'media_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['gallery'], 'mapOneToMany', [
            'fieldName' => 'galleryHasMedias',
            'targetEntity' => $config['class']['gallery_has_media'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'gallery',
            'orphanRemoval' => true,
            'orderBy' => [
                'position' => 'ASC',
            ],
        ]);

        if ($this->isClassificationEnabled($config)) {
            $collector->addAssociation($config['class']['media'], 'mapManyToOne', [
                'fieldName' => 'category',
                'targetEntity' => $config['class']['category'],
                'cascade' => [
                    'persist',
                ],
                'mappedBy' => null,
                'inversedBy' => null,
                'joinColumns' => [
                    [
                     'name' => 'category_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                    ],
                ],
                'orphanRemoval' => false,
            ]);
        }
    }

    /**
     * Inject CDN dependency to default provider.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureCdnAdapter(ContainerBuilder $container, array $config)
    {
        // add the default configuration for the server cdn
        if ($container->hasDefinition('sonata.media.cdn.server') && isset($config['cdn']['server'])) {
            $container->getDefinition('sonata.media.cdn.server')
                ->replaceArgument(0, $config['cdn']['server']['path'])
            ;
        } else {
            $container->removeDefinition('sonata.media.cdn.server');
        }

        if ($container->hasDefinition('sonata.media.cdn.panther') && isset($config['cdn']['panther'])) {
            $container->getDefinition('sonata.media.cdn.panther')
                ->replaceArgument(0, $config['cdn']['panther']['path'])
                ->replaceArgument(1, $config['cdn']['panther']['username'])
                ->replaceArgument(2, $config['cdn']['panther']['password'])
                ->replaceArgument(3, $config['cdn']['panther']['site_id'])
            ;
        } else {
            $container->removeDefinition('sonata.media.cdn.panther');
        }

        if ($container->hasDefinition('sonata.media.cdn.cloudfront') && isset($config['cdn']['cloudfront'])) {
            $container->getDefinition('sonata.media.cdn.cloudfront')
                ->replaceArgument(0, $config['cdn']['cloudfront']['path'])
                ->replaceArgument(1, $config['cdn']['cloudfront']['key'])
                ->replaceArgument(2, $config['cdn']['cloudfront']['secret'])
                ->replaceArgument(3, $config['cdn']['cloudfront']['distribution_id'])
            ;
        } else {
            $container->removeDefinition('sonata.media.cdn.cloudfront');
        }

        if ($container->hasDefinition('sonata.media.cdn.fallback') && isset($config['cdn']['fallback'])) {
            $container->getDefinition('sonata.media.cdn.fallback')
                ->replaceArgument(0, new Reference($config['cdn']['fallback']['master']))
                ->replaceArgument(1, new Reference($config['cdn']['fallback']['fallback']))
            ;
        } else {
            $container->removeDefinition('sonata.media.cdn.fallback');
        }
    }

    /**
     * Inject filesystem dependency to default provider.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureFilesystemAdapter(ContainerBuilder $container, array $config)
    {
        // add the default configuration for the local filesystem
        if ($container->hasDefinition('sonata.media.adapter.filesystem.local') && isset($config['filesystem']['local'])) {
            $container->getDefinition('sonata.media.adapter.filesystem.local')
                ->addArgument($config['filesystem']['local']['directory'])
                ->addArgument($config['filesystem']['local']['create'])
            ;
        } else {
            $container->removeDefinition('sonata.media.adapter.filesystem.local');
        }

        // add the default configuration for the FTP filesystem
        if ($container->hasDefinition('sonata.media.adapter.filesystem.ftp') && isset($config['filesystem']['ftp'])) {
            $container->getDefinition('sonata.media.adapter.filesystem.ftp')
                ->addArgument($config['filesystem']['ftp']['directory'])
                ->addArgument($config['filesystem']['ftp']['host'])
                ->addArgument([
                    'port' => $config['filesystem']['ftp']['port'],
                    'username' => $config['filesystem']['ftp']['username'],
                    'password' => $config['filesystem']['ftp']['password'],
                    'passive' => $config['filesystem']['ftp']['passive'],
                    'create' => $config['filesystem']['ftp']['create'],
                    'mode' => $config['filesystem']['ftp']['mode'],
                ])
            ;
        } else {
            $container->removeDefinition('sonata.media.adapter.filesystem.ftp');
            $container->removeDefinition('sonata.media.filesystem.ftp');
        }

        // add the default configuration for the S3 filesystem
        if ($container->hasDefinition('sonata.media.adapter.filesystem.s3') && isset($config['filesystem']['s3'])) {
            $container->getDefinition('sonata.media.adapter.filesystem.s3')
                ->replaceArgument(0, new Reference('sonata.media.adapter.service.s3'))
                ->replaceArgument(1, $config['filesystem']['s3']['bucket'])
                ->replaceArgument(2, ['create' => $config['filesystem']['s3']['create'], 'region' => $config['filesystem']['s3']['region'], 'directory' => $config['filesystem']['s3']['directory'], 'ACL' => $config['filesystem']['s3']['acl']])
            ;

            $container->getDefinition('sonata.media.metadata.amazon')
                ->addArgument([
                        'acl' => $config['filesystem']['s3']['acl'],
                        'storage' => $config['filesystem']['s3']['storage'],
                        'encryption' => $config['filesystem']['s3']['encryption'],
                        'meta' => $config['filesystem']['s3']['meta'],
                        'cache_control' => $config['filesystem']['s3']['cache_control'],
                ])
            ;

            if (3 === $config['filesystem']['s3']['sdk_version']) {
                $container->getDefinition('sonata.media.adapter.service.s3')
                ->replaceArgument(0, [
                    'credentials' => [
                        'secret' => $config['filesystem']['s3']['secretKey'],
                        'key' => $config['filesystem']['s3']['accessKey'],
                    ],
                    'region' => $config['filesystem']['s3']['region'],
                    'version' => $config['filesystem']['s3']['version'],
                ])
            ;
            } else {
                $container->getDefinition('sonata.media.adapter.service.s3')
                    ->replaceArgument(0, [
                        'secret' => $config['filesystem']['s3']['secretKey'],
                        'key' => $config['filesystem']['s3']['accessKey'],
                    ])
                ;
            }
        } else {
            $container->removeDefinition('sonata.media.adapter.filesystem.s3');
            $container->removeDefinition('sonata.media.filesystem.s3');
        }

        if ($container->hasDefinition('sonata.media.adapter.filesystem.replicate') && isset($config['filesystem']['replicate'])) {
            $container->getDefinition('sonata.media.adapter.filesystem.replicate')
                ->replaceArgument(0, new Reference($config['filesystem']['replicate']['master']))
                ->replaceArgument(1, new Reference($config['filesystem']['replicate']['slave']))
            ;
        } else {
            $container->removeDefinition('sonata.media.adapter.filesystem.replicate');
            $container->removeDefinition('sonata.media.filesystem.replicate');
        }

        if ($container->hasDefinition('sonata.media.adapter.filesystem.mogilefs') && isset($config['filesystem']['mogilefs'])) {
            $container->getDefinition('sonata.media.adapter.filesystem.mogilefs')
                ->replaceArgument(0, $config['filesystem']['mogilefs']['domain'])
                ->replaceArgument(1, $config['filesystem']['mogilefs']['hosts'])
            ;
        } else {
            $container->removeDefinition('sonata.media.adapter.filesystem.mogilefs');
            $container->removeDefinition('sonata.media.filesystem.mogilefs');
        }

        if ($container->hasDefinition('sonata.media.adapter.filesystem.opencloud') &&
            (isset($config['filesystem']['openstack']) || isset($config['filesystem']['rackspace']))) {
            if (isset($config['filesystem']['openstack'])) {
                $container->setParameter('sonata.media.adapter.filesystem.opencloud.class', 'OpenCloud\OpenStack');
                $settings = 'openstack';
            } else {
                $container->setParameter('sonata.media.adapter.filesystem.opencloud.class', 'OpenCloud\Rackspace');
                $settings = 'rackspace';
            }
            $container->getDefinition('sonata.media.adapter.filesystem.opencloud.connection')
                ->replaceArgument(0, $config['filesystem'][$settings]['url'])
                ->replaceArgument(1, $config['filesystem'][$settings]['secret'])
                ;
            $container->getDefinition('sonata.media.adapter.filesystem.opencloud')
                ->replaceArgument(1, $config['filesystem'][$settings]['containerName'])
                ->replaceArgument(2, $config['filesystem'][$settings]['create_container']);
            $container->getDefinition('sonata.media.adapter.filesystem.opencloud.objectstore')
                ->replaceArgument(1, $config['filesystem'][$settings]['region']);
        } else {
            $container->removeDefinition('sonata.media.adapter.filesystem.opencloud');
            $container->removeDefinition('sonata.media.adapter.filesystem.opencloud.connection');
            $container->removeDefinition('sonata.media.adapter.filesystem.opencloud.objectstore');
            $container->removeDefinition('sonata.media.filesystem.opencloud');
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureExtra(ContainerBuilder $container, array $config)
    {
        if ($config['pixlr']['enabled']) {
            $container->getDefinition('sonata.media.extra.pixlr')
                ->replaceArgument(0, $config['pixlr']['referrer'])
                ->replaceArgument(1, $config['pixlr']['secret'])
            ;
        } else {
            $container->removeDefinition('sonata.media.extra.pixlr');
        }
    }

    /**
     * Add class to compile.
     */
    public function configureClassesToCompile()
    {
        if (\PHP_VERSION_ID >= 70000) {
            return;
        }

        $this->addClassesToCompile([
            'Sonata\\MediaBundle\\CDN\\CDNInterface',
            'Sonata\\MediaBundle\\CDN\\CloudFront',
            'Sonata\\MediaBundle\\CDN\\Fallback',
            'Sonata\\MediaBundle\\CDN\\PantherPortal',
            'Sonata\\MediaBundle\\CDN\\Server',
            'Sonata\\MediaBundle\\Extra\\Pixlr',
            'Sonata\\MediaBundle\\Filesystem\\Local',
            'Sonata\\MediaBundle\\Filesystem\\Replicate',
            'Sonata\\MediaBundle\\Generator\\DefaultGenerator',
            'Sonata\\MediaBundle\\Generator\\GeneratorInterface',
            'Sonata\\MediaBundle\\Generator\\ODMGenerator',
            'Sonata\\MediaBundle\\Generator\\PHPCRGenerator',
            'Sonata\\MediaBundle\\Metadata\\AmazonMetadataBuilder',
            'Sonata\\MediaBundle\\Metadata\\MetadataBuilderInterface',
            'Sonata\\MediaBundle\\Metadata\\NoopMetadataBuilder',
            'Sonata\\MediaBundle\\Metadata\\ProxyMetadataBuilder',
            'Sonata\\MediaBundle\\Model\\Gallery',
            'Sonata\\MediaBundle\\Model\\GalleryHasMedia',
            'Sonata\\MediaBundle\\Model\\GalleryHasMediaInterface',
            'Sonata\\MediaBundle\\Model\\GalleryInterface',
            'Sonata\\MediaBundle\\Model\\GalleryManager',
            'Sonata\\MediaBundle\\Model\\GalleryManagerInterface',
            'Sonata\\MediaBundle\\Model\\Media',
            'Sonata\\MediaBundle\\Model\\MediaInterface',
            'Sonata\\MediaBundle\\Model\\MediaManagerInterface',
            'Sonata\\MediaBundle\\Provider\\BaseProvider',
            'Sonata\\MediaBundle\\Provider\\BaseVideoProvider',
            'Sonata\\MediaBundle\\Provider\\DailyMotionProvider',
            'Sonata\\MediaBundle\\Provider\\FileProvider',
            'Sonata\\MediaBundle\\Provider\\ImageProvider',
            'Sonata\\MediaBundle\\Provider\\MediaProviderInterface',
            'Sonata\\MediaBundle\\Provider\\Pool',
            'Sonata\\MediaBundle\\Provider\\VimeoProvider',
            'Sonata\\MediaBundle\\Provider\\YouTubeProvider',
            'Sonata\\MediaBundle\\Resizer\\ResizerInterface',
            'Sonata\\MediaBundle\\Resizer\\SimpleResizer',
            'Sonata\\MediaBundle\\Resizer\\SquareResizer',
            'Sonata\\MediaBundle\\Security\\DownloadStrategyInterface',
            'Sonata\\MediaBundle\\Security\\ForbiddenDownloadStrategy',
            'Sonata\\MediaBundle\\Security\\PublicDownloadStrategy',
            'Sonata\\MediaBundle\\Security\\RolesDownloadStrategy',
            'Sonata\\MediaBundle\\Security\\SessionDownloadStrategy',
            'Sonata\\MediaBundle\\Templating\\Helper\\MediaHelper',
            'Sonata\\MediaBundle\\Thumbnail\\ConsumerThumbnail',
            'Sonata\\MediaBundle\\Thumbnail\\FormatThumbnail',
            'Sonata\\MediaBundle\\Thumbnail\\ThumbnailInterface',
            'Sonata\\MediaBundle\\Twig\\Extension\\MediaExtension',
        ]);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        // Store SonataAdminBundle configuration for later use
        if (isset($bundles['SonataAdminBundle'])) {
            $this->bundleConfigs['SonataAdminBundle'] = current($container->getExtensionConfig('sonata_admin'));
        }
        // Store AppBundle configuration for use with EcommerceBundle\MediaBundle
        if (isset($bundles['EcommerceBundleSonataMediaBundle'])){
            $this->bundleConfigs['EcommerceBundleSonataMediaBundle'] = current($container->getExtensionConfig('sonata_media'));
        }
    }

    /**
     * Checks if the classification of media is enabled.
     *
     * @param array $config
     *
     * @return bool
     */
    private function isClassificationEnabled(array $config)
    {
        return interface_exists('Sonata\ClassificationBundle\Model\CategoryInterface')
            && !$config['force_disable_category'];
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureAdapters(ContainerBuilder $container, array $config)
    {
        foreach (['gd', 'imagick', 'gmagick'] as $adapter) {
            if ($container->hasParameter('sonata.media.adapter.image.'.$adapter.'.class')) {
                $container->register('sonata.media.adapter.image.'.$adapter, $container->getParameter('sonata.media.adapter.image.'.$adapter.'.class'));
            }
        }
        $container->setAlias('sonata.media.adapter.image.default', $config['adapters']['default']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureResizers(ContainerBuilder $container, array $config)
    {
        if ($container->hasParameter('sonata.media.resizer.simple.class')) {
            $class = $container->getParameter('sonata.media.resizer.simple.class');
            $definition = new Definition($class, [
                new Reference('sonata.media.adapter.image.default'),
                '%sonata.media.resizer.simple.adapter.mode%',
                new Reference('sonata.media.metadata.proxy'),
            ]);
            $container->setDefinition('sonata.media.resizer.simple', $definition);
        }

        if ($container->hasParameter('sonata.media.resizer.square.class')) {
            $class = $container->getParameter('sonata.media.resizer.square.class');
            $definition = new Definition($class, [
                new Reference('sonata.media.adapter.image.default'),
                '%sonata.media.resizer.square.adapter.mode%',
                new Reference('sonata.media.metadata.proxy'),
            ]);
            $container->setDefinition('sonata.media.resizer.square', $definition);
        }

        $container->setAlias('sonata.media.resizer.default', $config['resizers']['default']);
    }
}
