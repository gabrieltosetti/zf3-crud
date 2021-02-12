<?php

namespace Categoria\Controller;

use Categoria\Form\CategoriaForm;
use Categoria\Model\Categoria;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Categoria\Model\CategoriaTable;

class CategoriaController extends AbstractActionController
{
    // Add this property:
    private $table;

    // Add this constructor:
    public function __construct(CategoriaTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        return new ViewModel([
            'categorias' => $this->table->fetchAll(),
        ]);
    }

    public function addAction()
    {
        $form = new CategoriaForm();
        $form->get('submit')->setValue('Criar');

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $categoria = new Categoria();
        $form->setInputFilter($categoria->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $categoria->exchangeArray($form->getData());
        $this->table->saveCategoria($categoria);
        return $this->redirect()->toRoute('categoria');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('categoria', ['action' => 'add']);
        }

        // Retrieve the categoria with the specified id. Doing so raises
        // an exception if the categoria is not found, which should result
        // in redirecting to the landing page.
        try {
            $categoria = $this->table->getCategoria($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('categoria', ['action' => 'index']);
        }

        $form = new CategoriaForm();
        $form->bind($categoria);
        $form->get('submit')->setAttribute('value', 'Salvar');

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($categoria->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return $viewData;
        }

        $this->table->saveCategoria($categoria);

        // Redirect to categoria list
        return $this->redirect()->toRoute('categoria', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('categoria');
        }

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return [
                'id'        => $id,
                'categoria' => $this->table->getCategoria($id),
            ];
        }

        $del = $request->getPost('del', 'No');

        if ($del == 'Yes') {
            $id = (int) $request->getPost('id');

            if ($qtdProdutos = count($this->table->getProdutosDaCategoria($id))) {
                return $this->redirect()->toRoute(
                    'categoria',
                    [],
                    ['query' => ['msgErro' => "Para deletar esta categoria, excluia primeiramente o(s) {$qtdProdutos} produto(s) cadastrado(s)"]]
                );
            }

            $this->table->deleteCategoria($id);
        }

        // Redirect to list of categorias
        return $this->redirect()->toRoute('categoria');
    }
}
