<?php
session_start();

// Limpia las variables de sesión
$_SESSION = array();

// Si se usa una cookie para la sesión, la elimina
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Eliminar cookies específicas que mencionas
setcookie("empleado_seleccionado", "", time() - 3600, "/");
setcookie("categoria", "", time() - 3600, "/");

// Destruye la sesión
session_destroy();

// Redirige al inicio
header("Location: ../index.php");
exit();
?>
