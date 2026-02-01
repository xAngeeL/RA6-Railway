<?php
// Incluimos el fichero de autentificación
include_once("common/autentificacion.php"); ?>

<!-- Incluimos la cabecera -->
<?php include_once("common/cabecera.php"); ?>

<!-- Vista para editar un elemento de la tabla -->

<body>
	<!-- Incluimos un menú para la aplicación -->
	<?php include_once("common/menu.php"); ?>

	<div class="container md-col-offset-2">

		<!-- Parte específica de nuestra vista -->
		<!-- Formulario para insertar un nuevo item -->
		<form action="index.php" method="post">
			<input type="hidden" name="controlador" value="Sorteo">
			<input type="hidden" name="accion" value="nuevo">

			<?php echo isset($errores["nombre"]) ? "*" : "" ?>
			<div class="form-group">
				<label for="nombre">Nombre</label>
				<input class="form-control" type="text" name="nombre">
			</div>

			<div class="form-group">
				<label for="nombre">Descripción</label>
				<textarea class="form-control" name="descripcion" cols="40"
					rows="4"></textarea>
			</div>

			<input class="btn btn-success btn-block" type="submit" name="submit" value="Aceptar">
			<button class="btn btn-danger btn-block" type="button" onclick="window.location.href='index.php?controlador=Sorteo&accion=listar'">Cancelar</button>
		</form>
	</div>
	</br>

	<?php
	// Si hay errores se muestran
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