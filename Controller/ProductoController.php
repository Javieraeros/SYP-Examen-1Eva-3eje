<?php
require_once "Controller.php";

class ProductoController extends Controller
{
    public function manageGetVerb(Request $request)
    {

        $listaProductos = null;
        $id = null;
        $letra=null;
        $precioMin=null;
        $precioMax=null;
        $response = null;
        $code = null;

        if (isset($request->getUrlElements()[2])) {
            $id = $request->getUrlElements()[2];
        }
        //Vemos si quiere filtrar por letra
        if(isset($request->getQueryString()["letra"])){
            $letra=$request->getQueryString()["letra"];
            utf8_decode($letra);
            $listaProductos=ManejadoraProductoModel::getProductoLetra($letra);

        }else{
            //Aquí tenemos que ver si quiere filtrar por precio mín, máx o ambos
            $precioMin=$request->getQueryString()["preciomin"];
            $precioMax=$request->getQueryString()["preciomax"];

            //Para evitar precios por debajo de 0€ tanto el precioMin como el máximo deberan ser mayor que 0
            if(isset($precioMin) or isset($precioMax)){
                //Pongo este if aquí, y no arriba, para evitar que teniendo una query string con preciomin
                //y preciomax, llame a getProducto(id), por lo que no muestre mensaje de error
                //si el precioMin es <0 o Preciomax<0
                if($precioMin>=0 and $precioMax>=0){
                    $listaProductos=ManejadoraProductoModel::getProductoPrecio($precioMin,$precioMax);
                }
            }else{
                $listaProductos = ManejadoraProductoModel::getProducto($id);
            }
        }

        if ($listaProductos != null) {
            $code = '200';

        } else {

            //We could send 404 in any case, but if we want more precission,
            //we can send 400 if the syntax of the entity was incorrect...
            if (ManejadoraProductoModel::isValid($id) or ManejadoraProductoModel::isCharacter($letra) or
                ($precioMin>=0 and $precioMax>=0 and $precioMin<=$precioMax)) {
                $code = '204';
            } else {
                $code = '400';
            }

        }

        $response = new Response($code, null, $listaProductos, $request->getAccept());
        $response->generate();

    }
    
    public function managePutVerb(Request $request)
    {
        $response=null;
        $code=null;
        $resultado=null;
        $producto=null;



        //Solo se actualizará, añadirá un nuevo elemento, si el put se refiere a un elemento en concreto
        if (isset($request->getUrlElements()[2])) {
            $id = $request->getUrlElements()[2];

            $producto=new ProductoModel($id //Nos da igual qeu poner aquí, puesto que es un campo autogenerado
                ,$request->getBodyParameters()->nombre
                ,$request->getBodyParameters()->descripcion
                ,$request->getBodyParameters()->precio
            );
            //Si los datos son válidos
            if($producto->getnombre()!="" and $producto->getprecio()>0){
                /*Aquí decidimos si lo que tenemos que hacer es un put(actualización), en caso de que el sorteo exista
                o un put(inserción) en caso de que el sorteo no exista*/

                $existeSorteo = ManejadoraProductoModel::getProducto($id);
                if ($existeSorteo != null) {
                    $resultado = ManejadoraProductoModel::putProducto($producto);
                } else {
                    $resultado = ManejadoraProductoModel::postProducto($producto);
                }

                if($resultado){
                    $code='200';
                }else{
                    $code='405';
                }
            }else{
                $code='422';
            }
        }else {
            $code = '400';
        }

        $response = new Response($code, null, null, $request->getAccept());
        $response->generate();
    }
}