<?php
    require_once('DotEnv.php');
    (new \clases\DevCoder\DotEnv('../.env'))->load();
    
    if (getenv('APP_ENV') == "local") {
        $mysqli1 = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME_L'), getenv('DB_PASSWORD_L'), getenv('DB_DATABASE'), getenv('DB_PORT'));
    }
    else if (getenv('APP_ENV') == "pro") {
        $mysqli1 = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME_P'), getenv('DB_PASSWORD_P'), getenv('DB_DATABASE'));
    }
    
	if(mysqli_connect_error()) {
        echo mb_convert_encoding("Error al conectar con base de datos home unicab:", 'UTF-8'),mysqli_connect_error();
        exit();
    }
    else {
        //echo "Conexión exitosa";
    }
    
    $mysqli1->set_charset("utf8");
?>