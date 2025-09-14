<?php
if (isset($_POST['btn_submit'])):
//addItem($title, $description, $qty, $img)
    $errors = [];
    if ($_POST['title'] != ''):
        $title = $_POST['title'];
        $description = (isset($_POST['description']) && $_POST['description'] != '') ? $_POST['description'] : null;
        $qty = $_POST['qty'] ?? 1;
        if ($_FILES['img']['size'] != 0) {
            $file_name = $_FILES['img']['name'];
            //Avoiding finle name collision with uniqid()
            $file_name = uniqid() . $file_name;
            $file_size = $_FILES['img']['size'];
            $file_tmp = $_FILES['img']['tmp_name'];
            $file_type = $_FILES['img']['type'];
            $file_ext = explode('.', $_FILES['img']['name']);
            $file_ext = strtolower(end($file_ext));

            $extensions = array("jpeg", "jpg", "png");

            if (in_array($file_ext, $extensions) === false) {
                $errors[] = "Extension not allowed, please choose a JPEG or PNG file.";
            }

            // if ($file_size > 2097152) {
            //     $errors[] = 'File size must be excately 2 MB';
            // }

            if (empty($errors) == true) {
                if (!move_uploaded_file($file_tmp, "uploads/" . $file_name)) {
                    $errors[] = 'There was a problem uploading the file';
                }
            }
        }
        //Then I can insert into the database
        if (empty($errors)) {
            // If the item already has an image and the user is not 
            // uploading a new one then $img = image value on DB else
            // $img equal to new value or null
            if(isset($_POST['imagen']) && $_FILES['img']['size'] == 0){
                $img = $_POST['imagen'];     
            }
            else{
                $img = $file_name ?? null;
            }            
            //editItem($id, $title, $description, $qty, $img):bool
            $id = $_GET['id'];
            $user_id = $_SESSION['user_id'];
            $controller = new \StorageUnit\Controllers\ItemController;
            $item = new \StorageUnit\Models\Item($title, $description, $qty, $user_id, $img);
            $item->setDb(new \StorageUnit\Database\Connection);          
            $item = $controller->editItem($item, $id);
            if ($item) {
                $messages[] = 'Item successfuly edited';
            }

        } else {
            $error[] = 'Title is mandatory';
        }

    endif;

endif;
?>
<?php
if (isset($messages) && count($messages) > 0):
?>
<div class="alert alert-success" role="alert">
    <?=$messages[0]?>
</div>
<?php
endif
?>

<?php
//Showing errors
if (isset($errors) && count($errors) > 0):
    foreach ($errors as $error):
?>
<div class="alert alert-danger" role="alert">
    <?=$error?>
</div>
<?php
endforeach;
endif
?>

<div class="container mt-3 mb-3">
    <div class="row">
        <?php
//Searching for the item
if (isset($_GET['id'])):
    $item_id = $_GET['id'];
    //echo $_GET['id'];
    $controller = new \StorageUnit\Controllers\ItemController;
    $conn = new \StorageUnit\Database\Connection;
    $item = $controller->getItemById($item_id, $conn);
    //echo var_dump($item);
    ?>
        <div class="col-md-3 offset-md-4">
        <h1 class="text-center" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">Edit Item</h1>
        <div class="dropdown-divider"></div>
            <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item[0]['id']?>">
                <?php
    if (isset($item[0]['img'])):
        //echo gettype($item[0]['img']);
    ?>
    <div style="position: relative;" class="image-del-container">
                <i class="fas fa-times-circle float-right delete_img" title="Delete image"></i>
                <img src="/uploads/<?=$item[0]['img']?>" class="img-fluid edit_img" alt="<?=$item[0]['title']?>" />
    </div>
                <?php
endif;
?>
                <div class="form-group">
                    <label for="description">Upload image</label>
                </div>
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <!-- Checking if the item has an image to update the DB-->
                        <?php 
                        if(isset($item[0]['img'])):
                        ?>
                        <input type="hidden" name="imagen" value="<?=$item[0]['img']?>" />
                        <?php endif; ?>
                        <input type="file" class="custom-file-input" name="img" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03">
                        <label style="overflow: hidden;" class="custom-file-label" for="inputGroupFile03">Choose file</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title">Title*</label>
                    <input type="text" class="form-control" name="title" id="title" value="<?=$item[0]['title']?>" />
                </div>
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input type="number" class="form-control" name="qty" id="qty" value="<?=$item[0]['qty']?>" />
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3"><?=$item[0]['description']?></textarea>
                </div>
                <button class="btn btn-danger" type="submit" name="btn_delete">
                <i class="fas fa-trash-alt"></i>
                Delete
                </button>
                <button class="btn btn-primary" type="submit" name="btn_submit">
                <i class="fas fa-edit"></i>    
                Edit
                </button>
            </form>

        </div>

        <?php
else:
?>
        <div class="col-sm-12">
            <div class="jumbotron">
                <h1 class="display-4">Oooops. Something when wrong.</h1>
                <p class="lead">No item was selected.</p>
                <hr class="my-4">
            </div>
        </div>
        <?php
endif;
?>
    </div>
</div>