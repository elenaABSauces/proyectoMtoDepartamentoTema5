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
    
    if(isset($_REQUEST['cancelar'])){//Si el usuario pulsa el botón de cancelar
        header('Location: editarPerfil.php');//Redirigimos al usuario al editar perfil
        exit;
    }
    
    //Creamos un array para almacenar las diferentes traducciones de los idiomas disponibles
    $aIdiomas['es']=['saludo' => 'Bienvenido',
                     'password' => 'Contraseña actual: ',
                     'passwordNueva' => 'Nueva contraseña: ',
                     'passwordRepetida' => 'Repita contraseña: ',
                     'aceptar' => 'Aceptar',
                     'cancelar' => 'Cancelar'];

    $aIdiomas['en']=['saludo' => 'Welcome',
                     'password' => 'Current password: ',
                     'passwordNueva' => 'New password: ',
                     'passwordRepetida' => 'Repeat password: ',
                     'aceptar' => 'Acept',
                     'cancelar' => 'Cancel'];
    
    require_once '../core/libreriaValidacion.php';//Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/confDBPDO.php";//Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 

    //declaracion de variables universales
    define("OBLIGATORIO", 1);
    define("OPCIONAL", 0);
    $entradaOK = true;


    //Declaramos el array de errores y lo inicializamos a null
    $aErrores = ['PasswordActual' => null,
                 'PasswordNueva' => null,
                 'PasswordRepetida' => null];

    if(isset($_REQUEST['aceptar'])){ //Comprobamos que el usuario haya enviado el formulario
        $aErrores['PasswordActual'] = validacionFormularios::validarPassword($_REQUEST['PasswordActual'], 8, 3, 1, OBLIGATORIO);//Validamos la contraseña actual
        $aErrores['PasswordNueva'] = validacionFormularios::validarPassword($_REQUEST['PasswordNueva'], 8, 3, 1, OBLIGATORIO);//Validamos la nueva contraseña
        $aErrores['PasswordRepetida'] = validacionFormularios::validarPassword($_REQUEST['PasswordRepetida'], 8, 3, 1, OBLIGATORIO);//Validamos la nueva contraseña repetida
        try{
            $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

            $sqlUsuario = "Select T01_Password from T01_Usuario where T01_CodUsuario=:CodUsuario";
            $consultaUsuario = $miDB->prepare($sqlUsuario);//Preparamos la consulta
            $parametrosUsuario = [":CodUsuario" => $_SESSION['usuarioDAW215MtoDepartamentosTema5']];

            $consultaUsuario->execute($parametrosUsuario);//Pasamos los parámetros a la consulta
            $registro = $consultaUsuario->fetchObject();
            
            $passwordUsuario = $registro->T01_Password;//Almacenamos la contraseña de la consulta
            $passwordEncriptada=hash("sha256",($_SESSION['usuarioDAW215MtoDepartamentosTema5'].$_REQUEST['PasswordActual']));//Encriptamos la contraseña que ha introducido el usuario
            
            if($passwordEncriptada!=$passwordUsuario){//Si la contraseña que nos devuelve la consulta es diferente a la contraseña encriptada que ha introducido el usuario
                $aErrores['PasswordActual'] = "Contraseña incorrecta";//Almacenamos un mensaje de error en el array de errores
            }
            
            if($_REQUEST['PasswordNueva']!=$_REQUEST['PasswordRepetida']){//Si la nueva contraseña es diferente a la contraseña repetida
                $aErrores['PasswordRepetida']="Error, las contraseñas no coinciden";//Almacenamos un mensaje de error en el array de errores
            }
            
        }catch(PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
            $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
           unset($miDB); //cerramos la conexion con la base de datos
        }
        
        // Recorremos el array de errores
        foreach ($aErrores as $campo => $error){
            if ($error != null) { // Comprobamos que el campo no esté vacio
                $entradaOK = false; // En caso de que haya algún error le asignamos a entradaOK el valor false para que vuelva a rellenar el formulario
                $_REQUEST[$campo]="";//Limpiamos los campos del formulario
            }
        }
    }else{
        $entradaOK = false; // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
    }
    if($entradaOK){ // Si el usuario ha rellenado el formulario correctamente cambiamos la contraseña en la base de datos
        try{
            $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

            $sql = "Update T01_Usuario set T01_Password = :Password where T01_CodUsuario=:CodUsuario";
            $consulta = $miDB->prepare($sql);//Preparamos la consulta
            $parametros = [":Password" => hash("sha256", ($_SESSION['usuarioDAW215MtoDepartamentosTema5'].$_REQUEST['PasswordNueva'])),
                           ":CodUsuario" => $_SESSION['usuarioDAW215MtoDepartamentosTema5']];

            $consulta->execute($parametros);//Ejecutamos la consulta
            
            header('Location: editarPerfil.php');//Redirigimos al usuario a la ventana de editar perfil.
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
        <div class="logo">Cambiar Contraseña</div>
    </header>
    <main class="mainEditar">
        <div class="contenido">
            <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioAlta">
                    <h3 style="text-align: center;"><?php echo $aIdiomas[$_COOKIE['idioma']]['saludo']; ?></h3>
                <br>
                <div>
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="PasswordActual"><?php echo $aIdiomas[$_COOKIE['idioma']]['password']; ?></label>
                    <input type="password" style="background-color: #D2D2D2" id="PasswordActual" name="PasswordActual" value="<?php echo(isset($_REQUEST['PasswordActual']) ? $_REQUEST['PasswordActual'] : null); ?>">
                    <?php
                        if ($aErrores['PasswordActual'] != null) { // Si hay algun mensaje de error almacenado en el array para este campo del formulario se lo mostramos al usuario por pantalla al lado del campo correspondiente
                            echo "<span style='color: red;'>".$aErrores['PasswordActual']."</span>";
                        }
                    ?>
                    <br><br>
                    <label style="font-weight: bold;" class="CodigoDepartamento" for="PasswordNueva"><?php echo $aIdiomas[$_COOKIE['idioma']]['passwordNueva']; ?></label>
                    <input type="password" style="background-color: #D2D2D2" id="PasswordNueva" name="PasswordNueva" value="<?php echo(isset($_REQUEST['PasswordNueva']) ? $_REQUEST['PasswordNueva'] : null); ?>">
                    <?php
                        if ($aErrores['PasswordNueva'] != null) { // Si hay algun mensaje de error almacenado en el array para este campo del formulario se lo mostramos al usuario por pantalla al lado del campo correspondiente
                            echo "<span style='color: red;'>".$aErrores['PasswordNueva']."</span>";
                        }
                    ?>
                    <br><br>

                    <label style="font-weight: bold;" class="DescripcionDepartamento" for="PasswordRepetida"><?php echo $aIdiomas[$_COOKIE['idioma']]['passwordRepetida']; ?></label>
                    <input type="password" style="background-color: #D2D2D2" id="PasswordRepetida" name="PasswordRepetida" value="<?php echo(isset($_REQUEST['PasswordRepetida']) ? $_REQUEST['PasswordRepetida'] : null);?>">
                    <?php
                        if ($aErrores['PasswordRepetida'] != null) { // Si hay algun mensaje de error almacenado en el array para este campo del formulario se lo mostramos al usuario por pantalla al lado del campo correspondiente
                            echo "<span style='color: red;'>".$aErrores['PasswordRepetida']."</span>";
                        }
                    ?>
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