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
        case 'profile':
            $controller = new \StorageUnit\Controllers\ProfileController();
            if (isset($_GET['action']) && $_GET['action'] === 'updateStorageUnit') {
                $controller->updateStorageUnit();
            } elseif (isset($_GET['action']) && $_GET['action'] === 'getStorageUnit') {
                $controller->getStorageUnit();
            } elseif (isset($_GET['action']) && $_GET['action'] === 'uploadProfilePicture') {
                $controller->uploadProfilePicture();
            } elseif (isset($_GET['action']) && $_GET['action'] === 'deleteProfilePicture') {
                $controller->deleteProfilePicture();
            } else {
                $controller->index();
            }
            break;
        case 'export':
            include __DIR__ . '/../resources/views/export/export.php';
            break;
        case 'default':
            include __DIR__ . '/../app/home.php';
            break;
    }
}

router($script);
