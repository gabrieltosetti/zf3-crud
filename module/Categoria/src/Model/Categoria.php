<?php

namespace Categoria\Model;

// Add the following import statements:
use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Categoria implements InputFilterAwareInterface
{
    public $id_categoria_planejamento;
    public $nome_categoria;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id_categoria_planejamento = !empty($data['id_categoria_planejamento'])
            ? $data['id_categoria_planejamento']
            : null;
        $this->nome_categoria = !empty($data['nome_categoria']) ? $data['nome_categoria'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id_categoria_planejamento' => $this->id_categoria_planejamento,
            'nome_categoria'            => $this->nome_categoria,
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
            'name' => 'id_categoria_planejamento',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'nome_categoria',
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

        return $this->inputFilter = $inputFilter;
    }
}
