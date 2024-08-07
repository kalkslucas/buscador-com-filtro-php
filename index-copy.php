<?php 
require_once 'conn.php'; 
//?? significa verificar se a variável é NULL
$nome = filter_input(INPUT_POST, 'nome') ?? '';
$departamento = filter_input(INPUT_POST, 'departamento') ?? '';
$sexo = filter_input(INPUT_POST, 'sexo') ?? '';
$dataAdmissaoInicio = filter_input(INPUT_POST, 'dataAdmissaoInicio') ?? '';
$dataAdmissaoFim = filter_input(INPUT_POST, 'dataAdmissaoFim') ?? '';
$orderBy = filter_input(INPUT_POST, 'orderBy') ?? '';
$ascOrDesc = filter_input(INPUT_POST, 'ascOrDesc') ?? '';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Busca por filtros</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
  <div class="container-fluid">
    <div class="row m-2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <form action="index-copy.php" method="post" id="formulario" name="formulario">
              <div class="row mb-3">
                <div class="col-md-12">
                  <h4>Buscar por nome</h4>
                  <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>">
                </div>
              </div>
              
              <div class="row mb-3">
                <h4>Buscar por filtros</h4>
                <div class="col-md-3">
                  <label>Departamento</label>
                  <select name="departamento" id="departamento" class="form-select">
                    <option value="">Todos</option>
                    <?php 
                      $sql = "SELECT DEPARTAMENTO FROM funcionarios GROUP BY DEPARTAMENTO ORDER BY DEPARTAMENTO ASC";
                      $consulta = $pdo->prepare($sql);
                      $consulta->execute();
                      while($row = $consulta->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <option value="<?=$row['DEPARTAMENTO']?>"><?=$row['DEPARTAMENTO']?></option>
                    <?php } ?> 
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Sexo</label>
                  <select name="sexo" id="sexo" class="form-select">
                    <option value="">Todos</option>
                    <?php 
                      $sql = "SELECT SEXO FROM funcionarios GROUP BY SEXO ORDER BY SEXO ASC";
                      $consulta = $pdo->prepare($sql);
                      $consulta->execute();
                      while($row = $consulta->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <option value="<?=$row['SEXO']?>"><?=$row['SEXO']?></option>
                    <?php } ?> 
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Data de Admissão (Desde:)</label>
                  <input type="date" class="form-control" name="dataAdmissaoInicio" id="dataAdmissaoInicio" value="<?= htmlspecialchars($dataAdmissaoInicio) ?>">
                </div>
                <div class="col-md-3">
                  <label>Data de Admissão (Até:)</label>
                  <input type="date" class="form-control" name="dataAdmissaoFim" id="dataAdmissaoFim" value="<?= htmlspecialchars($dataAdmissaoFim) ?>">
                </div>
              </div>

              <div class="row mb-3">
                <h4>Ordenar por:</h4>
                <div class="col-md-6">
                  <select name="orderBy" id="orderBy" class="form-select">
                    <option value="1">Nome</option>
                    <option value="2">Departamento</option>
                    <option value="3">Sexo</option>
                    <option value="4">Admissão</option>
                  </select>
                </div>
                <div class="col-md-5">
                  <select name="ascOrDesc" id="ascOrDesc" class="form-select">
                    <option value="1">Crescente</option>
                    <option value="2">Decrescente</option>
                  </select>
                </div>
                <div class="col-md-1">
                  <input type="submit" value="Buscar" class="w-100 btn btn-success">
                </div>    
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-striped table-success">
                <thead>
                  <tr style="background-color: green; color: white;">
                    <td>ID</td>
                    <td>Nome</td>
                    <td>E-mail</td>
                    <td>Sexo</td>
                    <td>Departamento</td>
                    <td>Admissão</td>
                    <td>Salário</td>
                    <td>Cargo</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    //Declaração do array para armazenar os valores dos parâmetros passados para a query
                    $queryParams = [];
                    //Query SQL
                    $sql = 'SELECT ID, NOME, EMAIL, SEXO, DEPARTAMENTO, ADMISSAO, SALARIO, CARGO FROM funcionarios WHERE 1=1';

                    //Verifica se a variável $nome não está vazia
                    if(!empty($nome)){
                      $nomeParts = explode(' ', $nome); //Divide o nome em um array de strings caso seja um nome que contenha espaços. Ex: Lucas Kalks se transforma em 'Lucas', 'Kalks'.
                      $nomeConditions = array_map(function($part, $index){//O array_map aplica uma função anônima em cada item do array anterior. Baseado no exemplo, seria 'NOME LIKE Lucas' e 'NOME LIKE Kalks'
                        return "NOME LIKE :nome$index";
                      }, $nomeParts, array_keys($nomeParts));//Itens e Índices passados como parâmetro para obedecer as condições da função
                      $sql .= ' AND (' . implode(' OR ', $nomeConditions) . ')';//Implementa a condição de buscar um ou mais nomes. Ex: NOME LIKE Lucas OR NOME LIKE Kalks.
                      foreach ($nomeParts as $index => $part) { // Pega o índice e o valor do índice e adiciona ao array como parâmetro para a query
                        $queryParams["nome$index"] = "%$part%";
                      }
                    }

                    if(!empty($departamento)){
                      $sql .= ' AND DEPARTAMENTO = :departamento';
                      $queryParams['departamento'] = $departamento;
                      // Pega o índice e o valor do índice e adiciona ao array como parâmetro para a query
                    }
                    
                    if(!empty($sexo)){
                      $sql .= ' AND SEXO = :sexo';
                      $queryParams['sexo'] = $sexo;
                      // Pega o índice e o valor do índice e adiciona ao array como parâmetro para a query
                    }
                    
                    if(!empty($dataAdmissaoInicio) && !empty($dataAdmissaoFim)){
                      $sql .= ' AND ADMISSAO BETWEEN :dataAdmissaoInicio AND :dataAdmissaoFim';
                      $queryParams['dataAdmissaoInicio'] = $dataAdmissaoInicio;
                      $queryParams['dataAdmissaoFim'] = $dataAdmissaoFim;
                      // Pega o índice e o valor do índice e adiciona ao array como parâmetro para a query
                    }

                    //Criado um array com o nome das colunas para ordenação
                    $orderColumns = ['NOME', 'DEPARTAMENTO', 'SEXO', 'ADMISSAO'];
                    if(!empty($orderBy) && isset($orderColumns[$orderBy - 1])){//Verifica se a variavel orderBy não está vazia e se foi setado o valor da variável orderBy menos um. Esse cálculo corresponde a posição da coluna dentro do array orderColumns.
                      $sql .= ' ORDER BY ' . $orderColumns[$orderBy - 1]; //Define a coluna para ordenar
                      $sql .= ($ascOrDesc == '2') ? ' DESC' : ' ASC'; //Se a variável ascOrDesc for 2, ordena de forma decrescente. Se não, ordena de forma crescente.
                    }

                    $consulta = $pdo->prepare($sql);
                    //Varre o array queryParams e faz o bind de todos os valores inseridos no array para dentro da query, garantindo a proteção do SQL Injection
                    foreach ($queryParams as $key => $value) {
                      $consulta->bindValue(":$key", $value);
                    }
                    $consulta->execute();
                    
                    while($row = $consulta->fetch(PDO::FETCH_ASSOC)){
                  ?>
                  <tr>
                    <td><?=$row['ID']?></td>
                    <td><?=$row['NOME']?></td>
                    <td><?=$row['EMAIL']?></td>
                    <td><?=$row['SEXO']?></td>
                    <td><?=$row['DEPARTAMENTO']?></td>
                    <td><?=$row['ADMISSAO']?></td>
                    <td>R$ <?=$row['SALARIO']?></td>
                    <td><?=$row['CARGO']?></td>
                  </tr>
                  <?php } ?>
                </tbody>    
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
