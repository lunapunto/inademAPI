<?php
require_once 'functions.php';
$current_user = get_current_admin($_GET);
$title = i('title', $_GET);
$parent = i('parent', $_GET);
$grandparent = i('grandparent', $_GET);
$current_user = get_current_admin($_COOKIE);
$role = (int) $current_user['role'];
?>

<div id="topbar">
  <div id="tb-logo">
    <img src="<?= asset.'/admin/mono.svg';?>">
  </div>
  <div id="tb-flex">
    <div class="tbtd">

      <a href="<?= dir;?>/admin"><span class="bread">Inicio</span></a>
      <?php if($grandparent):?>
        <span class="divider"> > </span> <?= $grandparent;?>
      <?php endif;?>
      <?php if($parent):?>
        <span class="divider"> > </span> <?= $parent;?>
      <?php endif;?>
      <?php if($title):?>
        <span class="divider"> > </span> <?= $title;?>
      <?php endif;?>
    </div>
  </div>
  <div class="tbtd" id="iconsHead">
    <i class="material-icons searchTrigger closeih">close</i>
    <i class="material-icons searchTrigger searchih">search</i>
  </div>
</div>

<div id="menu">


  <div id="realmenu">
    <?php
      $menu = array(
                'General' => array(
                               array('Inicio', 'index.php')
                             ),
                'Negocios' => array(
                               array('Gestionar', 'visitas.php')
                              ),
                'Asesores' => array(
                                array('Gestionar', 'asesoresall.php')
                              ),

                 'Admins.' => array(
                                 array('Gestionar', 'admins.php')

                               )
              );
        if($role == 3){
          $menu['Notificaciones'][] = array('Enviar', 'sendnotification.php');

        }
        $current_endpoint = $_GET['current_server'];
        foreach($menu as $opttitle=>$group):
        ?>
        <div class="menu-group">
          <div class="menu-group-title">
            <?= $opttitle;?>
          </div>
          <div class="real-group">
            <?php
            foreach($group as $element):
              $urlx = strpos($element[1], 'http://');
              if($urlx === false){
                $url = admin.'/'.$element[1];
              }else{
                $url = $element[1];
              }
            ?>
            <a href="<?= admin.'/'.$element[1];?>">
              <div class="real-td <?= $element[1] == $current_endpoint ? 'current_td' : '';?>">
                  <?= $element[0];?>
              </div>
            </a>
            <?php endforeach;?>
          </div>
        </div>
        <?php endforeach;?>
  </div>


  <div id="logout-bottom">
    <div class="tbtd tbtd-usuario">
      <?= $current_user['usuario'];?>
    </div>
    <a href="<?= admin.'/ajax.php?action=admin_logout';?>">
    <div class="tbtd tbtd-logout">
      Cerrar sesión
    </div>
    </a>
  </div>

</div>
