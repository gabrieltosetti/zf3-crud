<?php

namespace Produto\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

class ProdutoTable
{
    private TableGateway $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll(): ResultSet
    {
        return $this->tableGateway->select();
    }

    public function getProdutosJoinCategorias(): ResultSet
    {
        $joinSelect = $this->tableGateway->getsql()->select();
        $joinSelect->join(
            'tb_categoria_produto',
            'tb_categoria_produto.id_categoria_planejamento = tb_produto.id_categoria_produto',
            'nome_categoria',
            \Zend\Db\Sql\Select::JOIN_LEFT
        );

        return $this->tableGateway->selectWith($joinSelect);
    }

    public function getProduto(int $id)
    {
        if (!$id) {
            throw new \Exception('Id do produto não informado.');
        }

        $rowset = $this->tableGateway->select(['id_produto' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveProduto(Produto $produto): void
    {
        $data = $produto->getArrayCopy();

        $id = (int) $produto->id_produto;

        if (!$id) {
            $data['data_cadastro'] = $data['data_cadastro'] ?: (new \DateTime())->format('Y-m-d H:i:s');
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getProduto($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Produto com id %d não existe',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id_produto' => $id]);
    }

    public function deleteProduto($id): void
    {
        $this->tableGateway->delete(['id_produto' => (int) $id]);
    }
}
