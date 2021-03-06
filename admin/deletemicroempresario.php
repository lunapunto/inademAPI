<?php
require_once 'functions.php';
$q = array(
            'path'=>'main',
            'screen'=>'loggedin',
            'grandparent'=>'<a href="'.dir.'/admin/microempresarios.php">Microempresarios</a>',
            'parent'     => false,
            'title'=>'Eliminar microempresario'
           );
admin_filter($q, $_COOKIE);
$current_user = get_current_admin($_COOKIE);
get_header($q,'admin');
get_menu($q,'admin');
$id = $_GET['id'];
$admin = $db->fetch('SELECT usuario FROM usuarios_microempresarios WHERE usuarioid='.$id);
?>

<div class="binder">
  <div class="the_content">

    <header>
      <div class="title">
        Microempresarios
      </div>
      <div class="subtitle">
        Borrar microempresario
      </div>
    </header>


    <section>

      <div class="prompt">
        Estás a punto de eliminar
        al microempresario con el usuario <strong><?= $admin['usuario'];?></strong>, todos los registros de este microempresario serán
        borrados esta acción es definitiva y no se puede deshacer.

        <br /><br />

        Si necesitas bloquear el ingreso de un microempresario desactívalo en la columna "Status" de la tabla de los microempresarios.

        <div class="buttons">
          <a href="<?= admin.'/microempresarios';?>">
            <div class="button button_alert">
              Cancelar
            </div>
          </a>
          <a href="<?= admin.'/ajax?action=deletemicroempresario&id='.$id;?>">
            <div class="button button_cancel">
              Continuar
            </div>
          </a>
        </div>

      </div>

    </section>


    <a href="<?= admin.'/microempresarios.php';?>">
    <div class="bigbutton">
      <i class="material-icons">view_list</i><span>Gestionar Microempresarios</span>
    </div>
    </a>

  </div>
</div>



<?php get_footer($q,'admin');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
