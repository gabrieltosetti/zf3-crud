<?php

namespace Produto\Form;

use Categoria\Model\CategoriaTable;
use Zend\Form\Form;

class ProdutoForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('produto');

        $this->add([
            'name' => 'id_produto',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'id_categoria_produto',
            'type' => 'select',
            'options' => [
                'label' => 'Categoria',
                'disable_inarray_validator' => true
            ],
        ]);
        $this->add([
            'name' => 'nome_produto',
            'type' => 'text',
            'options' => [
                'label' => 'Nome',
            ],
        ]);
        $this->add([
            'name' => 'valor_produto',
            'type' => 'number',
            'options' => [
                'label' => 'Valor',
                'min' => '0',
                'max' => '100000000',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
