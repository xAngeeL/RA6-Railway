<?php

// Clase del modelo para trabajar con objetos Sorteo que se almacenan en BD en la tabla SORTEO
class SorteoModel
{
    // Conexión a la BD
    protected $db;

    // Atributos del objeto item que coinciden con los campos de la tabla SORTEO
    private $codigo;
    private $nombre;
    private $descripcion;
    private $finalizado;

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

    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        return $this->nombre = $nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        return $this->descripcion = $descripcion;
    }

    public function getFinalizado()
    {
        return $this->finalizado;
    }

    public function setFinalizado($finalizado)
    {
        return $this->finalizado = $finalizado;
    }

    // Método para obtener todos los registros de la tabla SORTEO
    // Devuelve un array de objetos de la clase SorteoModel
    public function getAll()
    {
        //realizamos la consulta de todos los items
        $consulta = $this->db->prepare('SELECT * FROM SORTEO');
        $consulta->execute();
        
        // OJO!! El fetchAll() funcionará correctamente siempre que el nombre
        // de los atributos de la clase coincida con los campos de la tabla
        $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "SorteoModel");

        //devolvemos la colección para que la vista la presente.
        return $resultado;
    }


    // Método que devuelve (si existe en BD) un objeto SorteoModel con un código determinado
    public function getById($codigo)
    {
        $gsent = $this->db->prepare('SELECT * FROM SORTEO WHERE codigo = ?');
        $gsent->bindParam(1, $codigo);
        $gsent->execute();

        $gsent->setFetchMode(PDO::FETCH_CLASS, "SorteoModel");
        $resultado = $gsent->fetch();

        return $resultado;
    }

    // Método que almacena en BD un objeto SorteoModel
    // Si tiene ya código actualiza el registro y si no tiene lo inserta
    public function save()
    {
        if (!isset($this->codigo)) {
            $consulta = $this->db->prepare('INSERT INTO SORTEO(nombre,descripcion,finalizado) VALUES (?,?,?)');
            $consulta->bindParam(1, $this->nombre);
            $consulta->bindParam(2, $this->descripcion);
            $consulta->bindParam(3, $this->finalizado);
            $consulta->execute();
        } else {
            $consulta = $this->db->prepare('UPDATE SORTEO SET nombre=?,descripcion=?,finalizado=? WHERE codigo=?');
            $consulta->bindParam(1, $this->nombre);
            $consulta->bindParam(2, $this->descripcion);
            $consulta->bindParam(3, $this->finalizado);
            $consulta->bindParam(4, $this->codigo);
            $consulta->execute();
        }
    }

    // Método que elimina el SorteoModel de la BD
    public function delete()
    {
        $consulta = $this->db->prepare('DELETE FROM SORTEO WHERE codigo=?');
        $consulta->bindParam(1, $this->codigo);
        $consulta->execute();
    }

    // Método que indica si el sorteo tiene participantes
    public function tieneParticipantes() {
        $gsent = $this->db->prepare('SELECT COUNT(*) FROM PARTICIPANTE WHERE cod_sorteo = ?');
        $gsent->bindParam(1, $this->codigo);
        $gsent->execute();

        $resultado = $gsent->fetchColumn();

        return $resultado > 0;
    }

     // Método que limpia los amigos de los participantes en un sorteo determinado
    public function limpiarAmigos() {
        $consulta = $this->db->prepare('UPDATE PARTICIPANTE SET amigo = NULL WHERE cod_sorteo = ?');
        $consulta->bindParam(1, $this->codigo);
        $consulta->execute();
    }
}
?>