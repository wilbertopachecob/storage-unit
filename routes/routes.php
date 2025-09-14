<?php
function router($script)
{
    switch ($script) {
        case 'itemsList':
            include __DIR__ . '/../resources/views/items/itemsList.php';
            break;
        case 'editItem':
            include __DIR__ . '/../resources/views/items/editItem.php';
            break;
        case 'viewItem':
            include __DIR__ . '/../resources/views/items/viewItem.php';
            break;
        case 'addItem':
            include __DIR__ . '/../resources/views/items/addItem.php';
            break;
        case 'signUp':
            include __DIR__ . '/../resources/views/login/signUp.php';
            break;
        case 'signIn':
            include __DIR__ . '/../resources/views/login/signIn.php';
            break;
        case 'search':
            include __DIR__ . '/../app/search.php';
            break;
        case 'default':
            include __DIR__ . '/../app/home.php';
            break;
    }
}

router($script);
