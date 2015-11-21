<?php

    session_start();

    if (!isset($_SESSION['usuario_usuario'])) {
        header("Location: login.php");
    } else {

        $base = "mysql:host=twittercards.cowtpdj4ubzv.us-east-1.rds.amazonaws.com;dbname=esencialfront";

        try { $conn = new PDO($base, 'Paco', 'GrupoSalinas34'); } catch (PDOException $e) { }

        $guardar_usuario = $conn->prepare("INSERT INTO vendedor_mesa (usuario_usuario, usuario_password, usuario_tipo, email_contacto, token_desbloqueo, iv_desbloqueo) VALUES (:usuario_usuario, :usuario_password, :usuario_tipo, :email_contacto, :token_desbloqueo, :iv_desbloqueo);");
        $guardar_usuario->bindParam(":usuario_usuario", $usuario_usuario);
        $guardar_usuario->bindParam(":usuario_password", $usuario_password);
        $guardar_usuario->bindParam(":usuario_tipo", $usuario_tipo);
        $guardar_usuario->bindParam(":email_contacto", $email_contacto);
        $guardar_usuario->bindParam(":token_desbloqueo", $token_desbloqueo);
        $guardar_usuario->bindParam(":iv_desbloqueo", $iv_desbloqueo);

        if (isset($_POST['crear'])) {
            $usuario_usuario = $_POST['nombre_usuario'];
            $usuario_tipo = $_POST['tipo_usuario'];
            $email_contacto = $_POST['email_usuario'];
            $usuario_password = $_POST['contrasena_usuario'];

            $token_desbloqueo = RandomString();

            $iv_desbloqueo = RandomString();

            $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $token_desbloqueo, $usuario_password, MCRYPT_MODE_CBC, $iv_desbloqueo);

            $ciphertext = $iv_desbloqueo . $ciphertext;
            
            $usuario_password = base64_encode($ciphertext);

            $guardar_usuario->execute();

        }

    }

    function RandomString() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_¿?¡!@#$%&/()=`^+*[]{}¨´;:|ªº';
        $randstring = '';
        for ($i = 0; $i < 32; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
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

    <!-- DataTables CSS -->
    <link href="bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">

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

    <div id="wrapper">

        <?php include_once('nav.php'); ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Nuevo Usuario</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4">
                    <?php if ($guardar_usuario->rowCount() > 0): ?>
                    <div class="alert alert-success text-center">
                        El usuario <b><?php echo $usuario_usuario; ?></b> fue creado.
                    </div>
                    <?php endif ?>
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                                <form action="" method="POST" autocomplete="off" role="form">
                                    <div class="form-group">
                                        <label><span class="text-danger">*</span> Nombre de Usuario </label>
                                        <input class="form-control" type="text" name="nombre_usuario" required />
                                    </div>
                                    <div class="form-group">
                                        <label><span class="text-danger">*</span> Email </label>
                                        <input class="form-control" type="email" name="email_usuario" required />
                                    </div>
                                    <div class="form-group">
                                        <label><span class="text-danger">*</span> Contraseña </label>
                                        <input class="form-control" type="password" name="contrasena_usuario" required />
                                    </div>
                                    <div class="form-group">
                                        <label><span class="text-danger" required>*</span> Tipo de Usuario </label>
                                        <select name="tipo_usuario" class="form-control">
                                            <option value=""> --- Selecciona una opción --- </option>
                                            <option value="Administrador">Administrador</option>
                                            <option value="Vendedor">Vendedor</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-block" name="crear">Crear Usuario</button>
                                </form>              
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
                responsive: true
        });
    });
    </script>

</body>

</html>
