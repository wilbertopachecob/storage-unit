<?php
function router($script)
{
    switch ($script) {
        case 'itemsList':
            include './views/items/itemsList.php';
            break;
        case 'editItem':
            include './views/items/editItem.php';
            break;
        case 'viewItem':
            include './views/items/viewItem.php';
            break;
        case 'addItem':
            include './views/items/addItem.php';
            break;
        case 'signUp':
            include './views/login/signUp.php';
            break;
        case 'signIn':
            include './views/login/signIn.php';
            break;
        case 'search':
            include './search.php';
            break;
        case 'default':
            include 'home.php';
            break;
    }
}

router($script);
