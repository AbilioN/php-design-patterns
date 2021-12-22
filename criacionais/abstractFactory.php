<?php


// practical exemple


// famílias de objetos do tipo financiamento

abstract class Financiamento 
{
    protected float $taxaDeJuros;
    protected float $valor;


    public function __construct(float $taxaDeJuros, float $valor)
    {
        $this->taxaDeJuros = $taxaDeJuros;
        $this->valor = $valor;
    }

    public abstract function getValorMensal(int $quantidadeParcelas): float;

}

class FinanciamentoHabitacional extends Financiamento
{

    public function getValorMensal(int $quantidadeParcelas): float
    {
        $taxa = $this->taxaDeJuros / 100;
        $valParcela =  $this->valor * pow((1 + $taxa) , $quantidadeParcelas);
        $resultado =  $valParcela / $quantidadeParcelas;

        return $resultado; 
    }
}

class FinanciamentoVeicular extends Financiamento
{
    public function getValorMensal(int $quantidadeParcelas): float
    {
        $taxa = $this->taxaDeJuros / 100;
        
        $m = $this->valor * (1 + $taxa * $quantidadeParcelas);
        $resultado = $m / $quantidadeParcelas;
        return $resultado; 
    }
}

// familia de objetos do tipo Seguro


abstract class Seguro {

    protected float $valorAvaliado;

    protected array $cobertura;

    function __construct( float $valorAvaliado, array $cobertura)
    {
        $this->valorAvaliado = $valorAvaliado;
        $this->cobertura = $cobertura;
    }

    abstract function getValorMensal() : float;
}

class SeguroResidencial extends Seguro
{


    public function getValorMensal(): float
    {
        $adicionais = count($this->cobertura);
        return (($this->valorAvaliado * 0.1) + $adicionais) / 12;
    }
}

class SeguroVeicular extends Seguro
{

    public function getValorMensal(): float
    {
        $adicionais = count($this->cobertura) * 80;
        return (($this->valorAvaliado * 0.01) + $adicionais) / 12;
    }
}


interface AbstractBancoFactory
{
    public function getFinanciamento(float $valor) : Financiamento;

    public function getSeguro(float $valorAvaliado, array $cobertura) : Seguro;



} 

class BancoCaseiro implements AbstractBancoFactory
{

    private const TAXA_JUROS = 0.5;
    public function getFinanciamento(float $valor): Financiamento
    {
        return new FinanciamentoHabitacional(self::TAXA_JUROS, $valor);
    }

    public function getSeguro(float $valorAvaliado, array $cobertura): Seguro
    {
        return new SeguroResidencial($valorAvaliado, $cobertura);
    }
}


class BancoMotorizado implements AbstractBancoFactory
{

    private const TAXA_JUROS = 0.7;
    public function getFinanciamento(float $valor): Financiamento
    {
        return new FinanciamentoVeicular(self::TAXA_JUROS, $valor);
    }

    public function getSeguro(float $valorAvaliado, array $cobertura): Seguro
    {
        return new SeguroVeicular($valorAvaliado, $cobertura);
    }
}


class Cliente
{
    private AbstractBancoFactory $factory;

    public function __construct(string $tipo)
    {
        switch ($tipo)
        {
            case 'casa':
                $this->factory = new BancoCaseiro();
                break;
            case 'veiculo':
                $this->factory = new BancoMotorizado();
                break;
            default:
                new InvalidArgumentException('Opção inválida');
        }
    }

    public function getFactory() : AbstractBancoFactory
    {
        return $this->factory;
    }
}


$cliente  =  new Cliente("casa");

echo $cliente->getFactory()
            ->getFinanciamento(1000)
            ->getValorMensal(10);
echo PHP_EOL;


echo $cliente->getFactory()
            ->getSeguro(20000, ['terremoto' , 'enchente'])
            ->getValorMensal();


echo PHP_EOL;

$cliente2 = new Cliente('veiculo');

echo $cliente2->getFactory()
            ->getFinanciamento(5000)
            ->getValorMensal(24);