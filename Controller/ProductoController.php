<?php
require_once "Controller.php";

class ProductoController extends Controller
{
    public function manageGetVerb(Request $request)
    {

        $listaProductos = null;
        $id = null;
        $response = null;
        $code = null;

        if (isset($request->getUrlElements()[2])) {
            $id = $request->getUrlElements()[2];
        }


        $listaProductos = ManejadoraProductoModel::getProducto($id);

        if ($listaProductos != null) {
            $code = '200';

        } else {

            //We could send 404 in any case, but if we want more precission,
            //we can send 400 if the syntax of the entity was incorrect...
            if (ManejadoraProductoModel::isValid($id)) {
                $code = '404';
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