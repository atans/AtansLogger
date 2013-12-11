<?php
namespace AtansLogger;

return array(
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'atanslogger' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/log',
                    'defaults' => array(
                        '__NAMESPACE__'  => 'AtansLogger\Controller',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'error' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/error',
                            'defaults' => array(
                                '__NAMESPACE__'  => 'AtansLogger\Controller',
                                'controller'     => 'Error',
                                'action'         => 'index'
                            ),
                        ),
                    ),
                    'event' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/event',
                            'defaults' => array(
                                '__NAMESPACE__'  => 'AtansLogger\Controller',
                                'controller'     => 'Event',
                                'action'         => 'index'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../languages',
                'pattern' => '%s.mo',
                'text_domain' => __NAMESPACE__,
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'AtansLogger\Controller\Error' => 'AtansLogger\Controller\ErrorController',
            'AtansLogger\Controller\Event' => 'AtansLogger\Controller\EventController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);