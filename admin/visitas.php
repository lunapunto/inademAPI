<?php
require_once 'functions.php';
$q = array(
            'path'=>'main',
            'screen'=>'loggedin',
            'grandparent'=>false,
            'parent'     => false,
            'title'=>'Negocios'
           );
admin_filter($q, $_COOKIE);
$current_user = get_current_admin($_COOKIE);
$role = (int) $current_user['role'];
get_header($q,'admin');
get_menu($q,'admin');
?>

<div class="binder">
  <div class="the_content">
    <section>
      <div id="jsGrid">

      </div>
    </section>
  </div>
</div>

<script>
    var url = "<?= dir.'/admin/fetchVisitas.php';?>";
    $.ajax({
        type: "GET",
        url: url
    }).done(function(clients) {

    $("#jsGrid").jsGrid({
        width: "100%",
        height: "auto",
        pagerFormat: "Páginas: {first} {prev} {pages} {next} {last}    {pageIndex} de {pageCount}",
        pagePrevText: "Anterior",
        pageNextText: "Siguiente",
        pageFirstText: "Primera",
        pageLastText: "Última",
        pageNavigatorNextText: "...",
        pageNavigatorPrevText: "...",
        noDataContent: "Sin Resultados",
        deleteConfirm: "Esta acción es irreversible...",
        autoload: true,
        filtering: true,
        editing: <?= $role !== 3 ? 'false': 'true';?>,
        sorting: true,
        paging: true,

        loadIndication: true,
        loadIndicationDelay: 500,
        loadMessage: "Cargando datos...",
        loadShading: false,
        rowClick: function(args) { return;},
        controller: {
                loadData: function(filter) {
                  return  $.ajax({
                        type: "GET",
                        url: url,
                        data: filter,
                    }).always(function(msg){
                      console.log(msg);
                    });

                },
                insertItem: function(item) {
                  item.type = 'insert';
                    return $.ajax({
                        type: "POST",
                        url: url,
                        data: item
                    });
                },
                updateItem: function(item) {
                  item.type = 'update';
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: item
                    }).done(function(msg){
                      console.log(msg);
                      console.log(this.url);
                    });
                },
                deleteItem: function(item) {
                  item.type = 'delete';
                    return $.ajax({
                        type: "POST",
                        url: url,
                        data: item
                    }).done(function(msg){
                      console.log(msg);
                      console.log(this.url);
                    });;
                }
            },
        fields: [
            { name: "id",width:80, title: "ID Negocio",type: "number", validate: "required", filtering: true, editing: false},
            { name: "rfc", width: 140, align: "center", title: "RFC", type: "text", validate: "required", filtering: true},
            { name: "folio", title: "Folio", type: "text", validate: "required", filtering: true},
            {
                name: "convocatoria",
                title: 'Convocatoria',
                type: "select",
                filtering: false,
                width: 140,
                items: [
                  {Name: "4.1 Desarrollo de Capacidades Empresariales para Microempresas.", ID: "4.1 Desarrollo de Capacidades Empresariales para Microempresas."},
                  {Name: "4.1 a)Formación Empresarial para MIPYMES.", ID: "4.1 a)Formación Empresarial para MIPYMES."},
                  {Name: "4.1 b)Formación Empresarial para MIPYMES.", ID: "4.1 b)Formación Empresarial para MIPYMES."},
                  {Name: "5.1 Incorporación de Tecnologías de Información y Comunicaciones a las Micro y Pequeñas Empresas.'", ID: "5.1 Incorporación de Tecnologías de Información y Comunicaciones a las Micro y Pequeñas Empresas.'"},
                  {Name: "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s).", ID: "5.2  Desarrollo de Capacidades Empresariales para Microempresas a través de la incorporación de Tecnologías de la Información y Comunicaciones (TIC’s)."},
                  {Name: "Proyecto: MI TIENDA.", ID: "Proyecto: MI TIENDA."},
                  {Name: "Proyecto: MI TIENDA 2016.", ID: "Proyecto: MI TIENDA 2016."},

                  {Name: "Sin Convocatoria", ID: " "},
                ],
                valueField: "ID", textField: "Name"
            },
            { name: "negocio", title: "Negocio", type: "text", validate: "required", filtering: true},
            { name: "microempresario", width: 140,title: "Microempresario", type: "text", validate: "required", filtering: true},
            { name: "email", title: "Correo", width: 140, type: "text", validate: "required", filtering: true, editing: true},
            { name: "password", title: "Contraseña", width: 140, type: "text", validate: "required", filtering: true},
            { name: "giro", title: "Giro", type: "text", validate: "required", filtering: true, editing: false},
            { name: "consultor", title: "Consultor", type: "text", validate: "required", filtering: true, editing: false},
            //{ name: "entregables",title: "Entregables", type: "text", validate: "required", filtering: false, editing: false},
            //{ name: "horas",title: "Horas en total", type: "text", validate: "required", filtering: false, editing: false},
            { name: "status", title: "Status", type: "checkbox", filtering: false},
            {
                type: "control",
                width: 180,
                editButton: <?= $role !== 3 ? 'false': 'true';?>,
                deleteButton: true,
                itemTemplate: function(value, item) {
                              var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                              var $myButton = $('<a target="_blank" href="visita.php?id='+item.id+'"><div class="viewmore">Ver más</div></a>');
                              return $result.add($myButton);
                }
            }

        ]
    });

  });


</script>


<?php get_footer($q,'admin');?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
