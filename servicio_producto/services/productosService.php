<?php
// services/ProductoService.php
require_once __DIR__ . '/../vendor/econea/nusoap/src/nusoap.php';
require_once __DIR__ . '/../config/productos.php';
require_once __DIR__ . '/../controllers/ProductosController.php';

// Configuración del servicio SOAP
$namespace = "Productos";
$server = new Soap_Server();
$server->configureWSDL('ServicioProductos', $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

// Definir el tipo complejo (o modelo de datos) para el producto

   // Definir el tipo complejo (o modelo de datos) para el producto
    $server->wsdl->addComplexType(
    'Producto',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'nombre' => array('name' => 'nombre', 'type' => 'xsd:string'),
        'descripcion' => array('name' => 'descripcion', 'type' => 'xsd:string'),
        'stock' => array('name' => 'stock', 'type' => 'xsd:int'),
        'idcategoria' => array('name' => 'idcategoria', 'type' => 'xsd:int'),
        'idproveedor' => array('name' => 'idproveedor', 'type' => 'xsd:int'),
        'idestado' => array('name' => 'idestado', 'type' => 'xsd:int'),
        'iddescuento' => array('name' => 'iddescuento', 'type' => 'xsd:int'),
        'precio' => array('name' => 'precio', 'type' => 'xsd:float'),
    )
);

    function CrearProducto1($params) {
        error_log(print_r($params, true)); // Esto te mostrará los datos que llegan al servidor
        // ... resto del código para insertar en la base de datos
    }

// Registrar método SOAP para ver todos los productos
$server->register(
    'VerProductos',
    array(),
    array('return' => 'xsd:Array'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Ver todos los productos'
);

// Registrar método SOAP para ver detalle de un producto
$server->register(
    "VerProducto",
    array('id' => 'xsd:int'),    // Parámetro de entrada
    array('return' => 'xsd:string'),  // Tipo de retorno
    'urn:productos',            // Namespace
    'urn:productos#VerProducto', // Acción SOAP
    'rpc',                      // Estilo
    'encoded',                  // Uso
    'Obtiene los detalles de un producto por su ID' // Descripción
);


// Registrar método SOAP para crear un producto
$server->register(
    'CrearProducto',
    array('data' => 'tns:Producto'),
    array('return' => 'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Crear un producto'
);

// Registrar método SOAP para actualizar un producto
$server->register(
    'ActualizarProducto',
    array('data' => 'tns:Producto', 'id' => 'xsd:int'),
    array('return' => 'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Actualizar un producto'
);

// Registrar método SOAP para eliminar un producto
$server->register(
    'EliminarProducto',
    array('id' => 'xsd:int'), // El argumento de entrada es el ID del producto
    array('return' => 'xsd:string'), // Tipo
);

$server->register(
    'FiltrarProductos',
    array('valor' => 'xsd:string'), // Parámetro de entrada (variable de búsqueda)
    array('return' => 'xsd:string'), // Tipo de retorno (respuesta en XML)
    $namespace,
    false,
    'rpc',
    'encoded',
    'Buscar productos en todas las columnas basado en un valor'
);
$server->wsdl->addComplexType(
    'FiltroProducto',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'nombre' => array('name' => 'nombre', 'type' => 'xsd:string'),
        'stock' => array('name' => 'stock', 'type' => 'xsd:int'),
        'categoria' => array('name' => 'categoria', 'type' => 'xsd:int')
    )
);

// Registrar método SOAP para filtrar productos
$server->register(
    'BuscarEnProductos',
    array('valor' => 'xsd:string'), // Parámetro de entrada
    array('return' => 'xsd:string'), // Tipo de retorno
    $namespace,
    false,
    'rpc',
    'encoded',
    'Buscar en productos por un valor en cualquier campo'
);


// Función que llama al controlador para calcular el total con descuento
$server->register(
    'CalcularTotalConDescuentoPorNombre',
    array('nombre' => 'xsd:string'), // Parámetro de entrada
    array('return' => 'xsd:string'), // Tipo de retorno
    $namespace,
    false,
    'rpc',
    'encoded',
    'Calcular el total con descuento para un producto, buscando por nombre'
);

function FiltrarProductos($valor)
{
    $pdo = getConnection('productos_bd'); // Conectar a la base de datos de productos
    $controller = new ProductoController($pdo); // Instanciar el controlador

    // Capturar la salida del controlador en formato XML
    ob_start();
    $controller->buscarEnProductos($valor); // Pasar la variable de búsqueda
    $xmlResponse = ob_get_clean();

    return $xmlResponse; // Devolver la respuesta en XML
}

// Función que llama al controlador para calcular el total con descuento por nombre
function CalcularTotalConDescuentoPorNombre($nombre)
{
    $pdo = getConnection('productos_bd'); // Conectar a la base de datos de productos
    $controller = new ProductoController($pdo); // Instanciar el controlador

    // Capturar la salida del controlador en formato XML
    ob_start();
    $controller->calcularTotalConDescuentoPorNombre($nombre); // Pasar el nombre del producto
    $xmlResponse = ob_get_clean();

    return $xmlResponse; // Devolver la respuesta en XML
}

// Función que llama al controlador para buscar en productos
function BuscarEnProductos($valor)
{
    $pdo = getConnection('productos_bd'); // Conectar a la base de datos de productos
    $controller = new ProductoController($pdo); // Instanciar el controlador

    // Capturar la salida del controlador en formato XML
    ob_start();
    $controller->buscarEnProductos($valor); // Pasar el valor de búsqueda al controlador
    $xmlResponse = ob_get_clean();

    return $xmlResponse; // Devolver la respuesta en XML
}
// Función que llama al controlador para buscar una categoría por nombre
function BuscarCategoria($nombre)
{
    $pdo = getConnection('productos_bd'); // Conectar a la base de datos de productos
    $categoriaModel = new Categoria($pdo); // Pasar la conexión al modelo
    $categorias = $categoriaModel->buscarPorNombre($nombre);

    // Convertir los resultados a XML y devolverlos como respuesta SOAP
    $xml = new SimpleXMLElement('<categorias/>');
    foreach ($categorias as $categoria) {
        $categoriaNode = $xml->addChild('categoria');
        foreach ($categoria as $key => $value) {
            $categoriaNode->addChild($key, htmlspecialchars($value));
        }
    }
    return $xml->asXML();
}

// Función que llama al controlador para ver todos los productos
function VerProductos() {
    $pdo = getConnection();
    $controller = new ProductoController($pdo);
    return $controller->getAllProductos();
}

// Función que llama al controlador para obtener el detalle de un producto
function VerProducto($id) {
    // Obtén la conexión a la base de datos
    $pdo = getConnection();
    
    // Instancia el controlador de Producto
    $controller = new ProductoController($pdo);
    
    // Devuelve los detalles del producto invocando el método getProductoDetail del controlador
    return $controller->getProductoDetail($id);
}


// Función que llama al controlador para crear producto
function CrearProducto($data) {
    // Imprimir datos para depuración
    var_dump($data);
    
    $pdo = getConnection();
    $controller = new ProductoController($pdo);
    return $controller->createProducto($data);
}

// Función que llama al controlador para actualizar producto
function ActualizarProducto($data, $id) {
    $pdo = getConnection();
    $controller = new ProductoController($pdo);
    return $controller->updateProducto($data, $id);
}

// Función que llama al controlador para eliminar producto
function EliminarProducto($id) {
    $pdo = getConnection();
    $controller = new ProductoController($pdo);
    return $controller->deleteProducto($id);
}

//-----------------------

// Procesar solicitud SOAP
$POST_DATA = file_get_contents("php://input");
$server->service($POST_DATA);
exit();
?>
