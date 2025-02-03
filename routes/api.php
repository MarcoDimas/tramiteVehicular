<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\validacionTramiteVehiculo;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::any('soapService', function () {

    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="SOAP Service"');
        header('HTTP/1.0 401 Unauthorized');
        die("No autorizado para usar este servicio");
    }

    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
    $user = User::where('email', $username)->first();

    if (!$user || !password_verify($password, $user->password)) {
        header('HTTP/1.0 401 Unauthorized');
        die("Acceso no autorizado");
    }


    $server = new nusoap_server();
    $server->configureWSDL('SoapService', false, url('soapService'), 'document');


    $server->wsdl->addComplexType(
        'ResponseData',
        'complexType',
        'struct',
        'all',
        '',
        [
            'message' => ['name' => 'message', 'type' => 'xsd:string'],
            'encrypted' => ['name' => 'encrypted', 'type' => 'xsd:string'],
            'statusCode' => ['name' => 'statusCode', 'type' => 'xsd:int'],
            'statusText' => ['name' => 'statusText', 'type' => 'xsd:string']
        ]
    );

    $server->register(
        'validarTramite',
        ['serie' => 'xsd:string', 'folio' => 'xsd:string'],
        ['return' => 'tns:ResponseData'],
        'urn:SoapService',
        'urn:SoapService#validarTramite',
        'rpc',
        'encoded',
        'Valida un trámite y lo guarda en la base de datos'
    );

    // 📡 Manejo de WSDL
    if (isset($_GET['wsdl'])) {
        header("Content-Type: text/xml");
        echo $server->wsdl->serialize();
        exit;
    }

    // 📩 Capturar la solicitud SOAP
    $request = file_get_contents("php://input");

    // 📌 Limpiar cualquier salida previa
    while (ob_get_level()) {
        ob_end_clean();
    }

    header("Content-Type: text/xml; charset=utf-8");

    // 🔄 Procesar la solicitud SOAP
    $server->service($request);
    exit;
});


function validarTramite($serie, $folio)
{

    $data = "$serie|$folio";
    $encrypted = encrypt_decrypt('encrypt', $data);

    ValidacionTramiteVehiculo::create([
        'serie' => $serie,
        'folio' => $folio,
        'encriptado' => $encrypted
    ]);

    return [
        'message' => 'Datos almacenados y encriptados correctamente.',
        'encrypted' => $encrypted,
        'statusCode' => 200,
        'statusText' => 'OK'
    ];
}

function encrypt_decrypt($action, $string)
{
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = "clave_Secreta";
    $secret_iv = "clave_Secreta2";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    
    return $output;
}

