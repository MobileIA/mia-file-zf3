<?php

namespace MIAFile\View\Helper;

/**
 * Description of FormMobileiaGalleryFactory
 *
 * @author matiascamiletti
 */
class FormMobileiaGalleryFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        if(!$container instanceof AbstractPluginManager){
            // zend-servicemanager v3. v2 passes the helper manager directly.
            $container = $container->get('ViewHelperManager');
        }
        // Creamos objeto
        return new FormMobileiaGallery($container->get('headScript'), $container->get('escapeHtmlAttr'), $container->get('basePath'));
    }
}
