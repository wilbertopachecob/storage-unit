<?php
function isloggedIn(): bool{
    if(isset($_SESSION['user_id'])){
        return true;
    }
    return false;
}

function accesingFiles(): void{
    if(isset($_GET['script'])){
        $script = $_GET['script'];
        $checkVars = array('itemsList', 'editItem', 'addItem');
        if(in_array($script, $checkVars)){
            if(!isloggedIn()){
                header("Location: http://" . $_SERVER['HTTP_HOST'] . "/storageUnit");
                exit;
            }
        }
        $checkVars = array('signUp', 'signIn');
        if(in_array($script, $checkVars)){
            if(isloggedIn()){ 
                header("Location: http://" . $_SERVER['HTTP_HOST'] . "/storageUnit");
                exit;
            }
        }
    }
}

accesingFiles();

