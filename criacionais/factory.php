<?php

interface Produto
{

    public function getNome() : string;
    public function setValor(float $valor);
}

class SmartphoneImportado implements Produto
{
    private float $valor;

    public function getNome(): string
    {
        return "Smartphone importado xpto";
    }

    public function setValor(float $valor)
    {
        $this->valor = $valor;
    }
}


class PacoteViagem implements Produto
{
    private \DateTimeInterface $data;
    private float $valor;

    function getNome(): string
    {
        return "Pacote de Viagens";
    }

    public function setValor(float $valor)
    {
        $this->valor = $valor;
    }

}

abstract class ProdutoFactory
{
    public abstract function criarProduto(float $valor) : Produto;
}

class SmartphoneImportadoFactory extends ProdutoFactory
{
    private const URI_API = 'https://api.exchangeratesapi.io/latest?base=USD&symbols=BLR';

    public function criarProduto(float $valor): SmartphoneImportado
    {
        $cotacao_json =  json_decode(file_get_contents(self::URI_API));
        var_dump($cotacao_json);
        die;
        $cotacao = $cotacao_json->rates->BRL;

      
        $produto = new SmartphoneImportado();

        $produto->setValor($valor * $cotacao);
         
        return $produto;
    }


}

class PacoteViagemFactory extends ProdutoFactory
{
    const MESES_ALTA_TEMPORADA = [1, 7, 12];

    public function criarProduto(float $valor): PacoteViagem
    {
        $hoje = new \DateTime();
        $produto = new PacoteViagem($hoje);

        if(in_array($hoje->format('m'), self::MESES_ALTA_TEMPORADA))
        {
            $produto->setValor($valor * 2);

        }else
        {
            $produto->setValor($valor);
        }

        return $produto;
    }
}

$factory = new SmartphoneImportadoFactory();

$produto = $factory->criarProduto(100);
var_dump($produto);

echo PHP_EOL;

$factory2 = new PacoteViagemFactory();
$produto2 = $factory->criarProduto(100);

var_dump($produto2);