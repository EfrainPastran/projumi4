<?php
//listo
class Validator {
    public static function validateRegisterData(array $data): array {
        $errors = [];
        
        // Validar cédula (ejemplo para Colombia)
        if (empty($data['cedula']) || !preg_match('/^[0-9]{6,12}$/', $data['cedula'])) {
            $errors['cedula'] = 'La cédula debe tener entre 6 y 12 dígitos';
        }
        
        // Validar email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no es válido';
        }
        
        // Validar contraseña
        if (empty($data['password']) || strlen($data['password']) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
        } elseif ($data['password'] !== $data['confirm_pass']) {
            $errors['confirm_pass'] = 'Las contraseñas no coinciden';
        }
        
        // Validar nombre
        if (empty($data['nombre']) || strlen($data['nombre']) < 2) {
            $errors['nombre'] = 'El nombre es requerido';
        }
        
        // Validar fecha de nacimiento
        if (!empty($data['fecha_nacimiento'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_nacimiento']);
            if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_nacimiento']) {
                $errors['fecha_nacimiento'] = 'La fecha de nacimiento no es válida';
            }
        }
        
        return $errors;
    }
}

?>