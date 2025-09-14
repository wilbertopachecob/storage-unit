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
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        $checkVars = array('itemsList', 'editItem', 'addItem');
        if(in_array($script, $checkVars)){
            if(!isloggedIn()){
                header("Location: " . $protocol . "://" . $host . "/index.php");
                exit;
            }
        }
        
        // Only redirect logged-in users away from sign-in/sign-up pages if they're not submitting forms
        $checkVars = array('signUp', 'signIn');
        if(in_array($script, $checkVars)){
            // Don't redirect if we're processing a login/signup form
            if(!isset($_POST['btn_submit'])) {
                if(isloggedIn()){ 
                    header("Location: " . $protocol . "://" . $host . "/index.php?script=itemsList");
                    exit;
                }
            }
        }
    }
}

// Only call accesingFiles() if we're not in a direct file access context
// This prevents issues when including guards.php in files like analytics.php
if (basename($_SERVER['PHP_SELF']) !== 'analytics.php') {
    accesingFiles();
}

