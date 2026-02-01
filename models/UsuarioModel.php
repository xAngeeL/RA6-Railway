<?php

// Clase del modelo para trabajar con objetos Usuario que se almacenan en BD en la tabla USUARIO
class UsuarioModel
{
    // Conexión a la BD
    protected $db;

    // Atributos del objeto item que coinciden con los campos de la tabla USUARIO
    private $codigo;
    private $login;
    private $password;

    private $es_admin;

    // Constructor que utiliza el patrón Singleton para tener una única instancia de la conexión a BD
    public function __construct()
    {
        //Traemos la única instancia de PDO
        $this->db = SPDO::singleton();
    }

    // Getters y Setters
    public function getCodigo()
    {
        return $this->codigo;
    }
    public function setCodigo($codigo)
    {
        return $this->codigo = $codigo;
    }

    public function getLogin()
    {
        return $this->login;
    }
    public function setLogin($login)
    {
        return $this->login = $login;
    }

    public function getEsAdmin()
    {
        return $this->es_admin;
    }

    public function setEsAdmin($es_admin)
    {
        return $this->es_admin = $es_admin;
    }

    // Método para obtener todos los registros de la tabla USUARIO
    // Devuelve un array de objetos de la clase UsuarioModel
    public function getAll()
    {
        //realizamos la consulta de todos los items
        $consulta = $this->db->prepare('SELECT * FROM USUARIO');
        $consulta->execute();
        
        // OJO!! El fetchAll() funcionará correctamente siempre que el nombre
        // de los atributos de la clase coincida con los campos de la tabla
        $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "UsuarioModel");

        //devolvemos la colección para que la vista la presente.
        return $resultado;
    }


    // Método que devuelve (si existe en BD) un objeto UsuarioModel con un código determinado
    public function getById($codigo)
    {
        $gsent = $this->db->prepare('SELECT * FROM USUARIO WHERE codigo = ?');
        $gsent->bindParam(1, $codigo);
        $gsent->execute();

        $gsent->setFetchMode(PDO::FETCH_CLASS, "UsuarioModel");
        $resultado = $gsent->fetch();

        return $resultado;
    }

    // Método que devuelve (si existe en BD) un objeto UsuarioModel con un login determinado
    public function getByLogin($login)
    {
        $gsent = $this->db->prepare('SELECT * FROM USUARIO WHERE login = ?');
        $gsent->bindParam(1, $login);
        $gsent->execute();

        $gsent->setFetchMode(PDO::FETCH_CLASS, "UsuarioModel");
        $resultado = $gsent->fetch();

        return $resultado;
    }

    // Método para autenticar a un usuario
    public function autenticar($login,$password): bool
    {
        $gsent = $this->db->prepare('SELECT * FROM USUARIO WHERE login = ? AND password=?');
        $gsent->bindParam(1, $login);
        $password_encrypt = sha1($password);
        $gsent->bindParam(2, $password_encrypt);
        $gsent->execute();

        $gsent->setFetchMode(PDO::FETCH_CLASS, "UsuarioModel");
        $resultado = $gsent->fetch();

        if (!$resultado) {
            return false;
        } else {
            return true;
        }
    }

    // Método que almacena en BD un objeto UsuarioModel
    // Si tiene ya código actualiza el registro y si no tiene lo inserta
    public function save()
    {
        if (!isset($this->codigo)) {
            $consulta = $this->db->prepare('INSERT INTO USUARIO(login,password) VALUES (?,?)');
            $consulta->bindParam(1, $this->login);
            $consulta->bindParam(2, $this->password);
            $consulta->execute();
        } else {
            $consulta = $this->db->prepare('UPDATE USUARIO SET login=?,password=? WHERE codigo=?');
            $consulta->bindParam(1, $this->login);
            $consulta->bindParam(2, $this->password);
            $consulta->bindParam(3, $this->codigo);
            $consulta->execute();
        }
    }

    // Método que elimina el UsuarioModel de la BD
    public function delete()
    {
        $consulta = $this->db->prepare('DELETE FROM USUARIO WHERE codigo=?');
        $consulta->bindParam(1, $this->codigo);
        $consulta->execute();
    }
}
?>