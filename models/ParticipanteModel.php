<?php

// Clase del modelo para trabajar con la tabla PARTICIPANTE
class ParticipanteModel {
    // Conexión a la BD
    protected $db;

    // Atributos del objeto item que coinciden con los campos de la tabla PARTICIPANTE
    private $codigo;
    private $cod_sorteo;
    private $nombre;
    private $amigo;
    private $regalo;

    // Constructor que utiliza el patrón Singleton para tener una única instancia de la conexión a BD
    public function __construct() {
        //Traemos la única instancia de PDO
        $this->db = SPDO::singleton();
    }

    // Getters y Setters
    public function getCodigo() {
        return $this->codigo;
    }
    public function setCodigo($codigo) {
        return $this->codigo = $codigo;
    }

    public function getCodSorteo() {
        return $this->cod_sorteo;
    }

    public function setCodSorteo($cod_sorteo) {
        return $this->cod_sorteo = $cod_sorteo;
    }

    public function getNombre() {
        return $this->nombre;
    }
    public function setNombre($nombre) {
        return $this->nombre = $nombre;
    }

    public function getAmigo() {
        return $this->amigo;
    }

    public function setAmigo($amigo) {
        return $this->amigo = $amigo;
    }

    public function getRegalo() {
        return $this->regalo;
    }

    public function setRegalo($regalo) {
        return $this->regalo = $regalo;
    }


    // Método para obtener todos los registros de la tabla PARTICIPANTE
    // Devuelve un array de objetos de la clase ParticipanteModel
    public function getAll() {
        //realizamos la consulta de todos los items
        $consulta = $this->db->prepare('SELECT * FROM PARTICIPANTE');
        $consulta->execute();

        // OJO!! El fetchAll() funcionará correctamente siempre que el nombre
        // de los atributos de la clase coincida con los campos de la tabla
        $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "ParticipanteModel");

        //devolvemos la colección para que la vista la presente.
        return $resultado;
    }


    // Método que devuelve (si existe en BD) un objeto ParticipanteModel con un código determinado
    public function getById($codigo) {
        $gsent = $this->db->prepare('SELECT * FROM PARTICIPANTE WHERE codigo = ?');
        $gsent->bindParam(1, $codigo);
        $gsent->execute();

        $gsent->setFetchMode(PDO::FETCH_CLASS, "ParticipanteModel");
        $resultado = $gsent->fetch();

        return $resultado;
    }


    // Método que devuelve el nombre del amigo del participante
    public function getNombreAmigo() {
        $gsent = $this->db->prepare('SELECT nombre FROM PARTICIPANTE WHERE codigo = ?');
        $gsent->bindParam(1, $this->amigo);
        $gsent->execute();

        $resultado = $gsent->fetchColumn();

        return $resultado;
    }

    // Devuelve los participantes en un determinado sorteo    
    public function getByCodSorteo($codigo_sorteo) {
        $gsent = $this->db->prepare('SELECT * FROM PARTICIPANTE WHERE cod_sorteo = ?');
        $gsent->bindParam(1, $codigo_sorteo);
        $gsent->execute();

        $gsent->setFetchMode(PDO::FETCH_CLASS, "ParticipanteModel");
        $resultado = $gsent->fetchAll();

        return $resultado;
    }


    // Método que almacena en BD un objeto ParticipanteModel
    // Si tiene ya código actualiza el registro y si no tiene lo inserta
    public function save() {
        if (!isset($this->codigo)) {
            $consulta = $this->db->prepare('INSERT INTO PARTICIPANTE(cod_sorteo,nombre,regalo) VALUES (?,?,?)');
            $consulta->bindParam(1, $this->cod_sorteo);
            $consulta->bindParam(2, $this->nombre);
            $consulta->bindParam(3, $this->regalo);
            $consulta->execute();
        } else {
            $consulta = $this->db->prepare('UPDATE PARTICIPANTE SET cod_sorteo=?,nombre=?,amigo=?,regalo=? WHERE codigo=?');
            $consulta->bindParam(1, $this->cod_sorteo);
            $consulta->bindParam(2, $this->nombre);
            $consulta->bindParam(3, $this->amigo);
            $consulta->bindParam(4, $this->regalo);
            $consulta->bindParam(5, $this->codigo);
            $consulta->execute();
        }
    }

    // Método que elimina el ParticipanteModel de la BD
    public function delete() {
        $consulta = $this->db->prepare('DELETE FROM PARTICIPANTE WHERE codigo=?');
        $consulta->bindParam(1, $this->codigo);
        $consulta->execute();
    }


    // Metodo que indica si un participante es amigo de otro
    public function esAmigoDeAlguien() {
        $gsent = $this->db->prepare('SELECT COUNT(*) FROM PARTICIPANTE WHERE amigo = ?');
        $gsent->bindParam(1, $this->codigo);
        $gsent->execute();

        $resultado = $gsent->fetchColumn();

        return $resultado > 0;
    }
   
}
?>