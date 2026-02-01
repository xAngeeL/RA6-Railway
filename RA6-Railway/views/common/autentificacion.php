<?php
// Iniciamos la sesión
session_start();

// Si el usuario no se ha autenticado le indicamos que 
if (!isset($_SESSION['usuario_app'])) {
   header("Location:index.php?controlador=App&accion=login");
   exit;
}

?>
