<?php
namespace App;
use \DateTime;
use \Exception;
trait ValidadorTrait
{
    protected array $errores = [];

    public function validarTexto($valor, $campo, $min = 1, $max = 255)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }
        if (!preg_match("/^[\p{L}\s]+$/ui", $valor)) {
            return "El campo $campo solo puede contener letras y espacios.";
        }
        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }
        return true;
    }

    public function validarDecimal($valor, $campo = 'monto', $min = 0, $max = 999999.99){
        $valor = trim($valor);

        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }

        // Validar formato decimal: números con hasta 2 decimales
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $valor)) {
            return "El campo $campo debe ser un número válido con hasta 2 decimales.";
        }

        // Convertir a número para comparar
        $numero = (float)$valor;

        if ($numero < $min) {
          return "El campo $campo no puede ser menor que $min.";
        }

        if ($numero > $max) {
          return "El campo $campo no puede ser mayor que $max.";
        }

        return true;
    }

    public function validarDescripcion($valor, $campo, $min = 1, $max = 255)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,\-#áéíóúÁÉÍÓÚüÜ]+$/u", $valor)) {
            return "El campo $campo solo puede contener letras, números y signos (.,-#).";
        }
        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }
        return true;
    }

    public function validarTelefono($valor, $campo = 'telefono', $requerido = true)
    {
        $valor = trim($valor);
        
        if ($valor === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Eliminar espacios, guiones, paréntesis y otros caracteres comunes en teléfonos
        $telefonoLimpio = preg_replace('/[\s\-\(\)\+]/', '', $valor);
        
        // Validar formato de teléfono (mínimo 7 dígitos, máximo 15)
        if (!preg_match('/^\d{7,15}$/', $telefonoLimpio)) {
            return "El campo $campo debe contener entre 7 y 15 dígitos. Formatos válidos: 04121234567, 04161234567, 04141234567, 02511234567";
        }

        return true;
    }

    public function validarDireccion($valor, $campo = 'direccion', $min = 3, $max = 255)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }

        // Patrón más flexible para direcciones: letras, números, espacios y caracteres comunes en direcciones
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,\-\#\/°áéíóúÁÉÍÓÚüÜ()]+$/u", $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, espacios y los signos: . , - # / ° ( )";
        }

        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }

        return true;
    }

    public function validarStatus($valor, $campo = 'estatus')
    {
        if (!in_array((string)$valor, ['0', '1'], true)) {
            return "El campo $campo solo puede ser 0 (Inactivo) o 1 (Activo).";
        }
        return true;
    }
    
     /**
     * Valida campos numéricos enteros (como stock, cantidad, etc.)
     */
    public function validarNumerico($valor, $campo, $min = 1, $max = 11)
    {
        $valor = trim((string)$valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }
        if (!ctype_digit($valor)) {
            return "El campo $campo debe ser un número entero.";
        }
        if (strlen($valor) > $max) {
            return "El campo $campo no puede superar los $max dígitos.";
        }
        if ((int)$valor < 0) {
            return "El campo $campo no puede ser negativo.";
        }
        return true;
    }

    public function validarCodigoSelect($valor, $campo)
    {
        if (empty($valor)) {
            return "Debe seleccionar un valor para $campo.";
        }
        if (!is_numeric($valor) || (int)$valor <= 0) {
            return "El campo $campo debe ser un número válido.";
        }
        return true;
    }
    

    //Validaciones de Luis
    

    /**
     * Valida el rango entre dos fechas
     */
    public function validarRangoFechas($fecha_inicio, $fecha_fin)
    {
        $fechaInicio = DateTime::createFromFormat('Y-m-d', $fecha_inicio);
        $fechaFin = DateTime::createFromFormat('Y-m-d', $fecha_fin);

        if ($fechaInicio === false || $fechaFin === false) {
            return "Las fechas proporcionadas no son válidas.";
        }

        // Validar que la fecha de fin no sea menor que la de inicio
        if ($fechaFin < $fechaInicio) {
            return "La fecha de fin no puede ser menor que la fecha de inicio.";
        }

        // Validar que el rango no sea demasiado extenso (opcional, ajusta según necesidades)
        $diferencia = $fechaInicio->diff($fechaFin);
        if ($diferencia->y > 1) { // Máximo 5 años de diferencia
            return "El rango de fechas no puede ser mayor a 5 años.";
        }

        return true;
    }

    //Valida descripción específica para bitácora (más flexible que texto normal)
    
    public function validarDescripcionBitacora($valor, $campo = 'descripción', $min = 5, $max = 500)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }
        
        // Patrón más flexible para bitácora: permite letras, números, espacios, puntuación básica y caracteres especiales comunes
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,;:\-\#\(\)\[\]\/\\\?¿¡!áéíóúÁÉÍÓÚüÜ@%&+='\" ]+$/u", $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, espacios y signos de puntuación básicos.";
        }
        
        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }
        
        return true;
    }

    /**
     * Valida fecha específica para bitácora (permite fechas pasadas y futuras)
     */
    public function validarFechaBitacora($fecha, $campo = 'fecha', $requerido = true)
    {
        $fecha = trim($fecha);
        
        if ($fecha === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar formato básico YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return "El campo $campo debe tener el formato YYYY-MM-DD (ej: 2024-12-31).";
        }

        // Validar que sea una fecha real
        list($ano, $mes, $dia) = explode('-', $fecha);
        if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
            return "El campo $campo no es una fecha válida.";
        }

        // Validar que no sea una fecha demasiado antigua (por ejemplo, menor a 2000)
        $fechaDateTime = DateTime::createFromFormat('Y-m-d', $fecha);
        $fechaMinima = DateTime::createFromFormat('Y-m-d', '2000-01-01');
        
        if ($fechaDateTime < $fechaMinima) {
            return "El campo $campo no puede ser anterior al año 2000.";
        }

        // Validar que no sea una fecha demasiado futura (por ejemplo, mayor a 1 año desde hoy)
        $fechaMaxima = new DateTime();
        $fechaMaxima->modify('+1 year');
        
        if ($fechaDateTime > $fechaMaxima) {
            return "El campo $campo no puede ser mayor a 1 año a partir de la fecha actual.";
        }

        return true;
    }

    /**
     * Valida ID de usuario
     */
    public function validarUsuario($valor, $campo = 'usuario', $requerido = true)
    {
        $valor = trim($valor);
        
        if ($valor === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar que sea numérico y positivo
        if (!is_numeric($valor) || $valor <= 0) {
            return "El campo $campo debe ser un ID de usuario válido (número positivo).";
        }

        // Validar que no sea un número demasiado grande (ajustar según tu sistema)
        if ($valor > 9999) {
            return "El campo $campo contiene un ID de usuario inválido.";
        }

        return true;
    }

    /**
     * Valida título para notificaciones
     */
    public function validarTituloNotificacion($valor, $campo = 'título', $min = 3, $max = 100)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }
        
        // Patrón para títulos: letras, números, espacios y algunos signos básicos
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,;:\-\#\(\)\!¿¡áéíóúÁÉÍÓÚüÜ ]+$/u", $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, espacios y signos básicos (.,;:-\#!¿¡).";
        }
        
        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }
        
        return true;
    }

    /**
     * Valida descripción para notificaciones
     */
    public function validarDescripcionNotificacion($valor, $campo = 'descripción', $min = 5, $max = 500)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }
        
        // Patrón más flexible para notificaciones
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,;:\-\#\(\)\[\]\/\\\?¿¡!áéíóúÁÉÍÓÚüÜ@%&+='\" ]+$/u", $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, espacios y signos de puntuación básicos.";
        }
        
        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }
        
        return true;
    }

    /**
     * Valida fecha para notificaciones (permite fechas pasadas y futuras con límites razonables)
     */
    public function validarFechaNotificacion($fecha, $campo = 'fecha', $requerido = true)
    {
        $fecha = trim($fecha);
        
        if ($fecha === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar formato básico YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return "El campo $campo debe tener el formato YYYY-MM-DD (ej: 2024-12-31).";
        }

        // Validar que sea una fecha real
        list($ano, $mes, $dia) = explode('-', $fecha);
        if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
            return "El campo $campo no es una fecha válida.";
        }

        // Validar que no sea una fecha demasiado antigua (por ejemplo, menor a 2020)
        $fechaDateTime = DateTime::createFromFormat('Y-m-d', $fecha);
        $fechaMinima = DateTime::createFromFormat('Y-m-d', '2020-01-01');
        
        if ($fechaDateTime < $fechaMinima) {
            return "El campo $campo no puede ser anterior al año 2020.";
        }

        // Validar que no sea una fecha demasiado futura (por ejemplo, mayor a 2 años desde hoy)
        $fechaMaxima = new DateTime();
        $fechaMaxima->modify('+2 years');
        
        if ($fechaDateTime > $fechaMaxima) {
            return "El campo $campo no puede ser mayor a 2 años a partir de la fecha actual.";
        }

        return true;
    }

    /**
     * Valida status específico para notificaciones (0=no leída, 1=leída)
     */
    public function validarStatusNotificacion($valor, $campo = 'status')
    {
        if (!in_array((string)$valor, ['0', '1'], true)) {
            return "El campo $campo solo puede ser 0 (No leída) o 1 (Leída).";
        }
        return true;
    }

    /**
     * Valida ruta (opcional)
     */
    public function validarRuta($valor, $campo = 'ruta', $requerido = false)
    {
        $valor = trim($valor);
        
        if ($valor === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar longitud máxima
        if (mb_strlen($valor) > 255) {
            return "El campo $campo no puede tener más de 255 caracteres.";
        }

        // Validar formato básico de ruta (permite letras, números, guiones, barras, puntos)
        if (!preg_match('/^[a-zA-Z0-9\/\.\-_]+$/', $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, guiones, puntos y barras.";
        }

        return true;
    }

    /**
     * Sanitiza ruta
     */
    public function sanitizarRuta($ruta)
    {
        if ($ruta === null || $ruta === '') {
            return '';
        }
        
        // Eliminar espacios y caracteres potencialmente peligrosos
        $ruta = trim($ruta);
        $ruta = preg_replace('/[^\w\/\.\-]/', '', $ruta);
        
        return $ruta;
    }

    /**
     * Valida dirección de envío específica
     */
    public function validarDireccionEnvio($valor, $campo = 'dirección de envío', $min = 10, $max = 255)
    {
        $valor = trim($valor);
        if ($valor === '') {
            return "El campo $campo es obligatorio.";
        }

        // Patrón más específico para direcciones de envío
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,\-\#\/°áéíóúÁÉÍÓÚüÜ()&]+$/u", $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, espacios y los signos: . , - # / ° ( ) &";
        }

        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }

        return true;
    }

    /**
     * Valida estatus específico para envíos
     */
    public function validarEstatusEnvio($valor, $campo = 'estatus')
    {
        $estatusPermitidos = ['Pendiente', 'En proceso', 'En camino', 'Entregado', 'Cancelado'];
        
        if (!in_array($valor, $estatusPermitidos, true)) {
            return "El campo $campo debe ser uno de los siguientes: " . implode(', ', $estatusPermitidos);
        }
        
        return true;
    }

    /**
     * Valida número de seguimiento (opcional)
     */
    public function validarNumeroSeguimiento($valor, $campo = 'número de seguimiento', $requerido = false)
    {
        $valor = trim($valor);
        
        if ($valor === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar longitud
        if (mb_strlen($valor) < 5 || mb_strlen($valor) > 50) {
            return "El campo $campo debe tener entre 5 y 50 caracteres.";
        }

        // Validar formato: letras, números, guiones
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $valor)) {
            return "El campo $campo solo puede contener letras, números y guiones.";
        }

        return true;
    }

    public function validarTelefonodestino($valor, $campo = 'teléfono destinatario', $requerido = true)
        {
            $valor = trim($valor);
            
            // if ($valor === '') {
            //     return $requerido ? "El campo $campo es obligatorio." : true;
            // }

            // Eliminar espacios, guiones, paréntesis y otros caracteres comunes en teléfonos
            $telefonoLimpio = preg_replace('/[\s\-\(\)\+]/', '', $valor);
            
            // Validar formato de teléfono (mínimo 7 dígitos, máximo 15)
            if (!preg_match('/^\d{7,15}$/', $telefonoLimpio)) {
                return "El campo $campo debe contener entre 7 y 15 dígitos. Formatos válidos: 04121234567, 04161234567, 04141234567, 02511234567";
            }

            return true;
        }

    //Valida dirección exacta para delivery
    
    public function validarDireccionExacta($valor, $campo = 'dirección exacta', $min = 5, $max = 255)
    {
        $valor = trim($valor);
        // if ($valor === '') {
        //     return "El campo $campo es obligatorio.";
        // }

        // Patrón más específico para direcciones exactas de delivery
        if (!preg_match("/^[\p{L}ñÑ\d\s\.,\-\#\/°áéíóúÁÉÍÓÚüÜ()&:'\"]+$/u", $valor)) {
            return "El campo $campo contiene caracteres no válidos. Solo se permiten letras, números, espacios y los signos: . , - # / ° ( ) & : ' \"";
        }

        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }

        return true;
    }

    /**
     * Valida nombre del destinatario
     */
    public function validarNombreDestinatario($valor, $campo = 'destinatario', $min = 3, $max = 100)
    {
        $valor = trim($valor);
        // if ($valor === '') {
        //     return "El campo $campo es obligatorio.";
        // }
        
        // Patrón para nombres de personas: letras, espacios y algunos caracteres especiales
        if (!preg_match("/^[\p{L}\s'\.\-áéíóúÁÉÍÓÚüÜñÑ]+$/u", $valor)) {
            return "El campo $campo solo puede contener letras, espacios, apóstrofes (') y guiones.";
        }
        
        if (mb_strlen($valor) < $min || mb_strlen($valor) > $max) {
            return "El campo $campo debe tener entre $min y $max caracteres.";
        }
        
        return true;
    }

    /**
     * Valida correo electrónico
     */
    public function validarCorreodestino($valor, $campo = 'correo electrónico', $requerido = true)
    {
        $valor = trim($valor);
        
        // if ($valor === '') {
        //     return $requerido ? "El campo $campo es obligatorio." : true;
        // }

        // Validar formato básico de email
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            return "El campo $campo debe ser una dirección de correo electrónico válida.";
        }

        // Validar longitud máxima
        if (mb_strlen($valor) > 100) {
            return "El campo $campo no puede tener más de 100 caracteres.";
        }

        return true;
    }

    /**
     * Valida estatus específico para delivery
     */
    public function validarEstatusDelivery($valor, $campo = 'estatus')
    {
        $estatusPermitidos = ['Pendiente', 'En proceso', 'En camino', 'Entregado', 'Cancelado'];
        
        if (!in_array($valor, $estatusPermitidos, true)) {
            return "El campo $campo debe ser uno de los siguientes: " . implode(', ', $estatusPermitidos);
        }
        
        return true;
    }

    /**
     * Valida estatus específico para pagos
     */
    public function validarEstatusPago($valor, $campo = 'estatus de pago')
    {
        $estatusPermitidos = ['Pendiente', 'Aprobado', 'Rechazado', 'En proceso', 'Completado'];
        
        if (!in_array($valor, $estatusPermitidos, true)) {
            return "El campo $campo debe ser uno de los siguientes: " . implode(', ', $estatusPermitidos);
        }
        
        return true;
    }

    /**
     * Valida estatus para aprobación/rechazo de pagos
     */
    public function validarEstatusPagoAprobacion($valor, $campo = 'estatus de pago')
    {
        $estatusPermitidos = ['Aprobado', 'Rechazado'];
        
        if (!in_array($valor, $estatusPermitidos, true)) {
            return "El campo $campo debe ser 'Aprobado' o 'Rechazado' para esta operación.";
        }
        
        return true;
    }

    /**
     * Valida fecha de pago
     */
    public function validarFechaPago($fecha, $campo = 'fecha de pago', $requerido = true)
    {
        $fecha = trim($fecha);
        
        if ($fecha === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar formato básico YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return "El campo $campo debe tener el formato YYYY-MM-DD (ej: 2024-12-31).";
        }

        // Validar que sea una fecha real
        list($ano, $mes, $dia) = explode('-', $fecha);
        if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
            return "El campo $campo no es una fecha válida.";
        }

        // Validar que no sea una fecha futura
        $fechaDateTime = DateTime::createFromFormat('Y-m-d', $fecha);
        $hoy = new DateTime();
        $hoy->setTime(0, 0, 0);
        
        if ($fechaDateTime > $hoy) {
            return "El campo $campo no puede ser una fecha futura.";
        }

        // Validar que no sea demasiado antigua (ej: mayor a 10 años)
        $fechaMinima = new DateTime();
        $fechaMinima->modify('-10 years');
        
        if ($fechaDateTime < $fechaMinima) {
            return "El campo $campo no puede ser anterior a 10 años.";
        }

        return true;
    }

    /**
     * Valida fecha de pago
     */
    public function validarFecha($fecha, $campo = 'fecha de pago', $requerido = true)
    {
        $fecha = trim($fecha);
        
        if ($fecha === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar formato básico YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return "El campo $campo debe tener el formato YYYY-MM-DD (ej: 2024-12-31).";
        }

        // Validar que sea una fecha real
        list($ano, $mes, $dia) = explode('-', $fecha);
        if (!checkdate((int)$mes, (int)$dia, (int)$ano)) {
            return "El campo $campo no es una fecha válida.";
        }

        // Validar que no sea una fecha futura
        $fechaDateTime = DateTime::createFromFormat('Y-m-d', $fecha);

        // Validar que no sea demasiado antigua (ej: mayor a 10 años)
        $fechaMinima = new DateTime();
        $fechaMinima->modify('-10 years');
        
        if ($fechaDateTime < $fechaMinima) {
            return "El campo $campo no puede ser anterior a 10 años.";
        }

        return true;
    }

    /**
     * Valida referencia de pago
     */
    public function validarReferenciaPago($valor, $campo = 'referencia de pago', $requerido = true)
    {
        $valor = trim($valor);
        
        if ($valor === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar longitud
        if (mb_strlen($valor) < 5 || mb_strlen($valor) > 50) {
            return "El campo $campo debe tener entre 5 y 50 caracteres.";
        }

        // Validar formato: letras, números, guiones, espacios
        if (!preg_match('/^[a-zA-Z0-9\-\s]+$/', $valor)) {
            return "El campo $campo solo puede contener letras, números, guiones y espacios.";
        }

        return true;
    }

    /**
     * Valida detalle de pago individual
     */
    public function validarDetallePago($item, $index = 0)
    {
        if (!is_array($item)) {
            return "El item $index del detalle debe ser un array.";
        }

        // Validar campos requeridos
        $camposRequeridos = ['fk_detalle_metodo_pago', 'monto', 'referencia'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($item[$campo]) || empty($item[$campo])) {
                return "El campo '$campo' es requerido en el item $index del detalle.";
            }
        }

        // Validar ID método de pago
        $resultadoMetodo = $this->validarIdRelacional($item['fk_detalle_metodo_pago'], 'método de pago', true);
        if ($resultadoMetodo !== true) {
            return "Item $index: " . $resultadoMetodo;
        }

        // Validar monto
        if (!is_numeric($item['monto']) || $item['monto'] <= 0) {
            return "Item $index: El monto debe ser un número positivo.";
        }

        // Validar monto máximo (ajustar según necesidades)
        if ($item['monto'] > 999999.99) {
            return "Item $index: El monto no puede ser mayor a 999,999.99.";
        }

        // Validar referencia
        $resultadoReferencia = $this->validarReferenciaPago($item['referencia'], 'referencia', true);
        if ($resultadoReferencia !== true) {
            return "Item $index: " . $resultadoReferencia;
        }

        return true;
    }

    /**
     * Valida monto de pago
     */
    public function validarMontoPago($monto, $campo = 'monto de pago', $requerido = true)
    {
        $monto = trim($monto);
        
        if ($monto === '') {
            return $requerido ? "El campo $campo es obligatorio." : true;
        }

        // Validar que sea numérico
        if (!is_numeric($monto)) {
            return "El campo $campo debe ser un número válido.";
        }

        // Validar que sea positivo
        if ($monto <= 0) {
            return "El campo $campo debe ser mayor a cero.";
        }

        // Validar formato decimal (máximo 2 decimales)
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $monto)) {
            return "El campo $campo debe tener máximo 2 decimales.";
        }

        // Validar monto máximo
        if ($monto > 999999.99) {
            return "El campo $campo no puede ser mayor a 999,999.99.";
        }

        return true;
    }

    /**
     * Sanitiza una fecha (asegura formato YYYY-MM-DD)
     */
    public function sanitizarFecha($fecha)
    {
        $fecha = trim($fecha);
        
        if ($fecha === '') {
            return '';
        }

        // Intentar convertir a formato YYYY-MM-DD
        try {
            $dateTime = new DateTime($fecha);
            return $dateTime->format('Y-m-d');
        } catch (Exception $e) {
            // Si no se puede parsear, devolver la fecha original
            return $fecha;
        }
    }






    /**
     * Devuelve true si no hay errores.
     */
    public function sinErrores(): bool
    {
        return empty($this->errores);
    }

    /**
     * Devuelve el array de errores acumulados.
     */
    public function obtenerErrores(): array
    {
        return $this->errores;
    }
}