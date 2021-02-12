<?php

namespace Produto\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Produto\Form\ProdutoForm;
use Produto\Model\Produto;
use Produto\Model\ProdutoTable;

class ProdutoController extends AbstractActionController
{
    private $table;

    public function __construct(ProdutoTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        return new ViewModel([
            'produtos' => $this->table->fetchAll(),
        ]);
    }

    public function addAction()
    {
        $form = new ProdutoForm();
        $form->get('submit')->setValue('Criar');

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $produto = new Produto();
        $form->setInputFilter($produto->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            var_dump($form->getMessages());
            return ['form' => $form];
        }

        $produto->exchangeArray($form->getData());
        $this->table->saveProduto($produto);
        return $this->redirect()->toRoute('produto');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('produto', ['action' => 'add']);
        }

        try {
            $produto = $this->table->getProduto($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('produto', ['action' => 'index']);
        }

        $form = new ProdutoForm();
        $form->bind($produto);
        $form->get('submit')->setAttribute('value', 'Salvar');

        /** @var \Zend\Http\Request */
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

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
