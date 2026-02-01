<!-- Incluimos la cabecera -->
<?php include_once("common/cabecera.php"); ?>

<!-- Vista para hacer login en la aplicación -->

<body>
	<!-- Incluimos un menú para la aplicación -->
	<?php include_once("common/menu.php"); ?>

	<!-- Parte específica de nuestra vista -->
	<!-- Formulario para insertar un nuevo item -->
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<form action="index.php" method="post">
					<input type="hidden" name="controlador" value="App">
					<input type="hidden" name="accion" value="login">

					<?php echo isset($errores["login"]) ? "*" : "" ?>
					<div class="form-group">
					<label for="login">Login</label>
					<input class="form-control" type="text" name="login">
					</div>
					</br>

					<label for="password">Password</label>
					<input class="form-control" type="password" name="password">
					</br>

					<input class="btn btn-primary btn-block" type="submit" name="submit" value="Entrar">
				</form>
			</div>
		</div>
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