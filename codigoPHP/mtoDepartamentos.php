<?php
    session_start();
    
    if (!isset($_SESSION['usuarioDAW216MtoDepartamentosTema5'])) {//Si el usuario no se ha autentificado
        header('Location: login.php');//Redirigimos al usuario al login
        exit;
    }
    
    if(!isset($_SESSION['BusquedaDepartamento'])){//Si el usuario no ha realizado ninguna busqueda de ningun departamento
        $_SESSION['BusquedaDepartamento']="";//Por defecto establecemos la variable de sesión vacía para que aparezcan todos los departamentos almacenados
    }
    
    if(isset($_REQUEST['volver'])){//si pulsa el botón de volver
        header('Location: programa.php'); //Redirigimos al usuario a la página inicial de DWES
        exit();
    }
    
    if(!isset($_SESSION['PaginaActual'])){//Si no está establecida la pagina actual en la sesion
        $_SESSION['PaginaActual']=1;//Establecemos la página actual a 1
    }
    
    if(isset($_REQUEST['editar'])){//Si el usuario pulsa el botón de editar
        $_SESSION['CodDepartamento']=$_REQUEST['editar'];//Almacenamos el valor del botón, el cual contiene el valor del departamento que queremos editar, en la variable de sesion
        header('Location: editarDepartamento.php');//Redirigimos al usuario a la ventana de editar departamento
        exit;
    }
    
    if(isset($_REQUEST['consultar'])){//Si el usuario pulsa el boton de consultar
        $_SESSION['CodDepartamento']=$_REQUEST['consultar'];//Almacenamos el valor del botón, el cual contiene el valor del departamento que queremos editar, en la variable de sesion
        header('Location: mostrarDepartamento.php');//Redirigimos al usuario a la ventana de mostrar departamento
        exit;
    }
    
    if(isset($_REQUEST['borrar'])){//Si el usuario pulsa el boton de borrar
        $_SESSION['CodDepartamento']=$_REQUEST['borrar'];//Almacenamos el valor del botón, el cual contiene el valor del departamento que queremos editar, en la variable de sesion
        header('Location: bajaDepartamento.php');//Redirigimos al usuario a la ventana de baja departamento
        exit;
    }
    
    if(isset($_REQUEST['bajaLogica'])){//Si el usuario pulsa el boton de baja logica
        $_SESSION['CodDepartamento']=$_REQUEST['bajaLogica'];//Almacenamos el valor del botón, el cual contiene el valor del departamento que queremos editar, en la variable de sesion
        header('Location: bajaLogicaDepartamento.php');//Redirigimos al usuario a la ventana de baja logica de departamento
        exit;
    }
    
    if(isset($_REQUEST['rehabilitar'])){//Si el usuario pulsa el boton de rehabilitar
        $_SESSION['CodDepartamento']=$_REQUEST['rehabilitar'];//Almacenamos el valor del botón, el cual contiene el valor del departamento que queremos editar, en la variable de sesion
        header('Location: rehabilitarDepartamento.php');//Redirigimos al usuario a la ventana de rehabilitar departamento
        exit;
    }
    
    require_once "../core/libreriaValidacion.php";//Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/confDBPDO.php";//Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 
    require_once "../config/confPaginacion.php";//Incluimos el archivo de configuración para poder acceder a la constante de la url del header Location  
    
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
        <div class="logo">Mantenimiento de Departamentos</div>
        <nav>
            <ul class="enlaces">
                <li><a href="exportarDepartamentos.php"><img src="../webroot/media/exportar.png" alt="EXPORTAR" width="30">EXPORTAR </a></li>
                <li><a href="importarDepartamentos.php"><img src="../webroot/media/importar.png" alt="IMPORTAR" width="30">IMPORTAR </a></li>
                <li><a href="altaDepartamento.php"><img src="../webroot/media/add.png" alt="AÑADIR" width="30">AÑADIR </a></li>
            </ul>
        </nav> 
    </header>
    <main class="mainIndex">
        <div class="contenidoIndex">
            <?php
            /**
                *@author: Cristina Núñez
                *@since: 14/11/2020
            */
        
                //declaracion de variables universales
                define("OBLIGATORIO", 1);
                define("OPCIONAL", 0);
                $entradaOK = true;

                $error = null;//Inicializamos a null la variable donde almacenaremos los errores del campo
                
                if (isset($_REQUEST['avanzarPagina'])) {//Si pulsa el botón de avanzar pagina
                    $_SESSION['PaginaActual'] = $_REQUEST['avanzarPagina'];//el numero de la pagina es igual al valor de avanzarPagina
                } else if(isset($_REQUEST['retrocederPagina'])){//Si pulsa el botón de retroceder pagina
                    $_SESSION['PaginaActual'] = $_REQUEST['retrocederPagina'];//el numero de la pagina es igual al valor de retrocederPagina
                }else if(isset($_REQUEST['paginaInicial'])){//Si pulsa el botón de pagina inicial
                    $_SESSION['PaginaActual'] = $_REQUEST['paginaInicial'];//el numero de la pagina es igual al valor de paginaInicial
                }else if(isset($_REQUEST['paginaFinal'])){//Si pulsa el botón de pagina final
                    $_SESSION['PaginaActual'] = $_REQUEST['paginaFinal'];//el numero de la pagina es igual al valor de paginaFinal
                }
             
                if(isset($_REQUEST['buscar'])){// Si el usuario ha pulsado el botón de buscar
                    $error = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 1, OPCIONAL);//Comprobamos que la descripción sea alfanumerico
                    
                    if($error!=null){//Si hay errores
                        $entradaOK = false;
                        $_REQUEST['DescDepartamento'] = "";//Limpiamos el campo del formulario
                    }
                }
                
                if(isset($_REQUEST['buscar'])){//Si el usuario ha pulsado el botón de buscar
                    if($entradaOK){//Si no hay errores
                        $_SESSION['BusquedaDepartamento']=$_REQUEST['DescDepartamento'];//Almacenamos en la variable de sesion el valor de la busqueda del usuario
                        $_SESSION['PaginaActual']=1;//Asignamos la pagina actual a 1
                    }
                }
            ?>
            <div class="formBuscar">
                <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioBuscar">
                            <label for="DescDepartamento" class="descDepartamento">Descripción de departamento: </label>
                            <input type="text" style="background-color: #D2D2D2" id="DescDepartamento" name="DescDepartamento" value="<?php echo $_SESSION['BusquedaDepartamento'];?>" class="descDepartamento">
                            <?php echo($error!=null ? "<span style='color:red'>".$error."</span>" : null); ?>
                            <input type="submit" value="Buscar" name="buscar" class="enviar">
                </form>
            </div>
            <div class="resultadoConsulta">
            <?php
                
                if($entradaOK){//Si el usuario ha rellenado correctamente el formulario
                    
                    try {
                        $miDB = new PDO(DNS,USER,PASSWORD);//Instanciamos un objeto PDO y establecemos la conexión
                        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Configuramos las excepciones

                        $sql = 'SELECT * FROM T02_Departamento WHERE T02_DescDepartamento LIKE "%":DescDepartamento"%" LIMIT '.(($_SESSION['PaginaActual']-1)*MAXDEPARTAMENTOS).','.MAXDEPARTAMENTOS;

                        $consulta = $miDB->prepare($sql);//Preparamos la consulta
                        $parametros = [":DescDepartamento" => $_SESSION['BusquedaDepartamento']];
                        $consulta->execute($parametros);//Pasamos los parametros y ejecutamos la consulta
                        
                        $sqlPaginacion = 'SELECT count(*) FROM T02_Departamento WHERE T02_DescDepartamento LIKE "%":DescDepartamento"%"';
                        $consultaPaginacion = $miDB->prepare($sqlPaginacion);//Preparamos la consulta
                        $parametrosPaginacion = [":DescDepartamento" => $_SESSION['BusquedaDepartamento']];
                        $consultaPaginacion->execute($parametrosPaginacion);//Pasamos los parametros y ejecutamos la consulta

                        $resultado = $consultaPaginacion->fetch();//Almacenamos el resultado el primer registro de la consulta y avanzamos el puntero al registro siguiente

                        if($resultado[0]%MAXDEPARTAMENTOS==0){//Si el resto de dividir el numero de registros de nuestro departamento entre el total de registros por pagina es cero
                            $numPaginas = ($resultado[0]/MAXDEPARTAMENTOS);//El numero máximo de paginas es la división entre el numero de registros de nuestro departamento entre el total de registros por pagina
                        } else {
                            $numPaginas = floor($resultado[0]/MAXDEPARTAMENTOS)+1;//El numero máximo de paginas es la división entre el numero de registros de nuestro departamento entre el total de registros por pagina mas uno
                        }
                        
                        settype($numPaginas,"integer");//convertimos el numero de paginas totales a integer

                        ?>
                        <div class="tabla">
                            <form name="formularioBotones" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                                <table class="tablaConsultaCampos">
                                    <thead>
                                        <tr>
                                            <th class="cDepartamento">Código</th>
                                            <th class="dDepartamento">Descripción</th>
                                            <th class="dDepartamento">FechaCreación</th>
                                            <th class="fDepartamento">FechaBaja</th>
                                            <th class="vDepartamento">VolumenNegocio</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                    <?php
                            if($consulta->rowCount()>0){//Si hay algún resultado

                                $registro = $consulta->fetchObject();//Obtenemos la primera fila del resultado de la consulta y avanzamos el puntero a la siguiente fila

                                while($registro){ //Mientras haya un registro  
                    ?>
                                        <tr>
                                            <td class="campo" style="<?php echo($registro->T02_FechaBajaDepartamento ? 'color: red' : 'color: green'); ?>"><?php echo $registro->T02_CodDepartamento; ?></td>
                                            <td class="campo" style="<?php echo($registro->T02_FechaBajaDepartamento ? 'color: red' : 'color: green'); ?>"><?php echo $registro->T02_DescDepartamento; ?></td>
                                            <?php
                                                $timestampFechaCreacion=$registro->T02_FechaCreacionDepartamento;//Almacenamos el valor de la fecha de creacion del departamento
                                                settype($timestampFechaCreacion, 'integer');//Convertimos a integer el valor
                                                $fechaCreacion = new DateTime();//Creamos una fecha 
                                                $fechaCreacion->setTimestamp($timestampFechaCreacion);//Asignamos el timestamp de la fechaHora de creacion del departamento
                                            ?>
                                            <td class="campo" style="<?php echo($registro->T02_FechaBajaDepartamento ? 'color: red' : 'color: green'); ?>"><?php echo $fechaCreacion->format('d-m-Y');  ?></td>
                                            <?php
                                                $timestampFecha=$registro->T02_FechaBajaDepartamento;//Almacenamos el valor de la fecha de baja del departamento
                                                settype($timestampFecha, 'integer');//Convertimos a integer el valor
                                                $fecha = new DateTime();//Creamos una fecha 
                                                $fecha->setTimestamp($timestampFecha);//Asignamos el timestamp de la fechaHora de baja del departamento
                                            ?>
                                            <td class="campo" style="<?php echo($registro->T02_FechaBajaDepartamento ? 'color: red' : 'color: green'); ?>" class="fecha"><?php echo($registro->T02_FechaBajaDepartamento ? $fecha->format('d-m-Y') : 'null'); ?></td>
                                            <td class="campo" style="<?php echo($registro->T02_FechaBajaDepartamento ? 'color: red' : 'color: green'); ?>"><?php echo $registro->T02_VolumenNegocio; ?></td>

                                            <td class="boton"><button type="submit" name='editar' value="<?php echo $registro->T02_CodDepartamento;//Almacenamos el valor del codigo del departamento devuelto por la consulta en el valor del boton ?>" style="background-color: transparent; border: 0;" ><img src="../webroot/media/editar.png" alt="EDITAR" width="30"></button></td>       
                                            <td class="boton"><button type="submit" name='consultar' value="<?php echo $registro->T02_CodDepartamento;//Almacenamos el valor del codigo del departamento devuelto por la consulta en el valor del boton ?>" style="background-color: transparent; border: 0;"><img src="../webroot/media/ver.png" alt="CONSULTAR" width="30"></button></td>
                                            <td class="boton"><button type="submit" name='borrar' value="<?php echo $registro->T02_CodDepartamento;//Almacenamos el valor del codigo del departamento devuelto por la consulta en el valor del boton ?>" style="background-color: transparent; border: 0;"><img src="../webroot/media/borrar.png" alt="BORRAR" width="30"></button></td>
                                            <td class="boton"><button type="submit" name='bajaLogica' value="<?php echo $registro->T02_CodDepartamento;//Almacenamos el valor del codigo del departamento devuelto por la consulta en el valor del boton ?>" style="background-color: transparent; border: 0;"><img src="../webroot/media/baja.png" alt="BajaLogica" width="30"></button></td>
                                            <td class="boton"><button type="submit" name='rehabilitar' value="<?php echo $registro->T02_CodDepartamento;//Almacenamos el valor del codigo del departamento devuelto por la consulta en el valor del boton ?>" style="background-color: transparent; border: 0;"><img src="../webroot/media/rehabilitar.png" alt="Rehabilitar" width="30"></button></td>

                                        </tr> 
                            <?php 
                                    $registro = $consulta->fetchObject();//Obtenemos la siguiente fila del resultado de la consulta y avanzamos el puntero a la siguiente fila
                                }

                            ?>

                                    </tbody>
                                </table>
                            </form>
                            <div class="paginacion">
                                <form name="formularioPaginacion" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                                    <table>
                                        <br>
                                        <tr>
                                            <td class="tBoton"><button class="pagina" <?php echo ($_SESSION['PaginaActual']==1 ? "hidden" : null);?> type="submit" name="paginaInicial" value="1"><img src="../webroot/media/pagInicial.png" alt="Rehabilitar" width="30"></button></td>
                                            <td class="tBoton"><button class="pagina" <?php echo ($_SESSION['PaginaActual']==1 ? "hidden" : null);?> type="submit" name="retrocederPagina" value="<?php echo $_SESSION['PaginaActual']-1;//si pulsa el boton de retocederPagina restamos uno a la pagina actual?>"><img src="../webroot/media/pagAnterior.png" alt="Rehabilitar" width="30"></button></td>
                                            <td><?php echo ($_SESSION['PaginaActual'].' de '.$numPaginas); ?></td>
                                            <td class="tBoton"><button class="pagina" <?php echo ($_SESSION['PaginaActual']>=$numPaginas ? "hidden" : null);?> type="submit" name="avanzarPagina" value="<?php echo $_SESSION['PaginaActual']+1;//si pulsa el boton de retocederPagina sumamos uno a la pagina actual?>"><img src="../webroot/media/pagSiguiente.png" alt="Rehabilitar" width="30"></button></td>
                                            <td class="tBoton"><button class="pagina" <?php echo ($_SESSION['PaginaActual']>=$numPaginas ? "hidden" : null);?> type="submit" name="paginaFinal" value="<?php echo $numPaginas;//numero total de páginas | página final?>"><img src="../webroot/media/pagFinal.png" alt="Rehabilitar" width="30"></button></td>
                                        </tr>
                                    </table>   
                                </form>
                            </div>
                <?php 
                            }else{
                        ?>
                                <tr>
                                    <th rowspan="4" style="color:red;">No se han encontrado registros</th>
                                </tr>
                            </tbody>
                        </table>
                <?php
                    }
                ?>
                        <form  name="formularioconsulta" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                            <table class="botones">
                                <tr>
                                    <td>
                                        <button type="submit" name='volver' value="Volver" class="volver">VOLVER</button>
                                    </td>
                                </tr>
                            </table> 
                        </form>
                    </div>
                </div>
                            
                <?php       
                    }catch (PDOException $excepcion) { //Código que se ejecutará si se produce alguna excepción
                        $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
                        $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

                        echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
                        echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
                    } finally {
                        unset($miDB);
                    }
                }
            ?>
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