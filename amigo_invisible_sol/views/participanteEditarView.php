<?php
// Incluimos la autentificación
include_once("common/autentificacion.php"); ?>

<!-- Incluimos la cabecera -->
<?php include_once("common/cabecera.php"); ?>

<!-- Vista para editar un elemento de la tabla -->

<body>
	<!-- Incluimos un menú para la aplicación -->
	<?php include_once("common/menu.php"); ?>

	<div class="container md-col-offset-2">
		<h2>Editar Participante</h2>
		<!-- Parte específica de nuestra vista -->
		<form action="index.php" method="post">
			<input type="hidden" name="controlador" value="Participante">
			<input type="hidden" name="accion" value="editar">

			<div class="form-group">
				<label for="codigo">Codigo</label>
				<input class="form-control" type="text" name="codigo" value="<?php echo $item->getCodigo(); ?>" readonly>
			</div>

			<div class="form-group">
				<?php echo isset($errores["nombre"]) ? "*" : "" ?>
				<label for="nombre">Nombre</label>
				<input class="form-control" type="text" name="nombre" value="<?php echo $item->getNombre(); ?>">
			</div>

			<div class="form-group">
				<?php echo isset($errores["regalo"]) ? "*" : "" ?>
				<label for="regalo">Regalo</label>
				<input class="form-control" type="text" name="regalo" value="<?php echo $item->getRegalo(); ?>">
			</div>

			<input class="btn btn-success btn-block" type="submit" name="submit" value="Aceptar">
			<button class="btn btn-danger btn-block" type="button" onclick="window.location.href='index.php?controlador=Participante&accion=listar'">Cancelar</button>
		</form>
	</div>
	</br>

	<?php
	// Si hay errores los mostramos.
	if (isset($errores)):
		foreach ($errores as $key => $error):
			echo $error . "</br>";
		endforeach;
	endif;
	?>

	<!-- Incluimos el pie de la página -->
	<?php include_once("common/pie.php"); ?>
</body>

</html>