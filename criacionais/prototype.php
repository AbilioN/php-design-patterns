<?php

class Aluno 
{
    public string $nome;
    public string $matricula;
    

    public function __construct(string $nome, string $matricula)
    {
        $this->nome = $nome;
        $this->matricula = $matricula;

    }

}

abstract class Turma 
{
    public string $codigo;
    public $alunos = [];

    public function __construct(string $codigo)
    {
        $this->codigo = $codigo;
    }
}

class ShallowTurma extends Turma{

}

class DeepTurma extends Turma
{
    public function __clone()
    {
        $this->alunos = array_map(fn ($o) => clone  $o , $this->alunos);
    }
}

$t0 = new ShallowTurma('Patterns2020');
$t1 = new DeepTurma('php2020');

$t0->alunos = [new Aluno("tiago" , "111") , new Aluno("maria" , "222")];
$t1->alunos = [new Aluno("Jose" , "333") , new Aluno("Pedro" , "444")];

$t2 = clone $t0;
$t3 = clone $t1;

$t2->codigo = "js2020";
$t2->alunos[0]->matricula = '777';

$t3->codigo = "laravel2021";
$t3->alunos[0]->matricula = '888';

var_dump($t0);
echo PHP_EOL;
var_dump($t1);
echo PHP_EOL;

var_dump($t2);
echo PHP_EOL;

var_dump($t3);
