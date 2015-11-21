<?php

    session_start();

    if ($_GET['logout']) {
        session_destroy();
        header("Location: login.php");
    }

    $base = "mysql:host=twittercards.cowtpdj4ubzv.us-east-1.rds.amazonaws.com;dbname=esencialfront";

    try { $conn = new PDO($base, 'Paco', 'GrupoSalinas34'); } catch (PDOException $e) { }

    $buscar_usuario = $conn->prepare("SELECT * FROM vendedor_mesa WHERE usuario_usuario = :usuario_usuario;");
    $buscar_usuario->bindParam(":usuario_usuario", $usuario_usuario);

    if (isset($_POST['login'])) {

        $usuario_usuario = $_POST['usuario'];

        $buscar_usuario->execute();

        $datos_usuario = $buscar_usuario->fetchObject();

        $key_usuario = $datos_usuario->token_desbloqueo;

        $password = $_POST['contrasena'];

        $iv_usuario = $datos_usuario->iv_desbloqueo;

        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key_usuario, $password, MCRYPT_MODE_CBC, $iv_usuario);

        $ciphertext = $iv_usuario . $ciphertext;
        
        $password_usuario = base64_encode($ciphertext);

        if($password_usuario === $datos_usuario->usuario_password) {
            $_SESSION['usuario_tipo'] = $datos_usuario->usuario_tipo;
            $_SESSION['usuario_usuario'] = $datos_usuario->usuario_usuario;
            $_SESSION['email_contacto'] = $datos_usuario->email_contacto;
            header("Location: index.php");
        } else {
            $error = 1;
        }

    }

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Esencial - Mesa de Regalos</title>

    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Iniciar Sesión</h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($error == 1): ?>
                        <div class="alert alert-danger text-center">
                            El usuario o la contraseña no coinciden.
                        </div>
                        <?php endif ?>
                        <form role="form" method="POST" action="" autocomplete="off">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Usuario" name="usuario" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Contraseña" name="contrasena" type="password" value="">
                                </div>
                                <button type="submit" class="btn btn-lg btn-success btn-block" name="login">Entrar</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

</body>

</html>
