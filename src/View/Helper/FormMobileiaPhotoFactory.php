<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MIAFile\View\Helper;

/**
 * Description of FormMobileiaPhotoFactory
 *
 * @author matiascamiletti
 */
class FormMobileiaPhotoFactory implements \Zend\ServiceManager\Factory\FactoryInterface
{
    public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null)
    {
        if(!$container instanceof AbstractPluginManager){
            // zend-servicemanager v3. v2 passes the helper manager directly.
            $container = $container->get('ViewHelperManager');
        }
        // Creamos objeto
        return new FormMobileiaPhoto($container->get('headScript'), $container->get('escapeHtmlAttr'));
    }
}