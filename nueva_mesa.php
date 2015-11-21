<?php

    session_start();

    if (!isset($_SESSION['usuario_usuario'])) {
        header("Location: login.php");
    } else {

        require 'PHPMailer/PHPMailerAutoload.php';

        $base = "mysql:host=twittercards.cowtpdj4ubzv.us-east-1.rds.amazonaws.com;dbname=esencialfront";

        try { $conn = new PDO($base, 'Paco', 'GrupoSalinas34'); } catch (PDOException $e) { }

        $guardar_evento = $conn->prepare("INSERT INTO evento (evento_nombre, evento_fecha) VALUES (:evento_nombre, :evento_fecha);");
        $guardar_evento->bindParam(":evento_nombre", $evento_nombre);
        $guardar_evento->bindParam(":evento_fecha", $evento_fecha);

        $actualizar_evento = $conn->prepare("UPDATE evento SET evento_codigo = :evento_codigo, clave_evento_libre = :clave_evento_libre WHERE evento_id = :evento_id;");
        $actualizar_evento->bindParam(":evento_codigo", $evento_codigo);
        $actualizar_evento->bindParam(":clave_evento_libre", $clave_evento_libre);
        $actualizar_evento->bindParam(":evento_id", $evento_id);

        $guardar_evento_tienda = $conn->prepare("INSERT INTO evento_tienda (evento_id, evento_nombre, evento_fecha, evento_codigo, clave_evento_libre, evento_vendedor) VALUES (:evento_id, :evento_nombre, :evento_fecha, :evento_codigo, :clave_evento_libre, :evento_vendedor);");
        $guardar_evento_tienda->bindParam(":evento_id", $evento_id);
        $guardar_evento_tienda->bindParam(":evento_nombre", $evento_nombre);
        $guardar_evento_tienda->bindParam(":evento_fecha", $evento_fecha);
        $guardar_evento_tienda->bindParam(":evento_codigo", $evento_codigo);
        $guardar_evento_tienda->bindParam(":clave_evento_libre", $clave_evento_libre);
        $guardar_evento_tienda->bindParam(":evento_vendedor", $evento_vendedor);

        $guardar_anfitrion = $conn->prepare("INSERT INTO anfitrion (evento_id, anf_nombre, anf_email, anf_telefono) VALUES (:evento_id, :anf_nombre, :anf_email, :anf_telefono);");
        $guardar_anfitrion->bindParam(":evento_id", $evento_id);
        $guardar_anfitrion->bindParam(":anf_nombre", $anf_nombre);
        $guardar_anfitrion->bindParam(":anf_email", $anf_email);
        $guardar_anfitrion->bindParam(":anf_telefono", $anf_telefono);

        $guardar_invitado = $conn->prepare("INSERT INTO invitado (evento_id, inv_nombre, inv_email) VALUES (:evento_id, :inv_nombre, :inv_email);");
        $guardar_invitado->bindParam(":evento_id", $evento_id);
        $guardar_invitado->bindParam(":inv_nombre", $inv_nombre);
        $guardar_invitado->bindParam(":inv_email", $inv_email);

        if (isset($_POST['registra_evento'])) {
            $evento_nombre = $_POST['nombre_evento'];
            $evento_fecha = date("Y-m-d" ,strtotime($_POST['fecha_evento']));
            $evento_vendedor = $_SESSION['usuario_usuario'];

            $guardar_evento->execute();

            $evento_id = $conn->lastInsertId();

            $clave_evento_libre = GeneraCodigo($evento_id);

            $evento_codigo = sha1($clave_evento_libre);

            $guardar_evento_tienda->execute();

            $actualizar_evento->execute();

            $num_anf = 0;

            foreach ($_POST['nombre_anfitrion'] as $anf) {
                $anf_nombre = utf8_decode($anf);
                $anf_email = $_POST['email_anfitrion'][$num_anf];
                $anf_telefono = $_POST['telefono_anfitrion'][$num_anf];

                $guardar_anfitrion->execute();

                $mail = new PHPMailer;

                //$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = get_host();  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = get_user();                 // SMTP username
                $mail->Password = get_pass();                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = get_puerto();                                    // TCP port to connect to

                $mail->setFrom('esencial@esencial.com.mx', 'Esencial');
                $mail->addAddress($anf_email, $anf_nombre);     // Add a recipient

                $mail->isHTML(true);                                  // Set email format to HTML

                $mail->Subject = 'Has registrado un evento con nosotros';
                $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    </head>

                    <body leftmargin="0" topmargin="0">
                    <table width="909" height="510" border="0" cellpadding="0" cellspacing="0" bgcolor="#72665c">
                      <tr> 
                        <td width="178" align="left" valign="top"><img src="https://www.esencial.com.mx/static/front_end/images/mail/registro_evento.png" width="400" height="510" border="0" /></td>
                        <td width="731" align="left" valign="top"><table width="510" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td height="77" align="center" valign="middle"><img src="https://www.esencial.com.mx/static/front_end/images/mail/logo_mailing_n.jpg" width="288" height="51" style="border:none;" /></td>
                          </tr>
                          <tr>
                            <td height="34" align="center" valign="top">            <span style="font-family:Arial, Helvetica, sans-serif; font-size:22px; color:#ff9933;" >Gracias por crear su evento con nosotros!</span></td>
                          </tr>
                          <tr>
                            <td height="20" align="center" valign="bottom"><img src="https://www.esencial.com.mx/static/front_end/images/mail/linea_bnlanca.jpg" width="411" height="11" style="border:none;" /></td>
                          </tr>
                          <tr>
                            <td height="153" align="center" valign="top"><table width="510" border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                  <td height="43" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ffffff;"></span></td>
                                  </tr>
                                <tr>
                                  <td height="25" align="center" valign="top"><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ffffff;">código del evento: </span><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $evento_id. '</span></td>
                                </tr>
                                <tr>
                                  <td align="center" valign="top"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" ></span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;"></span></td>
                                </tr>
                                <tr>
                                  <td height="48" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" >clave del evento:</span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $clave_evento_libre. ' </span></td>
                                </tr>
                            </table></td>
                          </tr>
                          <tr>
                            <td height="82" align="center" valign="top"><img src="https://www.esencial.com.mx/static/front_end/images/mail/linea_bnlanca.jpg" width="411" height="11" style="border:none;" /></td>
                          </tr>
                          <tr>
                            <td align="left" valign="top"><table border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <td width="200" cellpadding="2" cellspacing="0" border="0" style="border-right:2px solid #ff9933; margin:0px 5px; padding:0 10px;" align="center"><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933"> mex </font> <br />
                                  <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> méxico df goldsmith número 60 colonia polanco chapultepec méxico distrito federal <br/>
                                    teléfono (55) 5282 2034 </font></td>
                                <td width="200" cellpadding="2" cellspacing="0" border="0" align="center" ><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933" > mex </font> <br />
                                  <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> méxico df Javier barros sierra 540, park plaza torre I, santa fe méxico <br/>
                                    teléfono (55) 6377 44 10 </font></td>
                                <td width="200" cellpadding="2" cellspacing="0" style="border-left:2px solid #ff9933; margin:0px 5px; padding:0 10px;" align="center"><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933"> gdl </font> <br />
                                  <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> guadalajara empresarios número 215 puerta de hierro cp 44160 guadalajara jalisco méxico <br/>
                                    teléfono (33) 3630 0806 </font></td>
                              </tr>
                            </table></td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                    Para dejar de recibir nuestros correos da click <a href="%%UNSUBSCRIBE%%">aquí</a>
                    </body>
                    </html>';

                $mail->send();

                ++$num_anf;
            }

            $num_inv = 0;

            foreach ($_POST['nombre_invitado'] as $inv) {
                $inv_nombre = utf8_decode($inv);
                $inv_email = $_POST['email_invitado'][$num_inv];

                $guardar_invitado->execute();

                $mail = new PHPMailer;

                //$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = get_host();  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = get_user();                 // SMTP username
                $mail->Password = get_pass();                           // SMTP password
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = get_puerto();                                    // TCP port to connect to

                $mail->setFrom('esencial@esencial.com.mx', 'Esencial');
                $mail->addAddress($inv_email, $inv_nombre);     // Add a recipient

                $mail->isHTML(true);                                  // Set email format to HTML

                $mail->Subject = 'Has sido invitado a un evento con nosotros';
                $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                </head>

                <body leftmargin="0" topmargin="0">
                <table width="909" height="510" border="0" cellpadding="0" cellspacing="0" bgcolor="#72665c">
                  <tr> 
                    <td width="178" align="left" valign="top"><img src="https://www.esencial.com.mx/static/front_end/images/mail/registro_evento.png" width="400" height="510" border="0" /></td>
                    <td width="731" align="left" valign="top"><table width="510" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="77" align="center" valign="middle"><img src="https://www.esencial.com.mx/static/front_end/images/mail/logo_mailing_n.jpg" width="288" height="51" style="border:none;" /></td>
                      </tr>
                      <tr>
                        <td height="34" align="center" valign="top">            <span style="font-family:Arial, Helvetica, sans-serif; font-size:22px; color:#ff9933;" >Has sido invitado a un evento con nosotros!</span></td>
                      </tr>
                      <tr>
                        <td height="20" align="center" valign="bottom"><img src="https://www.esencial.com.mx/static/front_end/images/mail/linea_bnlanca.jpg" width="411" height="11" style="border:none;" /></td>
                      </tr>
                      <tr>
                        <td height="153" align="center" valign="top"><table width="510" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                              <td height="48" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ffffff;">Nombre del evento: </span><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $evento_nombre . '</span></td>
                              </tr>
                              <tr>
                                <td align="center" valign="top"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" ></span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;"></span></td>
                              </tr>
                            <tr>
                              <td height="48" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ffffff;">Anfitrión: </span><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $anf_nombre . '</span></td>
                            </tr>
                            <tr>
                              <td align="center" valign="top"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" ></span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;"></span></td>
                            </tr>
                            <tr>
                              <td height="48" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" >Código del evento:</span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $evento_id . ' </span></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td height="82" align="center" valign="top"><img src="https://www.esencial.com.mx/static/front_end/images/mail/linea_bnlanca.jpg" width="411" height="11" style="border:none;" /></td>
                      </tr>
                      <tr>
                        <td align="left" valign="top"><table border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="200" cellpadding="2" cellspacing="0" border="0" style="border-right:2px solid #ff9933; margin:0px 5px; padding:0 10px;" align="center"><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933"> mex </font> <br />
                              <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> méxico df goldsmith número 60 colonia polanco chapultepec méxico distrito federal <br/>
                                teléfono (55) 5282 2034 </font></td>
                            <td width="200" cellpadding="2" cellspacing="0" border="0" align="center" ><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933" > mex </font> <br />
                              <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> méxico df Javier barros sierra 540, park plaza torre I, santa fe méxico <br/>
                                teléfono (55) 6377 44 10 </font></td>
                            <td width="200" cellpadding="2" cellspacing="0" style="border-left:2px solid #ff9933; margin:0px 5px; padding:0 10px;" align="center"><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933"> gdl </font> <br />
                              <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> guadalajara empresarios número 215 puerta de hierro cp 44160 guadalajara jalisco méxico <br/>
                                teléfono (33) 3630 0806 </font></td>
                          </tr>
                        </table></td>
                      </tr>
                    </table></td>
                  </tr>
                </table>
                Para dejar de recibir nuestros correos da click <a href="%%UNSUBSCRIBE%%">aquí</a>
                </body>
                </html>';

                $mail->send();

                ++$num_inv;
            }

            $mail = new PHPMailer;

            //$mail->SMTPDebug = 3;                               // Enable verbose debug output

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = get_host();  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = get_user();                 // SMTP username
            $mail->Password = get_pass();                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = get_puerto();                                    // TCP port to connect to

            $mail->setFrom('esencial@esencial.com.mx', 'Esencial');
            $mail->addAddress($_SESSION['email_contacto']);

            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = 'Has creado un evento en el sistema';
            $mail->Body    = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                </head>

                <body leftmargin="0" topmargin="0">
                <table width="909" height="510" border="0" cellpadding="0" cellspacing="0" bgcolor="#72665c">
                  <tr> 
                    <td width="178" align="left" valign="top"><img src="https://www.esencial.com.mx/static/front_end/images/mail/registro_evento.png" width="400" height="510" border="0" /></td>
                    <td width="731" align="left" valign="top"><table width="510" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="77" align="center" valign="middle"><img src="https://www.esencial.com.mx/static/front_end/images/mail/logo_mailing_n.jpg" width="288" height="51" style="border:none;" /></td>
                      </tr>
                      <tr>
                        <td height="34" align="center" valign="top">            <span style="font-family:Arial, Helvetica, sans-serif; font-size:22px; color:#ff9933;" >Has creado un evento en el sistema</span></td>
                      </tr>
                      <tr>
                        <td height="20" align="center" valign="bottom"><img src="https://www.esencial.com.mx/static/front_end/images/mail/linea_bnlanca.jpg" width="411" height="11" style="border:none;" /></td>
                      </tr>
                      <tr>
                        <td height="153" align="center" valign="top"><table width="510" border="0" cellpadding="0" cellspacing="0">
                          <tr>
                              <td height="43" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ffffff;"></span></td>
                              </tr>
                            <tr>
                              <td height="25" align="center" valign="top"><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ffffff;">código del evento: </span><span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $evento_id. '</span></td>
                            </tr>
                            <tr>
                              <td align="center" valign="top"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" ></span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;"></span></td>
                            </tr>
                            <tr>
                              <td height="48" align="center" valign="bottom"><span style="font-family:Arial, Helvetica, sans-serif;  font-size:18px; color:#ffffff;" >clave del evento:</span> <span style="font-family:Arial, Helvetica, sans-serif; font-size:18px; color:#ff9933;">' . $clave_evento_libre. ' </span></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td height="82" align="center" valign="top"><img src="https://www.esencial.com.mx/static/front_end/images/mail/linea_bnlanca.jpg" width="411" height="11" style="border:none;" /></td>
                      </tr>
                      <tr>
                        <td align="left" valign="top"><table border="0" cellpadding="0" cellspacing="0">
                          <tr>
                            <td width="200" cellpadding="2" cellspacing="0" border="0" style="border-right:2px solid #ff9933; margin:0px 5px; padding:0 10px;" align="center"><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933"> mex </font> <br />
                              <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> méxico df goldsmith número 60 colonia polanco chapultepec méxico distrito federal <br/>
                                teléfono (55) 5282 2034 </font></td>
                            <td width="200" cellpadding="2" cellspacing="0" border="0" align="center" ><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933" > mex </font> <br />
                              <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> méxico df Javier barros sierra 540, park plaza torre I, santa fe méxico <br/>
                                teléfono (55) 6377 44 10 </font></td>
                            <td width="200" cellpadding="2" cellspacing="0" style="border-left:2px solid #ff9933; margin:0px 5px; padding:0 10px;" align="center"><font face="Arial, Helvetica, sans-serif" size="7" color="#ff9933"> gdl </font> <br />
                              <font face="Arial, Helvetica, sans-serif" size="2" color="#ffffff"> guadalajara empresarios número 215 puerta de hierro cp 44160 guadalajara jalisco méxico <br/>
                                teléfono (33) 3630 0806 </font></td>
                          </tr>
                        </table></td>
                      </tr>
                    </table></td>
                  </tr>
                </table>
                Para dejar de recibir nuestros correos da click <a href="%%UNSUBSCRIBE%%">aquí</a>
                </body>
                </html>';

            $mail->send();


            $success = 1;

        }

    }

        function get_host(){

            $encrypted = file_get_contents('5261a2db0dda1');
            
            $decrypted = decrypt($encrypted, "!@#$%^&*");

            return (string) $decrypted;
        }

        function get_puerto(){

            $encrypted = file_get_contents('5261a2db0dda2');
            
            $decrypted = decrypt($encrypted, "!@#$%^&*");

            return (string) $decrypted;
        }

        function get_user(){

            $encrypted = file_get_contents('5261a2db0dda3');
            
            $decrypted = decrypt($encrypted, "!@#$%^&*");

            return (string) $decrypted;
        }

        function get_pass(){

            $encrypted = file_get_contents('5261a2db0dda4');
            
            $decrypted = decrypt($encrypted, "!@#$%^&*");

            return (string) $decrypted;
        }

        function decrypt($encrypted_string, $encryption_key) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
            return $decrypted_string;
        }

    function GeneraCodigo($val){
        
        $tres_caracteres = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
        
        $referencia = str_pad(strval($val),4,'0',STR_PAD_LEFT);
        $codigo = $referencia.$tres_caracteres;

        return $codigo;
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

    <title>Esencial - Nueva Mesa de Regalos</title>

    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="dist/css/datepicker.css" rel="stylesheet">

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
                    <h1 class="page-header">Nueva Mesa de Regalos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <?php if ($success): ?>
                        <div class="alert alert-success text-center">
                            El evento <b><?php echo $evento_nombre; ?></b> fue creado.
                        </div>
                        <?php endif ?>
                        <div class="panel-body">
                            <form action="" role="form" method="POST" autocomplete="off">
                                <div class="form-group col-lg-4">
                                    <label><span class="text-danger">*</span> Nombre del Evento </label>
                                    <input class="form-control" type="text" name="nombre_evento" />
                                </div>
                                <div class="form-group col-lg-1">
                                    <label><span class="text-danger">*</span> Fecha del Evento </label>
                                    <input class="form-control" type="text" name="fecha_evento" id="fecha_evento" />
                                </div>

                                <div class="form-group col-lg-12">
                                    <h3>Festejado(s) / Anfitrión(es)</h3>
                                </div>
                                <div id="anfitriones" class="form-group col-lg-12">
                                    <div class="form-group col-lg-4">
                                        <label><span class="text-danger">*</span> Nombre Completo </label>
                                        <input class="form-control" type="text" name="nombre_anfitrion[]" />
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label><span class="text-danger">*</span> E-mail </label>
                                        <input class="form-control" type="email" name="email_anfitrion[]" />
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>Teléfono</label>
                                        <input class="form-control" type="number" name="telefono_anfitrion[]" />
                                    </div>
                                    <div class="form-group col-lg-1">
                                        <button class="btn btn-success" id="add_anfitrion"><i class="fa fa-plus"></i></a></button>
                                    </div>
                                </div>

                                <div class="form-group col-lg-12">
                                    <h3>Invitado(s)</h3>
                                </div>
                                <div id="invitados" class="form-group col-lg-12">
                                    <div class="form-group col-lg-4">
                                        <label><span class="text-danger">*</span> Nombre Completo </label>
                                        <input class="form-control" type="text" name="nombre_invitado[]" />
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label><span class="text-danger">*</span> E-mail </label>
                                        <input class="form-control" type="email" name="email_invitado[]" />
                                    </div>
                                    <div class="form-group col-lg-1">
                                        <button class="btn btn-success" id="add_invitado"><i class="fa fa-plus"></i></a></button>
                                    </div>
                                </div>

                                <div class="form-group col-lg-4">
                                    <button class="btn btn-primary" id="registra_evento" name="registra_evento">Registrar Evento</button>
                                </div>
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

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>
    <script src="dist/js/bootstrap-datepicker.js"></script>

    <script>
        $('#fecha_evento').datepicker()
    </script>

    <script>
        $(document).ready(function() {
            var anfitriones         = $("#anfitriones");
            var invitados         = $("#invitados");
            
            $("#add_anfitrion").click(function(e){ //on add input button click
                e.preventDefault();
                $(anfitriones).append('<div class="col-lg-12"><div class="form-group col-lg-4"><label><span class="text-danger">*</span> Nombre Completo</label><input class="form-control" type="text" name="nombre_anfitrion[]" autofocus /></div><div class="form-group col-lg-4"><label><span class="text-danger">*</span> E-mail</label><input class="form-control" type="text" name="email_anfitrion[]" /></div><div class="form-group col-lg-3"><label>Teléfono</label><input class="form-control" type="text" name="telefono_anfitrion[]" /></div><button class="btn btn-danger eliminar_anf"><i class="fa fa-times fa-fw"></i></button></div>'); //add input box
            });

            $("#add_invitado").click(function(e){ //on add input button click
                e.preventDefault();
                $(invitados).append('<div class="col-lg-12"><div class="form-group col-lg-4"><label><span class="text-danger">*</span> Nombre Completo</label><input class="form-control" type="text" name="nombre_invitado[]" autofocus /></div><div class="form-group col-lg-4"><label><span class="text-danger">*</span> E-mail</label><input class="form-control" type="text" name="email_invitado[]" /></div><button class="btn btn-danger eliminar_inv"><i class="fa fa-times fa-fw"></i></button></div>'); //add input box
            });
            
            $(anfitriones).on("click",".eliminar_anf", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove();
            })

            $(invitados).on("click",".eliminar_inv", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent('div').remove();
            })
        });
    </script>

</body>

</html>
