<?php

// Builder

// Builder é um pattern que separa a construção de um objeto complexo
// de sua representação para que o mesmo processo de construção possa criar representações diferentes

// os participantes deste patterns são:

// Builder, Builder Concreto, Diretor e Cliente;

// Neste exemplo faremos a transação de dois formatos de entrada (xml ou json)  para dois formatos de saída (html ou csv);

abstract class  Builder
{


    protected string $resultado = "";

    abstract function incluirCabecalho(array $header);

    abstract function incluirLinha(array $line);
    
    abstract function finalizar();

    public function getResultado() : string
    {
        return $this->resultado;
    }

}

abstract class Diretor 
{
    protected Builder $builder;
    

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }
}

class HtmlBuilder extends Builder
{

    private \DOMDocument $document;
    private \DOMElement $table;

    public function __construct()
    {
        $this->document =  new \DOMDocument('1.0' , 'utf-8');
        $this->document->appendChild($this->document->createElement('html'));
        $this->table =  $this->document->createElement('table');
        $this->table->setAttribute('border' , '1');
        $this->document->firstChild->appendChild($this->table);
    }

    public function criarTableRow(array $line, $tipo = 'td')
    {
        $tr = $this->document->createElement('tr');
        array_map(fn ($v) => $tr->appendChild($this->document->createElement($tipo, $v)), $line);
        $this->table->appendChild($tr);
    }

    public function finalizar()
    {
        $this->resultado = $this->document->saveHTML();
    }

    public function incluirCabecalho(array $header)
    {
        $this->criarTableRow($header, 'th');
    }

    public function incluirLinha(array $line)
    {
        $this->criarTableRow($line);
    }
}

class CsvBuilder extends Builder
{
    protected $csvArray = [];
    protected string $resultado = '';

    public function finalizar()
    {
        foreach($this->csvArray as $line)
        {
            $this->resultado .= implode(",", $line) . PHP_EOL;
        }
    }

    public function incluirCabecalho(array $header)
    {
        $this->csvArray[] = $header; 
    }

    public function incluirLinha(array $line)
    {
        $this->csvArray[] = $line;
    }
}

class DiretorXml extends Diretor
{

    public function construir(string $inputFileName)
    {
      
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->load($inputFileName);
        $root = $document->firstChild;
        $item1 = iterator_to_array($root->firstChild->childNodes);
        $this->builder->incluirCabecalho(array_column($item1, 'tagName'));

        
        foreach($root->childNodes as $child)
        {
            $item =  iterator_to_array($child->childNodes);
            $this->builder->incluirLinha(array_column($item, 'nodeValue'));
        }

        $this->builder->finalizar();
    }
}

class DiretorJson extends Diretor
{
    public function construir(string $inputFileName)
    {
        $jsonArray =  json_decode(file_get_contents($inputFileName));
        $this->builder->incluirCabecalho(array_keys( (array) $jsonArray[0]));

        foreach ($jsonArray as $jsonObject)
        {
            $this->builder->incluirLinha((array) $jsonObject);
        }

        $this->builder->finalizar();
    }
}

$input = 'cliente.json';

$builder = new CsvBuilder();

$diretor = new DiretorJson($builder);

$diretor->construir($input);

file_put_contents("clientes.csv", $builder->getResultado());