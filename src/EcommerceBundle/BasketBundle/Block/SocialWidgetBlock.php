<?php
namespace EcommerceBundle\BasketBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;

class SocialWidgetBlock extends AbstractBlockService
{


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Initialize social scripts',
            'template' => 'AppBundle:Social:initialize.html.twig',
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // dump($blockContext);die;
        // merge settings
       /* $settings = $blockContext->getSettings();
        $basket = $settings['attr']['basket'];
        $form = $settings['attr']['form'];
        $provider = $settings['attr']['provider'];*/
       /* dump($settings['attr']);die;
        $feeds = false;

        if ($settings['url']) {
            $options = array(
                'http' => array(
                    'user_agent' => 'Sonata/RSS Reader',
                    'timeout' => 2,
                )
            );

            // retrieve contents with a specific stream context to avoid php errors
            $content = @file_get_contents($settings['url'], false, stream_context_create($options));

            if ($content) {
                // generate a simple xml element
                try {
                    $feeds = new \SimpleXMLElement($content);
                    $feeds = $feeds->channel->item;
                } catch (\Exception $e) {
                    // silently fail error
                }
            }
        }*/

        return $this->renderResponse($blockContext->getTemplate(), [], $response);
    }
}


