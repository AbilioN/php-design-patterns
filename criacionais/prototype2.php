<?php

// another exemple

abstract class TermoContratual
{
    private string $nomeContratado;
    private string $numeroContrato;
    private string $template;

    function __construct(string $nomeContratado, string $numeroContrato)
    {
        $this->nomeContratado = $nomeContratado;
        $this->numeroContrato = $numeroContrato;
        $this->template = file_get_contents($this->getURITemplate());
    }

    public function getNomeContratado(): string
    {
        return $this->nomeContratado;
    }

    public function setNomeContratado(string $nomeContratado) : void
    {
        $this->nomeContratado = $nomeContratado;

    }

    public function setNumeroContrato(string $numeroContrato)
    {
        return $this->numeroContrato = $numeroContrato;
    }

    function getConteudo() : string
    {
        $hash = ["#contratado" , "#numero_contrato" ,  "#data"];
        $replaces = [$this->nomeContratado, $this->numeroContrato , (new \DateTime())->format('d/m/y')];
        return str_replace($hash, $replaces, $this->template);
    }

    abstract protected function getURITemplate() : string;

    public abstract function  __clone();
 
}

class DocumentoTermoCondicao extends TermoContratual
{
    public function __clone()
    {
        
    }

    protected function getURITemplate(): string
    {
        return "https://pastebin.com/raw/JNc3NVDy";
    }
} 

class RepresentanteLegal
{
    private string $nome;
    private string $cargo;

    public function __construct(string $nome, string $cargo)
    {
        $this->nome = $nome;
        $this->cargo = $cargo;
    }

    public function getNome() : string
    {
        return $this->nome;
    }

    public function getCargo() : string
    {
        return $this->cargo;
    }

    public function setNome(string $nome) : void
    {
        $this->nome = $nome;
    }

    public function setCargo(string $cargo) : void
    {
        $this->cargo = $cargo;
    }


}

class DocumentoTermoConfidencialidade extends TermoContratual
{
    private RepresentanteLegal $representanteLegal;
    
    public function __construct(string $nomeContratado, string $numeroContrato, RepresentanteLegal $representanteLegal)
    {
        parent::__construct($nomeContratado, $numeroContrato);
        $this->representanteLegal = $representanteLegal;
    }

    public function getRepresentanteLegal() : RepresentanteLegal
    {
        return $this->representanteLegal;
    }

    public function setRepresentanteLegal(RepresentanteLegal $representanteLegal)
    {
        return $this->representanteLegal;
    }
    
    public function getConteudo(): string
    {
        
        return str_replace("#representante" , $this->representanteLegal->getNome(), parent::getConteudo());
    }

    public function __clone()
    {
        $this->representanteLegal = clone $this->representanteLegal;
    }

    public function getURITemplate() : string
    {
        return "https://pastebin.com/raw/fvxp0W6Z";
    }
}

$docTC = new DocumentoTermoCondicao("Lorem Ipsum" , "000000");

$docConf = new DocumentoTermoConfidencialidade("Lorem Ipsum", "111111" ,
    new RepresentanteLegal("Fulano da Silva" , "Cargo Exemplo")
);

$cloneTc = clone $docTC;
$cloneConf = clone $docConf;

$cloneTc->setNumeroContrato("3333333");
$cloneConf->setNumeroContrato("44444");

var_dump($docTC);

echo PHP_EOL;

var_dump($docConf);

echo PHP_EOL;

var_dump($cloneTc);

echo PHP_EOL;

var_dump($cloneConf);

echo PHP_EOL;
