<?php
namespace User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use User\Controller\Factory\UserControllerFactory;
use User\Controller\UserController;
use User\Service\Factory\UserManagerFactory;
use User\Service\UserManager;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => UserController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            UserController::class => UserControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            UserManager::class => UserManagerFactory::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    //register entities with orm
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];
