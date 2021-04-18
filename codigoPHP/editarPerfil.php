<?php
    /**
        *@author: Cristina Núñez
        *@since: 02/12/2020
    */
    session_start();
    
    if (!isset($_SESSION['usuarioDAW216MtoDepartamentosTema5'])) {//Si el usuario no se ha autentificado
        header('Location: login.php');//Redirigimos al usuario al ejercicio01.php para que se autentifique
        exit;
    }
    
    if(isset($_REQUEST['salir'])){//Si el usuario ha pulsado el boton de salir
        session_destroy();//Destruimos la sesión actual
        header('Location: login.php');//Redirigimos al usuario al login
        exit;
    }
    
    if(isset($_REQUEST['cancelar'])){//Si el usuario ha pulsado el botón de cancelar
        header('Location: programa.php');//Redirigimos al usuario a la ventana del programa
        exit;
    }
    
    //Creamos un array donde almacenaremos las direfentes traducciones de los idiomas disponibles
    $aIdiomas['es']=['saludo' => 'Bienvenido',
                     'usuario' => 'Usuario: ',
                     'descripcion' => 'Descripción: ',
                     'fecha' => 'Fecha Hora Última conexión: ',
                     'conexiones' => 'Número de conexiones: ',
                     'password' => 'Contraseña: ',
                     'cambiarPassword' => 'Cambiar Contraseña',
                     'cerrarSesion' => 'Cerrar Sesión',
                     'imagen' => 'Imagen: ',
                     'eliminarCuenta' => 'Eliminar Cuenta',
                     'aceptar' => 'Aceptar',
                     'cancelar' => 'Cancelar'];
    
    $aIdiomas['en']=['saludo' => 'Welcome',
                     'usuario' => 'User: ',
                     'descripcion' => 'Description: ',
                     'fecha' => 'Date Time Last connection: ',
                     'conexiones' => 'Number of connections: ',
                     'password' => 'Password: ',
                     'cambiarPassword' => 'Change Password',
                     'cerrarSesion' => 'logoff',
                     'imagen' => 'Image: ',
                     'eliminarCuenta' => 'Delete Account',
                     'aceptar' => 'Acept',
                     'cancelar' => 'Cancel'];
    
    require_once '../core/libreriaValidacion.php';//Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/confDBPDO.php";//Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 
    
    try{//Realizamos una conexión con la base de datos para extraer los campos necesarios
        $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

        $sql = "Select T01_DescUsuario, T01_NumConexiones from T01_Usuario where T01_CodUsuario=:CodUsuario";
        $consulta = $miDB->prepare($sql);//Preparamos la consulta
        $parametros = [":CodUsuario" => $_SESSION['usuarioDAW215MtoDepartamentosTema5']];

        $consulta->execute($parametros);//Ejecutamos la consulta
        $registro = $consulta->fetchObject();
        
        $descripcionUsuario=$registro->T01_DescUsuario;//Almacenamos la descripción del usuario que nos devuelve la consulta
        $numConexiones=$registro->T01_NumConexiones;//Almacenamos el numero de conexiones del usuario que nos devuelve la consulta

    }catch(PDOException $excepcion){
        $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
        $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

        echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
        echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
    } finally {
       unset($miDB); //cerramos la conexion con la base de datos
    }
    //declaracion de variables universales
    define("OBLIGATORIO", 1);
    define("OPCIONAL", 0);
    $entradaOK = true;


    //Declaramos variables de errores
    $errorDescripcion = "";
    $errorImagen = "";
    
    if(isset($_REQUEST['eliminarCuenta'])){//Si pulsa el botón de eliminar cuenta
        try{//Realizamos una conexión a la base de datos para eliminar el usuario
            $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

            $sql = "DELETE from T01_Usuario where T01_CodUsuario=:CodUsuario";
            $consulta = $miDB->prepare($sql);//Preparamos la consulta
            $parametros = [":CodUsuario" => $_SESSION['usuarioDAW215MtoDepartamentosTema5']];

            $consulta->execute($parametros);//Ejecutamos la consulta
            session_destroy();//Destruimos la sesión actual
            header('Location: login.php');//Redirigimos al usuario al login
            exit;
            
        }catch(PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
            $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
           unset($miDB); //cerramos la conexion con la base de datos
        }
    }
    
    if(isset($_REQUEST['aceptar'])){ //Comprobamos que el usuario haya enviado el formulario
        $errorDescripcion = validacionFormularios::comprobarAlfaNumerico($_REQUEST['Descripcion'], 255, 3, OBLIGATORIO);

        if($_FILES['imagen']['tmp_name']!=null){//Si el usuario ha introducido una imagen
            $tipo = $_FILES['imagen']['type'];//Almacenamos el tipo de la imagen
            if (($tipo == "image/gif") || ($tipo == "image/jpeg") || ($tipo == "image/jpg") || ($tipo == "image/png")){//Comprobamos que el tipo se encuentra entre las diferentes opciones
                $imagenSubida = file_get_contents($_FILES['imagen']['tmp_name']);//Almacenamos el archivo convertido en una cadena
                
            }else{
                $errorImagen="formato incorrecto";
            }
        }
        // Recorremos el array de errores
        if ($errorDescripcion != null) { // Comprobamos que el campo no esté vacio
            $entradaOK = false; // En caso de que haya algún error le asignamos a entradaOK el valor false para que vuelva a rellenar el formulario
            $_REQUEST['Descripcion']="";//Limpiamos los campos del formulario
        }
        
        if ($errorImagen != null) { // Comprobamos que el campo no esté vacio
            $entradaOK = false; // En caso de que haya algún error le asignamos a entradaOK el valor false para que vuelva a rellenar el formulario
        }
    }else{
        $entradaOK = false; // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
    }
    if($entradaOK){ // Si el usuario ha rellenado el formulario correctamente rellenamos el array aFormulario con las respuestas introducidas por el usuario
        try{//Realizamos una consulta con la base de datos para actualizar el usuario
            $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

            $sql = "Update T01_Usuario set T01_DescUsuario = :DescUsuario where T01_CodUsuario=:CodUsuario";
            $consulta = $miDB->prepare($sql);//Preparamos la consulta
            $parametros = [":DescUsuario" => $_REQUEST['Descripcion'],
                           ":CodUsuario" => $_SESSION['usuarioDAW215MtoDepartamentosTema5']];

            $consulta->execute($parametros);//Ejecutamos la consulta
            
            if($imagenSubida!=null){//Si el usuario ha introducido alguna imagen
                $sqlImagen = "Update T01_Usuario set T01_ImagenUsuario = :Imagen where T01_CodUsuario=:CodUsuario";
                $consultaImagen = $miDB->prepare($sqlImagen);//Preparamos la consulta
                $parametrosImagen = [":Imagen" => $imagenSubida,
                                     ":CodUsuario" => $_SESSION['usuarioDAW215MtoDepartamentosTema5']];

                $consultaImagen->execute($parametrosImagen);//Ejecutamos la consulta
            }
            
            header('Location: programa.php');//Redirigimos al usuario al programa
            exit;
            
        }catch(PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
            $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
           unset($miDB); //cerramos la conexion con la base de datos
        }
    }else{//Si el usuario no ha rellenado el formulario correctamente volvera a rellenarlo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../webroot/css/estilo.css" rel="stylesheet"> 
</head>
<body>
    <header>
        <div class="logo">Editar Perfil</div>
    </header>
    <main class="mainEditar">
        <div class="contenido">
            <h3 style="text-align: center;"><?php echo $aIdiomas[$_COOKIE['idioma']]['saludo']; ?></h3>
            <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioAlta" enctype="multipart/form-data">
                    
                <div>
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="CodUsuario"><?php echo $aIdiomas[$_COOKIE['idioma']]['usuario']; ?></label>
                    <input type="text" style="background-color: transparent; border: 0px;" id="CodUsuario" name="CodUsuario" value="<?php echo $_SESSION['usuarioDAW215MtoDepartamentosTema5']; ?>" readonly>

                    <br><br>
                    
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="Descripcion"><?php echo $aIdiomas[$_COOKIE['idioma']]['descripcion']; ?></label>
                    <input type="text" style="background-color: #D2D2D2" id="Descripcion" name="Descripcion" value="<?php echo(isset($_REQUEST['Descripcion']) ? $_REQUEST['Descripcion'] : $descripcionUsuario); ?>">
                    <?php
                        if ($errorDescripcion != null) { // Si hay algun mensaje de error almacenado en el array para este campo del formulario se lo mostramos al usuario por pantalla al lado del campo correspondiente
                            echo "<span style='color: red;'>".$errorDescripcion."</span>";
                        }
                    ?>
                    <br><br>
                    
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="NConexiones"><?php echo $aIdiomas[$_COOKIE['idioma']]['conexiones']; ?></label>
                    <input type="text" style="background-color: transparent; border: 0px;" id="NConexiones" name="NConexiones" value="<?php echo $numConexiones; ?>" readonly>

                    <br><br>
                    <?php
                        if($numConexiones>1){
                    ?>
                        <label style="font-weight: bold;" class="CodigoDepartamento" for="FechaHora"><?php echo $aIdiomas[$_COOKIE['idioma']]['fecha']; ?></label>
                        <input type="text" style="background-color: transparent; border: 0px;" id="FechaHora" name="FechaHora" value="<?php echo date('d/m/Y H:i:s',$_SESSION['FechaHoraUltimaConexionAnterior']);?>" readonly>

                        <br><br>
                    <?php
                        }
                    ?>
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="imagen"><?php echo $aIdiomas[$_COOKIE['idioma']]['imagen']; ?> </label>
                    <input type="file" id="imagen" name="imagen">
                    <?php
                        if ($errorImagen != null) { // Si hay algun mensaje de error almacenado en el array para este campo del formulario se lo mostramos al usuario por pantalla al lado del campo correspondiente
                            echo "<span style='color: red;'>".$errorImagen."</span>";
                        }
                    ?>
                    <br><br>
                    <a href="cambiarPassword.php"><p style="color: blue;font-family: 'News Cycle', sans-serif; font-weight: bold; font-size: 17px;"><?php echo $aIdiomas[$_COOKIE['idioma']]['cambiarPassword']; ?></p></a>
                    <br>
                    <input type="submit" style="background-color: #ff8787;" value="<?php echo $aIdiomas[$_COOKIE['idioma']]['eliminarCuenta']; ?>" name="eliminarCuenta" class="aceptar">
                    <br><br>
                </div>
                <div>
                    <input type="submit" value="<?php echo $aIdiomas[$_COOKIE['idioma']]['aceptar']; ?>" name="aceptar" class="aceptar">
                    <input type="submit" value="<?php echo $aIdiomas[$_COOKIE['idioma']]['cancelar']; ?>" name="cancelar" class="aceptar">
                </div>
            </form>
        </div>
    </main>

       <footer> 
        <table class="tablaFooter">
            <tr> 
                <td><address> <a href="../../index.html">Elena de Anton &copy; 2020/21</a> <a href="https://github.com/elenaABSauces/LoginLogoffTema5" target="_blank"><img src="webroot/media/github.png" widht="20" height="20" /></a></address></td>
                
            </tr>
        </table>
    </footer>
  
</body>
</html>
<?php
    }
?>