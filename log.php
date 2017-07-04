<?php
$changes = array(
  array('title'=>'Error en comando SQL', 'des'=>'Un error en la consulta de administradores sin visita ocasionaba saturación en la base de datos.', 'type'=>'Administrador', 'date'=>'Mayo 15, 2017 18:30hrs.'),
  array('title'=>'Error en sintaxis', 'des'=>'La palabra "refrigeradores" se encontraba escrita como "refrigreradores".', 'type'=>'API', 'date'=>'Mayo 15, 2017 13:32hrs.'),
  array('title'=>'Error en sintaxis', 'des'=>'El giro "carnicería" estaba escrito como "carnincería", no detectaba el ID de giro.', 'type'=>'API', 'date'=>'Mayo 15, 2017 13:30hrs.'),
);
?>
<!DOCTYPE html>
<html>
<title>Log de cambios INADEM</title>
<meta charset="utf-8"/>
<style>
</style>
<body>
  <div class="main">
    <?php foreach($changes as $change):?>
      <p>
        <span class="badge"><?= $change['type'];?></span>
        <h5><?= $change['title'];?></h5>
        <h6><?= $change['date'];?></h6>
        <pre>
          <?= $change['des'];?>
        </pre>
      </p>
      <hr/>

    <?php endforeach;?>
  </div>
</body>
</html>
