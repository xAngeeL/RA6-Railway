<?php
// Controlador para el modelo ParticipanteModel
// Un controlador no tiene porque estar asociado a un objeto del modelo
class ParticipanteController {
    // Atributo con el motor de plantillas del microframework
    protected $view;

    // Constructor. Únicamente instancia un objeto View y lo asigna al atributo
    function __construct() {
        //Creamos una instancia de nuestro mini motor de plantillas
        $this->view = new View();
    }

    // Método del controlador para listar los participantes de un determinado sorteo
    public function listarByCodSorteo() {
        //Incluye el modelo que corresponde
        require 'models/ParticipanteModel.php';
        require 'models/SorteoModel.php';

        // Recuperar el código de sorteo recibido
        if (!isset($_REQUEST['cod_sorteo'])) {
            $this->view->show("errorView.php", array('error' => 'Debes indicar un código de sorteo'));
            exit;
        }

        $cod_sorteo = $_REQUEST['cod_sorteo'];

        //Creamos una instancia de nuestro "modelo"
        $items = new ParticipanteModel();
        $sorteo = new SorteoModel();

        //Le pedimos al modelo los participantes de un determinado sorteo
        $listado = $items->getByCodSorteo($cod_sorteo);

        // Obtengo si el sorteo esta finalizado
        $sorteo_finalizado = $sorteo->getById($cod_sorteo)->getFinalizado();

        //Pasamos a la vista toda la información que se desea representar
        $data['items'] = $listado;
        $data['cod_sorteo'] = $cod_sorteo;
        $data['sorteo_finalizado'] = $sorteo_finalizado;

        // Finalmente presentamos nuestra plantilla 
        // Llamamos al método "show" de la clase View, que es el motor de plantillas
        // Genera la vista de respuesta a partir de la plantilla y de los datos
        $this->view->show("participanteListarView.php", $data);
    }



    /**
     * Realiza el sorteo asignando a cada participante un amigo aleatorio
     * Garantiza que nadie se asigne a sí mismo
     */
    function realizarSorteo(&$participantes) {
        $intentosMaximos = 1000;
        $intentos = 0;

        while ($intentos < $intentosMaximos) {
            // Resetear asignaciones
            foreach ($participantes as &$p) {
                $p['amigo'] = null;
            }
            unset($p);

            // Crear lista de códigos disponibles
            $disponibles = array_column($participantes, 'codigo');

            $exitoso = true;

            foreach ($participantes as &$participante) {
                // Filtrar códigos disponibles (excluir el propio)
                $opciones = array_filter($disponibles, function ($codigo) use ($participante) {
                    return $codigo !== $participante['codigo'];
                });

                // Si no hay opciones disponibles, reintentar
                if (empty($opciones)) {
                    $exitoso = false;
                    break;
                }

                // Seleccionar aleatoriamente
                $opciones = array_values($opciones); // Reindexar
                $indiceAleatorio = array_rand($opciones);
                $codigoAsignado = $opciones[$indiceAleatorio];

                // Asignar amigo
                $participante['amigo'] = $codigoAsignado;

                // Eliminar el código asignado de disponibles
                $disponibles = array_filter($disponibles, function ($codigo) use ($codigoAsignado) {
                    return $codigo !== $codigoAsignado;
                });
                $disponibles = array_values($disponibles); // Reindexar
            }
            unset($participante);

            if ($exitoso) {
                return true;
            }

            $intentos++;
        }

        return false;
    }


    // Método que realiza el sorteo de un determinado sorteo que recibe el controlador
    // Utilizamos la función realizarSorteo() para hacer el sorteo
    public function sortear() {
        //Incluye el modelo que corresponde
        require 'models/ParticipanteModel.php';
        require 'models/SorteoModel.php';

        // Recuperar el item con el código de sorteo recibido
        if (!isset($_REQUEST['cod_sorteo'])) {
            $this->view->show("errorView.php", array('error' => 'Debes indicar un código de sorteo'));
            exit;
        }

        $cod_sorteo = $_REQUEST['cod_sorteo'];

        //Creamos una instancia de nuestro "modelo"
        $items = new ParticipanteModel();
        $sorteo = new SorteoModel();

        //Le pedimos al modelo todos participantes de un sorteo
        $listado = $items->getByCodSorteo($cod_sorteo);

        // Pasar el array de objetos a un array asociativo para pasarlo a la función de sorteo
        $listadoArray = array_map(function ($participante) {
            return [
                'codigo' => $participante->getCodigo(),
                'nombre' => $participante->getNombre(),
                'regalo' => $participante->getRegalo(),
                'amigo' => null
            ];
        }, $listado);

        // Realizar el sorteo
        $this->realizarSorteo($listadoArray);

        // Volver a convertir el array asociativo a array de objetos para pasarlo a la vista
        foreach ($listadoArray as $data) {
            foreach ($listado as $participante) {
                if ($participante->getCodigo() == $data['codigo']) {
                    $participante->setAmigo($data['amigo']);
                    $participante->save();
                    break;
                }
            }
        }


        // Recuperamos si el sorteo está finalizado
        $sorteo_finalizado = $sorteo->getById($cod_sorteo)->getFinalizado();

        //Pasamos a la vista toda la información que se desea representar
        $data['items'] = $listado;
        $data['cod_sorteo'] = $cod_sorteo;
        $data['sorteo_finalizado'] = $sorteo_finalizado;

        // Finalmente presentamos nuestra plantilla 
        // Llamamos al método "show" de la clase View, que es el motor de plantillas
        // Genera la vista de respuesta a partir de la plantilla y de los datos
        $this->view->show("participanteListarView.php", $data);
    }




    // Método del controlador para crear un nuevo participante en un determinado sorteo
    public function nuevo() {
        require 'models/ParticipanteModel.php';
        $item = new ParticipanteModel();

        $errores = array();

        // Si recibe por GET o POST el objeto se comprueba que tiene los campos obligatorios
        if (isset($_REQUEST['submit'])) {
            // Comprobamos si se ha recibido el código del sorteo
            if (!isset($_REQUEST['cod_sorteo']) || empty($_REQUEST['cod_sorteo']))
                $errores['cod_sorteo'] = "* Nombre: debes indicar un código de sorteo.";

            // Comprobamos si se ha recibido el nombre
            if (!isset($_REQUEST['nombre']) || empty($_REQUEST['nombre']))
                $errores['nombre'] = "* Nombre: debes indicar un nombre.";

            // Si no hay errores actualizamos en la BD
            if (empty($errores)) {
                $item->setCodSorteo($_REQUEST['cod_sorteo']);
                $item->setNombre($_REQUEST['nombre']);
                $item->setRegalo($_REQUEST['regalo']);
                $item->save();

                // Finalmente llama al método listar para que devuelva vista con listado de participantes actualizado
                header("Location: index.php?controlador=Participante&accion=listarByCodSorteo&cod_sorteo=" . $_REQUEST['cod_sorteo']);
                exit;
            }
        }

        // Si no recibe el item para añadir, devuelve la vista para añadir un nuevo item
        $this->view->show("participanteNuevoView.php", array('errores' => $errores, 'cod_sorteo' => $_REQUEST['cod_sorteo']));


    }

    // Este método no está implementado bien --> adáptalo para que funcione correctamente
    public function editar() {

        require 'models/ParticipanteModel.php';
        $items = new ParticipanteModel();

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

            // Comprobamos si se ha recibido el regalo
            if (!isset($_REQUEST['regalo']) || empty($_REQUEST['regalo']))
                $errores['regalo'] = "* Regalo: es obligatorio el regalo.";

            // Si no hay errores actualizamos en la BD
            if (empty($errores)) {
                // Cambia el valor del item y lo guarda en BD
                $item->setNombre($_REQUEST['nombre']);
                $item->setRegalo($_REQUEST['regalo']);
                $item->save();

                // Reenvía a la aplicación a la lista de items
                header("Location: index.php?controlador=Participante&accion=listarByCodSorteo&cod_sorteo=" . $item->getCodSorteo());
                exit;
            }
        }

        // Si no se ha pulsado el botón de actualizar se carga la vista para editar el item
        $this->view->show("participanteEditarView.php", array('item' => $item, 'errores' => $errores));



    }

    // Este método no está implementado bien --> adáptalo para que funcione correctamente
    public function borrar() {
        // Comprobamos si el usuario está autenticado
        include_once('views/common/autentificacion.php');

        //Incluye el modelo que corresponde
        require_once 'models/ParticipanteModel.php';

        //Creamos una instancia de nuestro "modelo"
        $items = new ParticipanteModel();

        // Recupera el item con el código recibido por GET o por POST
        $item = $items->getById($_REQUEST['codigo']);

        if ($item == null) {
            $this->view->show("errorView.php", array('error' => 'No existe codigo'));
        } else {
            // Si existe lo elimina de la base de datos y vuelve al inicio de la aplicación
            // Recupera el código de sorteo antes de borrar para la redirección
            $codigo_sorteo = $item->getCodSorteo();

            if ($item->esAmigoDeAlguien()) {
                $this->view->show("errorView.php", array('error' => 'No se puede borrar el participante porque es amigo de alguien.'));
                exit;
            } else {
                $item->delete();
                header("Location: index.php?controlador=Participante&accion=listarByCodSorteo&cod_sorteo=" . $codigo_sorteo);
                exit;
            }
        }
    }

}
?>