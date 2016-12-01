<?php


class ProductoModel implements JsonSerializable
{
    private $cod;
    private $nombre;
    private $descripcion;
    private $precio;

    /**
     * Sorteo constructor.
     * @param $cod
     * @param $nombre
     * @param $descripcion
     * @param $precio
     */
    public function __construct($cod, $nombre, $descripcion, $precio)
    {
        $this->cod = $cod;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
    }


    /**
     * @return mixed
     */
    public function getcod()
    {
        return $this->cod;
    }

    /**
     * @param mixed $cod
     */
    public function setcod($cod)
    {
        $this->cod = $cod;
    }

    /**
     * @return mixed
     */
    public function getnombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setnombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getdescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setdescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return mixed
     */
    public function getprecio()
    {
        return $this->precio;
    }

    /**
     * @param mixed $precio
     */
    public function setprecio($precio)
    {
        $this->precio = $precio;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */

    //Needed if the properties of the class are private.
    //Otherwise json_encode will encode blank objects
    function jsonSerialize()
    {
        return array(
            'cod' => $this->cod,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio);
    }

    public function __sleep(){
        return array('nombre','cod','descripcion','precio' );
    }

}