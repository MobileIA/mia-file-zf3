<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MIAFile;

return [
    'view_helpers' => [
        'aliases' => [
            'formMobileiaFile' => View\Helper\FormMobileiaFile::class,
            'formMobileiaGallery' => View\Helper\FormMobileiaGallery::class,
            'formMobileiaPhoto' => View\Helper\FormMobileiaPhoto::class,
        ],
        'factories' => [
            View\Helper\FormMobileiaFile::class => View\Helper\FormMobileiaFileFactory::class,
            View\Helper\FormMobileiaGallery::class => View\Helper\FormMobileiaGalleryFactory::class,
            View\Helper\FormMobileiaPhoto::class => View\Helper\FormMobileiaPhotoFactory::class,
        ],
    ]
];
