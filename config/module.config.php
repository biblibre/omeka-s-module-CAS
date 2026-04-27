<?php

namespace CAS;

return [
    'controller_plugins' => [
        'factories' => [
            'cas' => Service\Mvc\Controller\Plugin\CasFactory::class,
        ]
    ],
    'controllers' => [
        'factories' => [
            'CAS\Controller\Login' => Service\Controller\LoginControllerFactory::class,
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
        'factories' => [
            'CAS\Form\ConfigForm' => Service\Form\ConfigFormFactory::class,
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
    'service_manager' => [
        'factories' => [
            'CAS\CasService' => Service\CasServiceFactory::class,
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
        'factories' => [
            'cas' => Service\View\Helper\CasFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
