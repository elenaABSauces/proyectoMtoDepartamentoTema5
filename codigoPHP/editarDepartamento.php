<?php
    /**
        *@author: Cristina Núñez
        *@since: 09/12/2020
    */

session_start();

    if (!isset($_SESSION['usuarioDAW216MtoDepartamentosTema5'])) {//Si el usuario no se ha autentificado
        header('Location: login.php');//Redirigimos al usuario al ejercicio01.php para que se autentifique
        exit;
    }

    if(isset($_REQUEST['cancelar'])){//Si el usuario pulsa el botón de cancelar
        header('Location: mtoDepartamentos.php');//Enviamos al usuario al inicio del mtoDepartamentos
    }
    
    require_once '../core/libreriaValidacion.php';
    require_once "../config/confDBPDO.php";//Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 
    try {//Realizamos una conexion a la base de datos para sacar los diferentes campos necesarios
        
        $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

        $sqlCampos = "Select T02_DescDepartamento , T02_FechaBajaDepartamento , T02_VolumenNegocio  from T02_Departamento where T02_CodDepartamento=:CodDepartamento";
        $consultaCampos = $miDB->prepare($sqlCampos);//Preparamos la consulta
        $parametrosCampos = [":CodDepartamento" => $_SESSION['CodDepartamento']];

        $consultaCampos->execute($parametrosCampos);//Pasamos los parámetros a la consulta
        $resultadoCampos = $consultaCampos->fetchObject();//Obtenemos el primer registro de la consulta y avanzamos el puntero al siguiente

        $descripcionDepartamento=$resultadoCampos->T02_DescDepartamento;//Almacenamos el valor de la descripción del departamento de la conculta en la variable descripcionDepartamento
        $fechaBaja=$resultadoCampos->T02_FechaBajaDepartamento;//Almacenamos el valor de la fecha de baja del departamento de la conculta en la variable fechaBaja
        if($fechaBaja==null){//Si el campo está vacío
            $fechaBaja='null';//Le asignamos a la fecha de baja el valor null
        }
        $volumenNegocio=$resultadoCampos->T02_VolumenNegocio;//Almacenamos el valor del volumen de negocio del departamento de la conculta en la variable volumenNegocio

    }catch (PDOException $excepcion){
        $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
        $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

        echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
        echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
    } finally {
        unset($miDB);
    }
    //declaracion de variables universales
    define("OBLIGATORIO", 1);
    define("OPCIONAL", 0);
    $entradaOK = true;


    //Declaramos el array de errores y lo inicializamos a null
    $aErrores = ['DescDepartamento' => null,
                 'VolumenNegocio' => null];

    //Declaramos el array del formulario y lo inicializamos a null
    $aFormulario = ['DescDepartamento' => null,
                    'VolumenNegocio' => null];

    if(isset($_REQUEST['aceptar'])){ //Comprobamos que el usuario haya enviado el formulario
        $aErrores['DescDepartamento'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OBLIGATORIO);//Comprobamos que la descripción del departamento sea alfanumérico
        $aErrores['VolumenNegocio'] = validacionFormularios::comprobarFloat($_REQUEST['VolumenNegocio'], PHP_FLOAT_MAX, PHP_FLOAT_MIN, OBLIGATORIO);//Comprobamos que el volumen de negocio sea float

        // Recorremos el array de errores
        foreach ($aErrores as $campo => $error){
            if ($error != null) { // Comprobamos que el campo no esté vacio
                $entradaOK = false; // En caso de que haya algún error le asignamos a entradaOK el valor false para que vuelva a rellenar el formulario      
                $_REQUEST[$campo] = "";
            }
        }
    }else{
        $entradaOK = false; // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
    }
    if($entradaOK){ // Si el usuario ha rellenado el formulario correctamente actualizamos los datos del departamento
        try {
            $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones
;
            $sql = <<<EOD
               UPDATE T02_Departamento SET 
               T02_DescDepartamento=:DescDepartamento,
               T02_VolumenNegocio=:VolumenNegocio 
               WHERE T02_CodDepartamento=:CodDepartamento;
EOD;
            $consulta = $miDB->prepare($sql);//Preparamos la consulta
            $parametros = [ ":DescDepartamento" => $_REQUEST['DescDepartamento'],
                            ":VolumenNegocio" => $_REQUEST['VolumenNegocio'],
                            ":CodDepartamento" => $_REQUEST['CodDepartamento']];

            $consulta->execute($parametros);//Pasamos los parámetros a la consulta
            
            header('Location: mtoDepartamentos.php');//Redirigimos al usuario a la página inicial del mtoDepartamentos
        }catch (PDOException $excepcion){
            $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
            $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

            echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
            echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
        } finally {
            unset($miDB);
        }
    }else{ // Si el usuario no ha rellenado el formulario correctamente volvera a rellenarlo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mto Departamentos</title>
    <link href="../webroot/css/style.css" rel="stylesheet"> 
</head>
<body>
    <header>
        <div class="logo">Mantenimiento de Departamentos - Editar Departamento</div>
    </header>
    <main class="mainEditar">
        <div class="contenido">
                <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioEditar">
                    <div>
                        <label style="font-weight: bold;" class="CodigoDepartamento" for="CodDepartamento">Código de departamento: </label>
                        <input type="text" id="nombre" style="border: 0" name="CodDepartamento" value="<?php echo $_SESSION['CodDepartamento']; ?>"readonly>
                        <br><br>

                        <label style="font-weight: bold;" class="DescripcionDepartamento" for="DescDepartamento">Descripción de departamento: </label>
                        <input type="text" id="DescDepartamento" style="background-color: #D2D2D2" name="DescDepartamento" value="<?php echo(isset($_REQUEST['aceptar']) ? ($aErrores['DescDepartamento']!=null ? $descripcionDepartamento: $_REQUEST['DescDepartamento']) : $descripcionDepartamento);?>">
                        <?php echo($aErrores['DescDepartamento']!=null ? "<span style='color:red'>".$aErrores['DescDepartamento']."</span>" : null); ?>
                        <br><br>
                        
                        <label style="font-weight: bold;" class="Fecha" for="Fecha">Fecha: </label>
                        <input type="text" id="Fecha" style="border: 0" name="Fecha" value="<?php echo $fechaBaja ?>"readonly>
                        <br><br>

                        <label style="font-weight: bold;" class="Volumen" for="VolumenNegocio">Volumen de negocio: </label>
                        <input type="text" id="VolumenNegocio" style="background-color: #D2D2D2" name="VolumenNegocio" value="<?php echo(isset($_REQUEST['aceptar']) ? ($aErrores['VolumenNegocio']!=null ? $volumenNegocio : $_REQUEST['VolumenNegocio']) : $volumenNegocio);?>">
                        <?php echo($aErrores['VolumenNegocio']!=null ? "<span style='color:red'>".$aErrores['VolumenNegocio']."</span>" : null); ?>
                        <br><br>
                    </div>
                    <div>
                        <input type="submit" style="background-color: #a3f27b;" value="Aceptar" name="aceptar" class="aceptar">
                        <input type="submit" style="background-color: #f27b7b;" value="Cancelar" name="cancelar" class="cancelar">
                    </div>
                </form>
            
        </div>
    </main>
</body>
</html>
<?php
    }
?>