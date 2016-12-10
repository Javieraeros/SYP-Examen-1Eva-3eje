<?php

require_once "DbNames.php";
require_once "ProductoModel.php";
//ToDo ¿Devolver lo que introducimos?
class ManejadoraProductoModel
{

    public static function getProducto($id)
    {
        $listaProducto = null;

        $db = DatabaseModel::getInstance();
        $db_connection = $db->getConnection();


        $valid = self::isValid($id);

        //If the $id is valid or the client asks for the collection ($id is null)
        if ($valid === true || $id == null) {
            $query = "SELECT " . \ConstantesDB\ConsProductos::cod . ","
                . \ConstantesDB\ConsProductos::nombre.","
                . \ConstantesDB\ConsProductos::descripcion.","
                . \ConstantesDB\ConsProductos::precio . " FROM "
                . \ConstantesDB\ConsProductos::TABLE_NAME;


            if ($id != null) {
                $query = $query . " WHERE " . \ConstantesDB\ConsProductos::cod . " = ?";
            }

            $prep_query = $db_connection->prepare($query);

            //Si solo pide un sorteo, devuelve un objeto, en caso contrario devuelve un array
            if ($id != null) {
                $prep_query->bind_param('i', $id);
            }else{
                $listaProducto = array();
            }

            $prep_query->execute();

            $prep_query->bind_result($cod,$nombre,$descripcion,$precio);

            //Necesario para que no pete cuando hago varias consultas
            $prep_query->store_result();

            while ($prep_query->fetch()) {
                $nombre=utf8_encode($nombre);
                $descripcion=utf8_encode($descripcion);

                $producto = new ProductoModel($cod,$nombre,$descripcion,$precio);
                //Si solo pide un producto, devuelve un objeto, en caso contrario devuelve un array
                if($id==null){
                    $listaProducto[] = $producto;
                }else{
                    $listaProducto=$producto;
                }
            }

        }
        $db->closeConnection();

        return $listaProducto;
    }

    public static function isValid($id)
    {
        $res = false;

        if (ctype_digit($id)) {
            $res = true;
        }
        return $res;
    }


    /**
     * @param $producto: será una instacia de productomodel, el controller
     * se encargará de la validación
     */
    public static function postProducto(ProductoModel $producto){
        $db=DatabaseModel::getInstance();
        $connection=$db->getConnection();

        //Esto es para evitar error de que solo se pueden pasar variables por referencia.
        $cod=$producto->getcod();
        $nombre=$producto->getnombre();
        $descripcion=$producto->getdescripcion();
        $precio=$producto->getprecio();


        $query=$connection->prepare("Insert into ". \ConstantesDB\ConsProductos::TABLE_NAME.
            " (".\ConstantesDB\ConsProductos::cod.
            ",".\ConstantesDB\ConsProductos::nombre.
            "," . \ConstantesDB\ConsProductos::descripcion.
            ",".\ConstantesDB\ConsProductos::precio.
            ") Values (?,?,?,?);");


        $query->bind_param("issd",$cod,$nombre,
            $descripcion,
            $precio);

        $resultado=$query->execute();

        $db->closeConnection();
        return $resultado;

    }

    /**
     * @param $producto: será una instacia de productomodel, el controller
     * se encargará de la validación
     */
    public static function putProducto(ProductoModel $producto){
        $db=DatabaseModel::getInstance();
        $connection=$db->getConnection();

        $cod=$producto->getcod();
        $nombre=$producto->getnombre();
        $descripcion=$producto->getdescripcion();
        $precio=$producto->getprecio();

        $query=$connection->prepare("Update ". \ConstantesDB\ConsProductos::TABLE_NAME.
            " set ". \ConstantesDB\ConsProductos::nombre.
            "= ? ,".\ConstantesDB\ConsProductos::descripcion.
            "= ? ,".\ConstantesDB\ConsProductos::precio.
            "= ? where " .\ConstantesDB\ConsProductos::cod.
            "= ? ;");



        $query->bind_param("ssdi",$nombre,$descripcion,
            $precio,$cod);
        $resultado=$query->execute();

        $db->closeConnection();
        return $resultado;

    }

}