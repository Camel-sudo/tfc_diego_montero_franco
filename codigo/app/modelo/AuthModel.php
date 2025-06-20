<?php
class AuthModel {
    public static function getPermisos() {
    return [
        'sin_registro' => [
            'AuthController' => ['LoginForm', 'LoginUsuario'],
            'UsuarioController' => ['RegistroForm', 'registroUsuario'],
        ],
        'base' => [
            'AuthController' => ['logout'],
            'UsuarioController' => ['EditAccountForm'],
            'AlimentoController' => ['buscarAlimentoForm','buscar','guardarSeleccion'],
            'RegistroDiarioController' => ['misDietas','ajaxMisDietas'],
            'RegistroAlimentoController' => ['modificar','eliminar'],
        ],
        'premium' => [
            'AuthController' => ['logout'],
            'UsuarioController' => ['EditAccountForm'],
            'MacrosController' => ['misDietas'],
            'AlimentoController' => ['buscarAlimentoForm','buscar','guardarSeleccion'],
        ],
        'nutricionista' => [
            'AuthController' => ['logout'],
            'UsuarioController' => ['EditAccountForm'],
            'RegistroDiarioController' => ['misDietas','ajaxMisDietas'],
            'ClienteController' => ['obtenerClientes', 'verClientes','altaCliente','clientesDisponibles','eliminarCliente','editarCliente','seleccionarPaciente','altaClienteNutricionista'],
            'AlimentoController' => ['buscarAlimentoForm','buscar','guardarSeleccion'],
            'RegistroAlimentoController' => ['modificar','eliminar'],
            'NutricionistaController' => ['misPacientes'],
        ],
        'admin' => [
            '*' => ['*'], 
        ],
    ];
}
    public static function tienePermiso($controller, $action) {
        $rol = self::rol();
        $permisos = self::getPermisos();
    
        if (!isset($permisos[$rol])) return false;
        $acceso = $permisos[$rol];
    
        return (
            isset($acceso[$controller]) &&
            (in_array($action, $acceso[$controller]) || in_array('*', $acceso[$controller]))
        ) || isset($acceso['*']); 
    }
    
    public static function usuarioLogueado() {
        return isset($_SESSION['usuario_id']);
    }

    public static function usuarioNombre() {
        return $_SESSION['usuario_nombre'] ?? 'Invitado';
    }

    public static function rol() {
        $rol= $_SESSION['usuario_rol'] ?? 'sin_registro';
        return $rol;
    }
    public static function cargarMenu() {
    
        $html = '';
    
        if (!AuthModel::usuarioLogueado()) {
            $html .= '<a href="index.php?controller=AuthController&action=LoginForm">Login</a>';
            $html .= '<a href="index.php?controller=UsuarioController&action=RegistroForm">Registrarse</a>';
        } else {
            $nombre = htmlspecialchars(AuthModel::usuarioNombre());
            $html .= '<a href="index.php?controller=AuthController&action=logout">Cerrar sesión</a>';
            switch (AuthModel::rol()) {
                case 'admin':
                    $html .= '<a href="index.php?controller=AdminController&action=panel">Panel Admin</a>';
                    break;
                case 'nutricionista':
                    $html .= '<a href="index.php?controller=NutricionistaController&action=misPacientes">Mis pacientes</a>';
                    break;
                case 'base':
                    $html .= '<a href="index.php?controller=RegistroDiarioController&action=misDietas">Mis dietas</a>';
                    break;
            }
        }
    
        return $html;
    }    
}
