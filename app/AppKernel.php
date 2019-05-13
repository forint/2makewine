<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
//use EcommerceBundle\CustomerBundle as Customer;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new AppBundle\AppBundle(),
            // New Bundles
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),

            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\NotificationBundle\SonataNotificationBundle(),
            new Sonata\UserBundle\SonataUserBundle(),

            new Sonata\CustomerBundle\SonataCustomerBundle(),
            new \EcommerceBundle\CustomerBundle\EcommerceBundleSonataCustomerBundle(),

            new Sonata\ProductBundle\SonataProductBundle(),
            new \EcommerceBundle\ProductBundle\EcommerceBundleSonataProductBundle(),

            new Sonata\BasketBundle\SonataBasketBundle(),
            new \EcommerceBundle\BasketBundle\EcommerceBundleSonataBasketBundle(),

            new Sonata\OrderBundle\SonataOrderBundle(),
            new \EcommerceBundle\OrderBundle\EcommerceBundleSonataOrderBundle(),

            new Sonata\InvoiceBundle\SonataInvoiceBundle(),
            new \EcommerceBundle\InvoiceBundle\EcommerceBundleSonataInvoiceBundle(),

            new Sonata\MediaBundle\SonataMediaBundle(),
            new \EcommerceBundle\MediaBundle\EcommerceBundleSonataMediaBundle(),

            new Sonata\DeliveryBundle\SonataDeliveryBundle(),
            new \EcommerceBundle\DeliveryBundle\EcommerceBundleSonataDeliveryBundle(),

            new Sonata\PaymentBundle\SonataPaymentBundle(),
            new \EcommerceBundle\PaymentBundle\EcommerceBundleSonataPaymentBundle(),

            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new \EcommerceBundle\ClassificationBundle\EcommerceBundleSonataClassificationBundle(),

            new Sonata\PageBundle\SonataPageBundle(),
            new \EcommerceBundle\PageBundle\EcommerceBundleSonataPageBundle(),

            new Sonata\PriceBundle\SonataPriceBundle(),
            new \EcommerceBundle\PriceBundle\EcommerceBundleSonataPriceBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\SeoBundle\SonataSeoBundle(),
            new Sonata\CacheBundle\SonataCacheBundle(),
            // SonataMarkItUpBundle is deprecated. All assets are now available in formatter bundle
            // new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new \Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),

            new Translation\Bundle\TranslationBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
            new A2lix\AutoFormBundle\A2lixAutoFormBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new JMS\I18nRoutingBundle\JMSI18nRoutingBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        bcscale(2);

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
