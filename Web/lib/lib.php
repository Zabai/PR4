<?php
class View{
    public static function start($title)
    {
        $html = <<<HEADER
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8"> 
                <title>$title</title>
                <link rel="stylesheet" href="../estilos.css">
                <script src="../javascript/jquery-3.2.0.js"></script>
            </head>
            
            <body>
                <header>
                    <h1><img src="../images/logo.jpg" alt="Logo"> Distribuciones Latosas</h1>
                </header>
HEADER;
        User::session_start();
        echo $html;
    }

    public static function navigation(){
        echo "<nav><ul>";

        $files = ((User::getLoggedUser()['tipo'] == 2) ? array("index", "table", "contact") : array("index", "contact"));
        $navItems['index'] = "Inicio";
        $navItems['table'] = "Productos";
        $navItems['contact'] = "Contacto";

        foreach ($files as $item) {
            if (basename($_SERVER['PHP_SELF'], ".php") == $item)
                echo "<li><a href=../main/$item.php class=active>$navItems[$item]</a></li>";
            else
                echo "<li><a href=../main/$item.php>$navItems[$item]</a></li>";
        }

        if (User::getLoggedUser())
            echo "<li style='float: right'><a href='../account/account.php'>Mi cuenta</a></li>
                <li style='float: right'><a href=../account/action.php?op=logout>Salir</a></li>";
        else
            echo "<li style='float: right'><a href='../account/login.php'>Identificarse</a></li>";

        echo "</ul></nav>";
    }

    public static function productTable($name)
    {
        $db = new DB();
        $res = $db->execute_query("SELECT * FROM bebidas WHERE marca='$name';");

        $res->setFetchMode(PDO::FETCH_NAMED);

        foreach ($res as $bebida) {
            foreach ($bebida as $value) {
                $info[] = $value;
            }
        }

        echo <<<CONTENT
        <div class="panel producto">
            <h3>$info[1]</h3>
            <img src="../images/products/$info[1].jpg" alt="Lata">
            <table class="tablaVertical">
                <tr>
                    <th>Nombre:</th>
                    <td>$info[1]</td>
                </tr>
                <tr>
                    <th>Fabricante:</th>
                    <td>Laterio S.A</td>
                </tr>
                <tr>
                    <th>Fabricado en:</th>
                    <td>Fuerteventura</td>
                </tr>
                <tr>
                    <th>Tipo:</th>
                    <td>Lata</td>
                </tr>
                <tr>
                    <th>Cantidad:</th>
                    <td>330ml</td>
                </tr>
                <tr>
                    <th>Disponibilidad:</th>
                    <td>$info[2]</td>
                </tr>
                <tr>
                    <th>Ingredientes:</th>
                    <td>
                        <p>Agua carbonatada</p>
                        <p>Azúcar</p>
                        <p>Colorante E-150d</p>
                        <p>Acidulante E-338</p>
                        <p>Aromas naturales</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="clearfix"></div>
CONTENT;
    }

    public static function end(){
        echo <<<CLOSE
        <footer>
            <p>Distribuciones latosas&copy;</p>
        </footer>
    </body>
</html>
CLOSE;
    }
}

class DB{
    private static $connection=null;

    public function __construct(){
        $this->connect();
    }

    private function connect(){
        if(self::$connection === null) {
            self::$connection = new PDO("sqlite:../datos.db");
            self::$connection->exec("PRAGMA foreign_keys = ON;");
            self::$connection->exec("PRAGMA encoding='UTF-8';");
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public function execute_query($query, $parameters = null)
    {
        try{
            $result = self::$connection->prepare("$query");
            $result->execute($parameters);
            return $result;
        }catch (PDOException $e){
            echo '<h1>Error en la base de datos: ' . $e->getMessage() . '</h1>';
        }
        return "";
    }
}

class User{
    public static function session_start(){
        if(session_status () === PHP_SESSION_NONE){
            session_start();
        }
    }

    public static function getLoggedUser(){ //Devuelve un array con los datos del cuenta o false
        self::session_start();
        if(!isset($_SESSION['user'])) return false;
        return $_SESSION['user'];
    }

    public static function login($user, $password)
    {
        self::session_start();

        $db = new DB();
        $instance = $db->execute_query("SELECT * FROM usuarios WHERE usuario=? and clave=?", array($user, md5($password)));
        $instance->setFetchMode(PDO::FETCH_NAMED);

        $res = $instance->fetchAll();
        if (count($res) == 1) {
            $_SESSION['user'] = $res[0];
            return true;
        }
        return false;
    }

    public static function logout(){
        self::session_start();
        unset($_SESSION['user']);
    }
}
