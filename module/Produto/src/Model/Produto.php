<?php

namespace Produto\Model;

// Add the following import statements:
use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToFloat;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;
use Zend\Validator\StringLength;
use Zend\Validator\Date as DateValidator;

class Produto implements InputFilterAwareInterface
{
    public $id_produto;
    public $id_categoria_produto;
    public $nome_produto;
    public $valor_produto;
    public $data_cadastro;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id_produto           = !empty($data['id_produto']) ? $data['id_produto'] : null;
        $this->id_categoria_produto = !empty($data['id_categoria_produto']) ? $data['id_categoria_produto'] : null;
        $this->nome_produto         = !empty($data['nome_produto']) ? $data['nome_produto'] : null;
        $this->valor_produto        = !empty($data['valor_produto']) ? $data['valor_produto'] : 0;
        $this->data_cadastro        = !empty($data['data_cadastro']) ? $data['data_cadastro'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id_produto'           => $this->id_produto,
            'id_categoria_produto' => $this->id_categoria_produto,
            'nome_produto'         => $this->nome_produto,
            'valor_produto'        => $this->valor_produto,
            'data_cadastro'        => $this->data_cadastro,
        ];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id_produto',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'id_categoria_produto',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => GreaterThan::class,
                    'options' => [
                        'min' => 0,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'nome_produto',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 150,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'valor_produto',
            'required' => true,
            'filters' => [
                ['name' => ToFloat::class],
            ],
            'validators' => [
                [
                    'name' => GreaterThan::class,
                    'options' => [
                        'min' => 0,
                        'inclusive' => true,
                    ],
                ],
                [
                    'name' => LessThan::class,
                    'options' => [
                        'max' => 100000000,
                        'inclusive' => true,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'data_cadastro',
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => DateValidator::class,
                    'options' => [
                        'format' => 'Y-m-d H:i:s',
                    ],
                ],
            ],
        ]);
        return $this->inputFilter = $inputFilter;
    }
}
