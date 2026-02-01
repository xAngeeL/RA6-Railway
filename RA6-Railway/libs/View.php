<?php
// Clase de la que heredan todas las vistas de la aplicación web
/*
 El uso es bastante sencillo:
 $vista = new View();
 $vista->show('listado.php', array("nombre" => "Juan"));
*/

class View {
    function __construct() {
    }

    // Método show que recibe el nombre de la vista a mostrar y un array de variables
    // que recibirá la vista para poder mostrar datos o recibir información
    public function show($name, $vars = array()) {
        // $name es el nombre de nuestra plantilla, por ej, listado.php
        // $vars es el contenedor de nuestras variables, es un arreglo del tipo llave => valor, opcional.

        // Traemos una instancia de nuestra clase de configuracion.
        $config = Config::singleton();

        // Armamos la ruta a la plantilla
        $path = $config->get('viewsFolder') . $name;

        //Si no existe el fichero en cuestion, mostramos un 404
        if (file_exists($path) == false) {
            trigger_error('La plantilla `' . $path . '` no existe.', E_USER_NOTICE);
            return false;
        }

        // Si hay variables para asignar, las pasamos una a una
        // Con esto conseguimos que la plantilla de la vista pueda acceder a las variables recibidas
        if (is_array($vars)) {
            foreach ($vars as $key => $value) {
                $$key = $value;
            }
        }

        //Finalmente, incluimos la plantilla, con lo que todo su código se pone aquí
        include($path);
    }
}
?>