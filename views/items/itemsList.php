<div class="views">
<p> Views:  <i class="fas fa-th-large" title="Grid view"></i> | <i title="List view" class="fas fa-list-ul"></i></p>
<div class="dropdown-divider"></div>
</div>
<div class="container pt-3 pb-3 cards" >
<h1 class="text-center mt-5 mb-5" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">Storage Unit List</h1>

        <?php
//$user_id = $_SESSION['user_id'];        
$items = Item::getAllItems(1);
//Creating array of array with 3 elements to show 3 cards per card-group
// $arr3 = [];
// for ($i = 0; $i < count($items); $i += 3) {
//     $aux = [];
//     for ($j = $i; $j < 3; $j++) {
//         $aux[] = $items[$j];
//     }
//     if (count($aux) != 0) {
//         $arr3[] = $aux;
//     }
// }
$arr3 = array_chunk($items, 3);
//echo var_dump($arr3);
//Showing the cards
for ($j = 0; $j < count($arr3); $j++):
?>
<div class="card-group">
<?php
$i = 0;
foreach ($arr3[$j] as $item):
    $i++;
    if (strlen($item['description']) > 100):
        //substr ( string $string , int $start [, int $length ] ) : string
        $description = substr($item['description'], 0, 100) . ' ...';
    else:
        $description = $item['description'];
    endif;
    ?>
	            <!-- Correecting the margins for the last element of 3-->
	            <div class="card <?php if ($i % 3 != 0): ?> mr-md-5 mr-lg-5 <?php endif;?>" style="position: relative; top:0;">
            <!-- <div class="view-card" style="position: absolute; display: none;">
            <h2 class="solid">View item</h2>
            </div> -->
<?php 
$item['img'] = $item['img'] ?? 'image-not-found.png';
?>
              <img src="/storageUnit/uploads/<?=$item['img']?>" class="card-img-top" alt="<?=$item['title']?>">
              <div class="card-body">
                <h5 class="card-title" style="font-family: 'Rancho', serif; font-size:2em;"><?=$item['title']?></h5>
                <p class="card-text qty"><small class="text-muted">Number of items: </small><span class="badge badge-primary"><?=$item['qty']?></span></p>
                <p class="card-text description"><?=$description?></p>
              </div>
              <div class="card-footer">
                  <div class="row">
                      <div class="col-sm-6 mb-2">
                      <a class="btn btn-primary btn-view btn-block" href="#">
                        <i class="fas fa-eye"></i>
                        View
                       </a>
                      </div>
                      <div class="col-sm-6">
                      <a
                href="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item['id']?>"
                class="btn btn-success btn-edit btn-block"
                style="opacity:1;">
            <i class="fas fa-edit"></i>
            Edit
            </a>
                      </div>
                  </div>
              </div>
            </div>
          <?php endforeach; ?>
          </div>
          <?php
        endfor;          
          ?>
    </div>
</div>
