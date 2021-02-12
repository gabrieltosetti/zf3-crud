<?php

namespace Categoria\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class CategoriaTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getCategoria($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id_categoria_planejamento' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveCategoria(Categoria $categoria)
    {
        $data = [
            'nome_categoria' => $categoria->nome_categoria,
        ];

        $id = (int) $categoria->id_categoria_planejamento;

        if (!$id) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getCategoria($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Categoria com id %d não existe',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id_categoria_planejamento' => $id]);
    }

    public function deleteCategoria($id)
    {
        $this->tableGateway->delete(['id_categoria_planejamento' => (int) $id]);
    }
}
