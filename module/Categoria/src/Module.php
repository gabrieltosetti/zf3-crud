<?php

namespace Categoria;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\CategoriaTable::class => function ($container) {
                    $tableGateway = $container->get(Model\CategoriaTableGateway::class);
                    return new Model\CategoriaTable($tableGateway);
                },
                Model\CategoriaTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    return new TableGateway('tb_categoria_produto', $dbAdapter);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\CategoriaController::class => function ($container) {
                    return new Controller\CategoriaController(
                        $container->get(Model\CategoriaTable::class)
                    );
                },
            ],
        ];
    }
}
