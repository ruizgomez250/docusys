<?php

use App\Models\Producto;
use App\Models\Rol;



function obtenerIdUsuario()
{
    if (auth()->check()) {
        $idUsuario = auth()->id();
        // Hacer algo con $idUsuario
        return $idUsuario;
    } else {
        // El usuario no está autenticado
        // Puedes redireccionar a la página de inicio de sesión u otro manejo
    }
}
function verificaModelo(string $idusuario, string $nombremodelo)
{
    $permisos = Rol::where('id_usuario', $idusuario)
        ->where('nombre_modelo', $nombremodelo)
        ->first();
    if ($permisos) {
        return true;
    } else {
        return false;
    }
}




if (!function_exists('formatoHora')) {
    function formatoHora($hora)
    {
        return \Carbon\Carbon::parse($hora)->format('H:i');
    }
}

if (!function_exists('formatoFecha')) {
    function formatoFecha($fecha)
    {
        return \Carbon\Carbon::parse($fecha)->format('d-m-Y');
    }
}

if (!function_exists('formatoFecha2')) {
    function formatoFecha2($fecha)
    {
        return \Carbon\Carbon::parse($fecha)->format('Y-m-d');
    }
}

if (!function_exists('Valor')) {
    function formatNumber($number)
    {
        return number_format($number, 0, ',', '.');
    }
}

if (!function_exists('Valor2')) {
    function formatoNumero($number)
    {
        return number_format($number, 0, ',', '.'); // Mantener 2 decimales
    }
}



if (!function_exists('eliminarPuntos')) {
    /**
     * Elimina los puntos de un número y devuelve un número flotante.
     *
     * @param string $numero
     * @return float
     */
    function eliminarPuntos($numero)
    {
        return (float) str_replace(',', '.', str_replace('.', '', $numero));
    }
}

function calcularEdad($fechaNacimiento)
{
    $fechaNacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento);
    return $edad->y;
}


// Puedes agregar más funciones aquí si lo deseas
if (!function_exists('generarCodigoAleatorio')) {
    /**
     * Genera un código aleatorio mezclando números y letras.
     *
     * @param int $length La longitud del código a generar.
     * @return string El código aleatorio generado.
     */
    function generarCodigoAleatorio($length = 12)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
