<?php

    session_start();

    if (!isset($_SESSION['usuario_usuario'])) {
        header("Location: login.php");
    } else {

        $base = "mysql:host=twittercards.cowtpdj4ubzv.us-east-1.rds.amazonaws.com;dbname=esencialfront";

        try { $conn = new PDO($base, 'Paco', 'GrupoSalinas34'); } catch (PDOException $e) { }

        $evento = $conn->prepare("SELECT * FROM certificado WHERE evento_id = :evento_id");
        $evento->bindParam(":evento_id", $evento_consulta);

        if ($_SESSION['usuario_tipo'] === 'Administrador') {
            $consultar_evento = $conn->prepare("SELECT * FROM evento_tienda WHERE (evento_id = :evento_id OR evento_nombre = :evento_nombre);");
            $consultar_evento->bindParam(":evento_id", $evento_id);
            $consultar_evento->bindParam(":evento_nombre", $evento_nombre);
        } else {
            $consultar_evento = $conn->prepare("SELECT * FROM evento_tienda WHERE (evento_id = :evento_id OR evento_nombre = :evento_nombre) AND evento_vendedor = :evento_vendedor;");
            $consultar_evento->bindParam(":evento_id", $evento_id);
            $consultar_evento->bindParam(":evento_nombre", $evento_nombre);
            $consultar_evento->bindParam(":evento_vendedor", $evento_vendedor);
        }

        if ($_GET['id_evento']) {
            $evento_id = $_GET['id_evento'];
            $evento_nombre = $_GET['id_evento'];
            $evento_vendedor = $_SESSION['usuario_usuario'];

            $consultar_evento->execute();

            $data_consulta = $consultar_evento->fetchObject();

            $evento_consulta = $data_consulta->evento_id;

            $evento->execute();
        }

        if (isset($_POST['consultar'])) {
            $evento_id = $_POST['evento'];
            $evento_nombre = $_POST['evento'];
            $evento_vendedor = $_SESSION['usuario_usuario'];

            $consultar_evento->execute();

            $data_consulta = $consultar_evento->fetchObject();

            $evento_consulta = $data_consulta->evento_id;

            $evento->execute();
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
                    <h1 class="page-header">Consultar Mesa de Regalos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-4 col-lg-offset-4">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form action="" method="POST" autocomplete="off" role="form">
                                <div class="form-group">
                                    <label><span class="text-danger">*</span> Nombre de Evento / Número de Evento </label>
                                    <input class="form-control" type="text" name="evento" required />
                                </div>
                                <button type="submit" class="btn btn-success btn-block" name="consultar">Consultar</button>
                            </form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-4 .col-lg-offset-4 -->

                <?php if (isset($_POST['consultar']) || isset($_GET['id_evento'])): ?>
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Detalle de puntos
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="center">Invitado</th>
                                            <th class="center">Email</th>
                                            <th class="center">Mensaje</th>
                                            <th class="center">Fecha</th>
                                            <th class="center">Puntos Regalados</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($evento as $data_evento): ?>
                                        <tr class="odd gradeX">
                                            <td class="center"><?php echo utf8_encode($data_evento['cert_nombre']) ?></td>
                                            <td class="center"><?php echo $data_evento['cert_email'] ?></td>
                                            <td class="center"><?php echo utf8_encode($data_evento['cert_mensaje']) ?></td>
                                            <td class="center"><?php echo $data_evento['cert_fecha'] ?></td>
                                            <td class="center"><?php echo $data_evento['cert_puntos'] ?></td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
                <?php endif ?>
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
