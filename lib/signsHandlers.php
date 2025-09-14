<?php
// if (!isFileIncluded('user.php')) {
//     include 'db/user.php';
// }
include 'user.php';
$errors = [];
$URI = "Location: http://" . $_SERVER['HTTP_HOST'];
//I create this file to handle the redirections after the sings because you cant
//send headers after the code
//Handling singOut
if (isset($_GET['sign'])) {
    if ($_GET['sign'] == 'out') {
        USER::logout();
        header($URI);
        exit;
    }

//Handling signIn
    if ($_GET['sign'] == 'in') {
        if (isset($_POST['btn_submit'])) {
            $password = $_POST['password'];
            $email = $_POST['email'];
            $user = new User($email, $password, null);
            try {
                $user->login();
                header($URI."/index.php?script=itemsList");
                exit;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

//Handling signUp
    if ($_GET['sign'] == 'up') {
        if (isset($_POST['btn_submit'])) {
            $password = $_POST['password'];
            $email = $_POST['email'];
            $name = $_POST['name'];
            $user = new User($email, $password, $name);
            try {
                $user->addUser();
                header($URI);
                //If this function throw and exception the coder after
                //this is never executed
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

        }
    }

}
