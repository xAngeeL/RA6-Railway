<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Controlador general para la aplicación
class AppController {
    // Atributo con el motor de plantillas del microframework
    protected $view;

    // Constructor. Únicamente instancia un objeto View y lo asigna al atributo
    function __construct() {
        //Creamos una instancia de nuestro mini motor de plantillas
        $this->view = new View();
    }

    // Método del controlador para hacer login
    public function login() {
        require 'models/UsuarioModel.php';

        $usuario = new UsuarioModel();

        $errores = null;

        // Si se ha pulsado el botón de entrar
        if (isset($_REQUEST['submit'])) {

            if (isset($_REQUEST['login']) && isset($_REQUEST['password'])) {
                $usuario_existe = $usuario->getByLogin($_REQUEST['login']);

                if ($usuario_existe) {
                    if ($usuario->autenticar($_REQUEST['login'], $_REQUEST['password'])) {
                        // Si la autenticación es correcta guardamos variable de sesion y  vamos a la pantalla inicial de la app
                        session_start();
                        $_SESSION['usuario_app'] = $_REQUEST['login'];
                        header("Location: index.php");
                        exit;
                    } else {
                        $errores['login'] = "La contraseña no es la correcta";
                    }
                } else {
                    $errores['login'] = "No existe usuario con este login";
                }
            } else {
                $errores['login'] = "Hay que enviar login y password";
            }
        }

        // Si llego hasta aquí es porque no se ha llamado a ninguna vista, por lo que muestro el error
        $this->view->show("loginView.php", array('errores' => $errores));
    }

    // Método para cerrar sesión
    public function logout() {
        // Recuperamos la información de la sesión
        session_start();

        // Y la eliminamos
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

?>