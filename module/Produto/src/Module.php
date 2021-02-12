<?php

namespace Produto;

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
                Model\ProdutoTable::class => function ($container) {
                    $tableGateway = $container->get(Model\ProdutoTableGateway::class);
                    return new Model\ProdutoTable($tableGateway);
                },
                Model\ProdutoTableGateway::class => function ($container) {
                    return new TableGateway('tb_produto', $container->get(AdapterInterface::class));
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\ProdutoController::class => function ($container) {
                    return new Controller\ProdutoController(
                        $container->get(Model\ProdutoTable::class),
                        $container->get(\Categoria\Model\CategoriaTable::class)
                    );
                },
            ],
        ];
    }
}
