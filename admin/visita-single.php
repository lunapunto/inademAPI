<?php
require_once 'functions.php';
if(i('asesor')){
  $gp = '<a href="'.dir.'/admin/asesor.php?'.i('idparent').'">Asesor #'.i('idparent').'</a>';
}else{
  $gp = '<a href="'.dir.'/admin/visita.php?'.i('idparent').'">Negocio #'.i('idparent').'</a>';
}
$q = array(
            'path'=>'main',
            'screen'=>'loggedin',
            'classes' => 'hideSearch',
            'grandparent'=>$gp,
            'parent'     => false,
            'title'=>'Ver sesión'
           );

admin_filter($q, $_COOKIE);
get_header($q,'admin');
get_menu($q,'admin');
$id = i('id');
$visitas = new visitas;
$visita = $visitas->get_visita($id);
$hrs = $visita['time'];
?>

<div class="binder">
  <div class="the_content">

    <header>
      <div class="title">
        Visita
      </div>
      <div class="subtitle">
      #<?= $id;?>
      </div>
    </header>


    <section>
      <div class="visita-metas">

        <div class="vm-tr">
          <div class="vm-td vm-th">
            Fecha de visita
          </div>
          <div class="vm-td vm-tc">
            <?= pdate($visita['fecha_hora_inicio'], false);?>
          </div>
        </div>

      </div>

      <div class="bigmeta">

        <div class="data">
          <div class="cypher">
            <?= hrs($hrs);?>
          </div>
          <div class="title">
            Tiempo de visita
          </div>
        </div>

        <div class="data">
          <div class="cypher">
            <?= cdates($visita['fecha_hora_inicio'], $visita['fecha_hora_final']);?>
          </div>
          <div class="title">
            Inicio
          </div>
        </div>

        <div class="data">
          <div class="cypher">
            <?= cdates($visita['fecha_hora_final'], $visita['fecha_hora_inicio']);?>
          </div>
          <div class="title">
            Final
          </div>
        </div>

        <div class="table visita-table">

          <div class="th tr">
            <div class="td">
              Asesor
            </div>
            <div class="td td-right">
              Microempresario
            </div>
            <div class="td td-right">
              Giro
            </div>
            <div class="td td-2 td-right">
              Acciones
            </div>
          </div>

          <div class="tc tr">
            <div class="td">
              <?= get_asesor_name($visita['id_asesor']);?>
            </div>
            <div class="td td-right">
              <?= get_microempresario_name($visita['id_negocio']);?>
            </div>
            <div class="td td-right">
              <?= get_giro_name_negocio($visita['id_negocio']);?>
            </div>
            <div class="td td-2 td-right">
              <a href="<?= admin.'/asesor.php?id='.$visita['id_asesor'];?>">
                Perfil Asesor
              </a>
              &nbsp;&nbsp;
              <a href="<?= admin.'/visita.php?id='.$visita['id_negocio'];?>">
                Negocio
              </a>
            </div>
          </div>


        </div>




      </div>



      <div class="maps">
        <div class="maps-name">
          Ubicación de la visita
        </div>
        <div id="map">

        </div>
      </div>

      <script type="text/javascript">

      var map;
      function initMap() {
        var latLng = {lat: <?= $visita['latitud_inicio'];?>, lng: <?= $visita['longitud_inicio'];?>};
        map = new google.maps.Map(document.getElementById('map'), {
          center: latLng,
          scrollWheel: false,
          zoom: 16
        });

        var marker = new google.maps.Marker({
          position: latLng,
          map: map,
        });
      }

    </script>
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoBnelmi80gWbqLelL1j2G84Y5B84HKxE&callback=initMap">
    </script>




    </section>




  </div>
</div>






<?php get_footer($q, 'admin');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
