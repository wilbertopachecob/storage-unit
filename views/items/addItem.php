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
            $file_name = uniqid().$file_name;
            $file_size = $_FILES['img']['size'];
            $file_tmp = $_FILES['img']['tmp_name'];
            $file_type = $_FILES['img']['type'];
            $file_ext = explode('.', $_FILES['img']['name']);
            $file_ext = strtolower(end($file_ext));

            $extensions = array("jpeg", "jpg", "png");

            if (in_array($file_ext, $extensions) === false) {
                $errors[] = "extension not allowed, please choose a JPEG or PNG file.";
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
            $img = $file_name ?? null;
            //addItem($title, $description, $qty, $img)
            $user_id = $_SESSION['user_id'];
            $item = Item::addItem($title, $description, $qty, $user_id, $img);
            if ($item) {
                $messages[] = 'Item successfuly added';
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
endif;
?>
<div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-md-3 offset-md-4">
        <h1 class="text-center" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">Add Item</h1>
        <div class="dropdown-divider"></div>
          <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?script=addItem">
  <div class="form-group">
    <label for="title">Title*</label>
    <input type="text" class="form-control" name="title" id="title" />
  </div>
  <div class="form-group">
    <label for="qty">Quantity</label>
    <input type="number" class="form-control" name="qty" id="qty" value="1" />
  </div>
  <div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control" name="description" id="description" rows="3"></textarea>
  </div>
  <div class="form-group">
    <label for="description">Upload image</label>
  </div>
  <div class="input-group mb-3">
  <div class="custom-file">
    <input type="file" class="custom-file-input" name="img" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03">
    <label style="overflow: hidden;" class="custom-file-label" for="inputGroupFile03">Choose file</label>
  </div>
</div>
<div class="form-group">
    <img scr="" style="display:none" class="img-thumbnail" id="imagePreview" />
  </div>
  <button class="btn btn-primary" type="submit" name="btn_submit">
      Add
  </button>
</form>  
        </div>
    </div>
</div>
