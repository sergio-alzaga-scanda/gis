<?php
include("bd.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $pass = $_POST['pass'];

    $query = "SELECT * FROM usuarios WHERE correo = ? AND contrasena = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ss", $correo, $pass);  // "ss" = 2 strings
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            session_start();
            $_SESSION['usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
            if ($user['tipo_usuario'] > 1) {
                header("Location: ../Views/user_information.php");
            }else{
                header("Location: ../Views/menu.php");
            }
            
            exit();
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    Swal.fire({
        title: 'Error',
        text: 'Correo o contraseña incorrectos.',
        icon: 'error',
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        window.location.href = '../index.php';
    });
</script>";

        }

        $stmt->close();
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Error al preparar la consulta.',
                    icon: 'error'
                });
              </script>";
    }

    $conn->close();
}
?>
