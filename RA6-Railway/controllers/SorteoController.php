<?php
// Controlador para el modelo SorteoModel
// Un controlador no tiene porque estar asociado a un objeto del modelo
class SorteoController {
    // Atributo con el motor de plantillas del microframework
    protected $view;

    // Constructor. Únicamente instancia un objeto View y lo asigna al atributo
    function __construct() {
        //Creamos una instancia de nuestro mini motor de plantillas
        $this->view = new View();
    }

    // Método del controlador para listar los items almacenados
    public function listar() {
        //Incluye el modelo que corresponde
        require 'models/SorteoModel.php';


        //Creamos una instancia de nuestro "modelo"
        $items = new SorteoModel();

        //Le pedimos al modelo todos los items
        $listado = $items->getAll();

        //Pasamos a la vista toda la información que se desea representar
        $data['items'] = $listado;

        // Finalmente presentamos nuestra plantilla 
        // Llamamos al método "show" de la clase View, que es el motor de plantillas
        // Genera la vista de respuesta a partir de la plantilla y de los datos
        $this->view->show("sorteoListarView.php", $data);
    }

    // Método del controlador para crear un nuevo item
    public function nuevo() {
        require 'models/SorteoModel.php';
        $item = new SorteoModel();

        $errores = array();

        // Si recibe por GET o POST el objeto y lo guarda en la BD
        if (isset($_REQUEST['submit'])) {
            // Comprobamos si se ha recibido el nombre
            if (!isset($_REQUEST['nombre']) || empty($_REQUEST['nombre']))
                $errores['nombre'] = "* Nombre: debes indicar un nombre.";

            // Si no hay errores actualizamos en la BD
            if (empty($errores)) {
                $item->setNombre($_REQUEST['nombre']);
                $item->setDescripcion($_REQUEST['descripcion']);
                $item->setFinalizado(0);
                $item->save();

                // Finalmente llama al método listar para que devuelva vista con listado
                header("Location: index.php?controlador=Sorteo&accion=listar");
                exit;
            }
        }

        // Si no recibe el item para añadir, devuelve la vista para añadir un nuevo item
        $this->view->show("sorteoNuevoView.php", array('errores' => $errores));



    }

    // Método que procesa la petición para editar un item
    public function editar() {

        require 'models/sorteoModel.php';
        $items = new SorteoModel();

        // Recuperar el item con el código recibido
        $item = $items->getById($_REQUEST['codigo']);

        if ($item == null) {
            $this->view->show("errorView.php", array('error' => 'No existe codigo'));
        }

        $errores = array();

        // Si se ha pulsado el botón de actualizar
        if (isset($_REQUEST['submit'])) {

            // Comprobamos si se ha recibido el nombre
            if (!isset($_REQUEST['nombre']) || empty($_REQUEST['nombre']))
                $errores['nombre'] = "* Nombre: es obligatorio el nombre.";

            // Si no hay errores actualizamos en la BD
            if (empty($errores)) {
                // Cambia el valor del item y lo guarda en BD
                $item->setNombre($_REQUEST['nombre']);
                $item->setDescripcion($_REQUEST['descripcion']);
                $item->setFinalizado($_REQUEST['finalizado']);
                $item->save();

                // Reenvía a la aplicación a la lista de items
                header("Location: index.php?controlador=Sorteo&accion=listar");
                exit;
            }
        }

        // Si no se ha pulsado el botón de actualizar se carga la vista para editar el item
        $this->view->show("sorteoEditarView.php", array('item' => $item, 'errores' => $errores));



    }

    // Método para borrar un item 
    public function borrar() {
        // Comprobamos si el usuario está autenticado
        include_once('views/common/autentificacion.php');

        //Incluye el modelo que corresponde
        require_once 'models/SorteoModel.php';

        //Creamos una instancia de nuestro "modelo"
        $items = new SorteoModel();

        // Recupera el item con el código recibido por GET o por POST
        $item = $items->getById($_REQUEST['codigo']);

        if ($item == null) {
            $this->view->show("errorView.php", array('error' => 'No existe codigo'));
        } else {
            // Si el sorteo tiene participantes no se puede borrar
            if ($item->tieneParticipantes()) {
                $this->view->show("errorView.php", array('error' => 'No se puede borrar el sorteo porque tiene participantes.'));
                exit;
            } else {
                // Si existe lo elimina de la base de datos y vuelve al inicio de la aplicación
                $item->delete();
                header("Location: index.php?controlador=Sorteo&accion=listar");
                exit;
            }
        }
    }

    // Método para limpiar los amigos de los participantes en un sorteo determinado
    public function limpiar() {
        // Comprobamos si el usuario está autenticado
        include_once('views/common/autentificacion.php');

        //Incluye el modelo que corresponde
        require_once 'models/SorteoModel.php';

        //Creamos una instancia de nuestro "modelo"
        $items = new SorteoModel();

        // Recupera el item con el código recibido por GET o por POST
        $item = $items->getById($_REQUEST['cod_sorteo']);

        if ($item == null) {
            $this->view->show("errorView.php", array('error' => 'No existe codigo'));
        } else {
            // Limpiamos el sorteo usando el método del modelo
            $item->limpiarAmigos();
            header("Location: index.php?controlador=Participante&accion=listarByCodSorteo&cod_sorteo=" . $_REQUEST['cod_sorteo']);
            exit;
        }
    }
}
?>