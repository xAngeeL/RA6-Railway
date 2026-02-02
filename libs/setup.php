<?php
// Script para configurar mi aplicación web
// Establece las variables que indican los directorios de las clases
// Establece las variables para hacer la conexión a la base de datos

// Obtiene la instancia del objeto que guarda los datos de configuración
$config = Config::singleton();

// Carpetas para los Controladores, los Modelos y las Vistas
$config->set('controllersFolder', 'controllers/');
$config->set('modelsFolder', 'models/');
$config->set('viewsFolder', 'views/');

// Parámetros de conexión a la BD
//$config->set('dbhost', 'db'); // Cambiar 'db' por 'localhost' si se usa XAMPP
//$config->set('dbname', 'amigo_invisible');
//$config->set('dbuser', 'root');
//$config->set('dbpass', 'root');

$config->set('dbhost', getenv('MYSQLHOST'));
$config->set('dbport', getenv('MYSQLPORT'));
$config->set('dbname', 'amigo_invisible'); // o getenv('MYSQLDATABASE')
$config->set('dbuser', getenv('MYSQLUSER'));
$config->set('dbpass', getenv('MYSQLPASSWORD'));


//mysql://root:lteuylEchIFhtTtGYBsIEPznIbkkHkSF@metro.proxy.rlwy.net:37830/railway

?>




