<?php 
require_once 'conn.php'; 
$nome = filter_var($_POST['nome'] = "");
$departamento = filter_var($_POST['departamento'] = "");
$sexo = filter_var($_POST['sexo'] = "");
$dataAdmissaoInicio = filter_var($_POST['dataAdmissaoInicio'] = "");
$dataAdmissaoFim = filter_var($_POST['dataAdmissaoFim'] = "");
$orderBy = filter_var($_POST['orderBy'] = "");
$ascOrDesc = filter_var($_POST['ascOrDesc'] = "");


if(!isset($nome)){$nome = "";}
if(!isset($departamento)){$departamento = "";}
if(!isset($sexo)){$sexo = "";}
if(!isset($dataAdmissaoInicio)){$dataAdmissaoInicio = "";}
if(!isset($dataAdmissaoFim)){$dataAdmissaoFim = "";}
if(!isset($orderBy)){$orderBy = "";}
if(!isset($ascOrDesc)){$ascOrDesc = "";}

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
            <form action="index.php" method="post" id="formulario" name="formulario">
              <div class="row mb-3">
                <div class="col-md-12">
                  <h4>Buscar por nome</h4>
                  <input type="text" name="nome" class="form-control" value="<?= $nome ?>">
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
                      if($row = $consulta->rowCount() > 0){
                        while($row = $consulta->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <option value="<?=$row['DEPARTAMENTO']?>"><?=$row['DEPARTAMENTO']?></option>
                    <?php } } ?> 
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
                      if($row = $consulta->rowCount() > 0){
                        while($row = $consulta->fetch(PDO::FETCH_ASSOC)){
                    ?>
                    <option value="<?=$row['SEXO']?>"><?=$row['SEXO']?></option>
                    <?php } } ?> 
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Data de Admissão (Desde:)</label>
                  <input type="date" class="form-control" name="dataAdmissaoInicio" id="dataAdmissaoInicio" value="<?= $dataAdmissaoInicio ?>">
                </div>
                <div class="col-md-3">
                  <label>Data de Admissão (Até:)</label>
                  <input type="date" class="form-control" name="dataAdmissaoFim" id="dataAdmissaoFim" value="<?= $dataAdmissaoFim ?>">
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
                    if($nome == ''){$nome = ' ';}
                    $explodeNome = explode(' ', $nome);

                    if($nome == '' AND $departamento == '' AND $sexo == '' AND $dataAdmissaoInicio = '' AND $dataAdmissaoFim == ''){//Se não tiver nenhum filtro, exibe todos os dados da tabela
                      $sql = 'SELECT ID, NOME, EMAIL, SEXO, DEPARTAMENTO, ADMISSAO, SALARIO, CARGO FROM funcionarios';
                    }else{
                      $sql = 'SELECT ID, NOME, EMAIL, SEXO, DEPARTAMENTO, ADMISSAO, SALARIO, CARGO FROM funcionarios';

                      //Caso tenha algum filtro no campo 'Nome', inclua a condição de buscar de acordo com o nome inserido
                      if($nome != ''){
                        $sql .= " WHERE NOME LIKE LOWER(%$explodeNome[0]%)";
                      }

                      for($i = 1; $i < count($explodeNome); $i++){
                        if(!empty($explodeNome[$i])){
                          $sql .= " OR NOME LIKE %$explodeNome[$i]%";
                        }
                      }

                      //Caso tenha algum filtro no campo 'Departamento', inclua a condição de buscar de acordo com o departamento selecionado
                      if($departamento != ''){
                        $sql .= " AND DEPARTAMENTO = :departamento";
                      }
                      //Caso tenha algum filtro no campo 'Sexo', inclua a condição de buscar de acordo com o sexo selecionado
                      if($sexo != ''){
                        $sql .= " AND SEXO = :sexo";
                      }
                      //Caso tenha algum filtro no campo 'Data de Admissao', inclua a condição de buscar de acordo com o período de datas especificado
                      if($dataAdmissaoInicio != ''){
                        $sql .= " AND ADMISSAO BETWEEN :dataAdmissaoInicio AND :dataAdmissaoFim";
                      }
                      //Definindo a ordem da query
                      switch ($orderBy){
                        case "1":
                          $sql .= " ORDER BY NOME ";
                          break;
                        case "2":
                          $sql .= " ORDER BY DEPARTAMENTO ";
                          break;
                        case "3":
                          $sql .= " ORDER BY SEXO ";
                          break;
                        case "4":
                          $sql .= " ORDER BY ADMISSAO ";
                          break;
                      }

                      switch ($ascOrDesc){
                        case "1":
                          $sql .= "ASC";
                          break;
                        case "2":
                          $sql .= "DESC";
                          break;
                      }
                    }
                    $consulta = $pdo->prepare($sql);
                    $consulta->bindParam(":departamento", $departamento, PDO::PARAM_STR);
                    $consulta->bindParam(":sexo", $sexo, PDO::PARAM_STR);
                    $consulta->bindParam(":dataAdmissaoInicio", $dataAdmissaoInicio, PDO::PARAM_STR);
                    $consulta->bindParam(":dataAdmissaoFim", $dataAdmissaoFim, PDO::PARAM_STR);
                    $consulta->execute();
                    if($row = $consulta->rowCount() > 0){
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
                  <?php } } ?>
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