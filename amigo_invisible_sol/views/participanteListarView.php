<?php
// Incluimos la autentificación
include_once("common/autentificacion.php"); ?>

<?php include_once("common/cabecera.php"); ?>

<body>
    <?php include_once("common/menu.php"); ?>

    <div class="container md-col-offset-2">
        <h2>Listado de Participantes del sorteo <?php echo $cod_sorteo; ?></h2>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Regalo</th>
                    <th>Amigo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($items as $item) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $item->getCodigo() ?>
                        </td>
                        <td>
                            <?php echo $item->getNombre() ?>
                        </td>
                        <td>
                            <?php echo $item->getRegalo() ?>
                        </td>
                        <td>
                            <?php echo $item->getNombreAmigo() ?>
                        <td>
                            <a href="index.php?controlador=Participante&accion=editar&codigo=<?php echo $item->getCodigo() ?>" class="btn btn-primary btn-xs" <?php if ($sorteo_finalizado) echo "disabled" ?>>Editar</a>
                            <a href="index.php?controlador=Participante&accion=borrar&codigo=<?php echo $item->getCodigo() ?>" class="btn btn-danger btn-xs" <?php if ($sorteo_finalizado) echo "disabled" ?>>Borrar</a> 
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <a href="index.php?controlador=Participante&accion=nuevo&cod_sorteo=<?php echo $cod_sorteo; ?>" class="btn btn-success" <?php if ($sorteo_finalizado) echo "disabled" ?>>Nuevo participante</a>
        <a href="index.php?controlador=Participante&accion=sortear&cod_sorteo=<?php echo $cod_sorteo; ?>" class="btn btn-warning" <?php if ($sorteo_finalizado) echo "disabled" ?>>Realizar sorteo</a>
        <a href="index.php?controlador=Sorteo&accion=limpiar&cod_sorteo=<?php echo $cod_sorteo; ?>" class="btn btn-info" <?php if ($sorteo_finalizado) echo "disabled" ?>>Limpiar sorteo</a>
        <a href="index.php?controlador=Sorteo&accion=listar" class="btn btn-primary">Volver</a>
    </div>

    <!-- Incluimos el pie de página -->
    <?php include_once("common/pie.php"); ?>
</body>

</html>