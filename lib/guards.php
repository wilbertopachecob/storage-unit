<?php
function isloggedIn(): bool{
    if(!isset($_SESSION)){
        session_start();
    }
    if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
        return true;
    }
    return false;
}

function accesingFiles(): void{
    if(isset($_GET['script'])){
        $script = $_GET['script'];
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        $checkVars = array('itemsList', 'editItem', 'addItem');
        if(in_array($script, $checkVars)){
            if(!isloggedIn()){
                header("Location: http://" . $host . "/index.php");
                exit;
            }
        }
        $checkVars = array('signUp', 'signIn');
        if(in_array($script, $checkVars)){
            if(isloggedIn()){ 
                header("Location: http://" . $host . "/index.php?script=itemsList");
                exit;
            }
        }
    }
}

accesingFiles();

