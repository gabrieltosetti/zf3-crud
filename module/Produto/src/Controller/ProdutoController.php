<?php

namespace Produto\Controller;

use Categoria\Model\CategoriaTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Produto\Form\ProdutoForm;
use Produto\Model\Produto;
use Produto\Model\ProdutoTable;

class ProdutoController extends AbstractActionController
{
    private $table;
    private $categoriaTable;

    public function __construct(ProdutoTable $table, CategoriaTable $categoriaTable)
    {
        $this->table = $table;
        $this->categoriaTable = $categoriaTable;
    }

    public function indexAction()
    {
        return new ViewModel([
            'produtos' => $this->table->getProdutosJoinCategorias(),
        ]);
    }

    public function addAction()
    {
        $form = new ProdutoForm();
        $form->get('submit')->setValue('Criar');

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();

        $categorias = $this->categoriaTable->fetchAll();
        $arCategorias = [];

        foreach ($categorias as $c) {
            $arCategorias[$c->id_categoria_planejamento] = $c->nome_categoria;
        }

        if (!$request->isPost()) {
            return ['form' => $form, 'categorias' => $arCategorias];
        }

        $produto = new Produto();
        $form->setInputFilter($produto->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form, 'categorias' => $arCategorias];
        }

        $produto->exchangeArray($form->getData());

        $this->categoriaTable->getCategoria($produto->id_categoria_produto);

        $this->table->saveProduto($produto);
        return $this->redirect()->toRoute('produto');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('produto', ['action' => 'add']);
        }

        try {
            $produto = $this->table->getProduto($id);
        } catch (\Throwable $e) {
            return $this->redirect()->toRoute('produto', ['action' => 'index']);
        }

        $form = new ProdutoForm();
        $form->bind($produto);
        $form->get('submit')->setAttribute('value', 'Salvar');

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();

        $categorias = $this->categoriaTable->fetchAll();
        $arCategorias = [];

        foreach ($categorias as $c) {
            $arCategorias[$c->id_categoria_planejamento] = $c->nome_categoria;
        }

        $viewData = ['id' => $id, 'form' => $form, 'categorias' => $arCategorias];

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($produto->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return $viewData;
        }

        $this->table->saveProduto($produto);

        return $this->redirect()->toRoute('produto', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('produto');
        }

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteProduto($id);
            }

            return $this->redirect()->toRoute('produto');
        }

        return [
            'id'    => $id,
            'produto' => $this->table->getProduto($id),
        ];
    }
}
