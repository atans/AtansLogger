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
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'AtansLogger\Controller\Error' => 'AtansLogger\Controller\ErrorController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);