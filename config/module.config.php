<?php

namespace CAS;

return [
    'controllers' => [
        'factories' => [
            'CAS\Controller\Login' => Service\Controller\LoginControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'isCasUser' => Service\ControllerPlugin\IsCasUserFactory::class,
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'CAS\Form\ConfigForm' => Form\ConfigForm::class,
        ],
    ],
    'js_translate_strings' => [
        'Log in with CAS', // @translate
    ],
    'navigation_links' => [
        'factories' => [
            'casLoginUrl' => Service\Site\Navigation\Link\CasLoginUrlFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'cas' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/cas',
                    'defaults' => [
                        '__NAMESPACE__' => 'CAS\Controller',
                    ],
                ],
                'child_routes' => [
                    'login' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => 'Login',
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'validate' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/validate',
                            'defaults' => [
                                'controller' => 'Login',
                                'action' => 'validate',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'casLoginUrl' => View\Helper\CasLoginUrl::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
