<div class="views" role="toolbar" aria-label="View options">
    <p> Views: 
        <button type="button" class="btn btn-link p-0" aria-label="Switch to grid view" title="Grid view">
            <i class="fas fa-th-large" aria-hidden="true"></i>
        </button> | 
        <button type="button" class="btn btn-link p-0" aria-label="Switch to list view" title="List view">
            <i class="fas fa-list-ul" aria-hidden="true"></i>
        </button>
    </p>
    <div class="dropdown-divider"></div>
</div>
<main class="container pt-3 pb-3 cards" role="main" aria-labelledby="items-heading">
    <h1 id="items-heading" class="text-center mt-5 mb-5">
        Storage Unit List
    </h1>

    <?php
$controller = new \StorageUnit\Controllers\ItemController;
$conn = new \StorageUnit\Database\Connection;       
$items = $controller->getAllItems($_SESSION['user_id'], $conn);
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
//$arr3 = array_chunk($items, 3);
//echo var_dump($arr3);
//Showing the cards
//for ($j = 0; $j < count($arr3); $j++):
?>
    <div class="cards-container" role="grid" aria-label="Storage items">
        <?php
foreach ($items as $item):
    if (strlen($item['description']) > 100):
        //substr ( string $string , int $start [, int $length ] ) : string
        $description = substr($item['description'], 0, 100) . ' ...';
    else:
        $description = $item['description'];
    endif;
    ?>
    <div class="card-item" role="gridcell">
            <article class="card h-100">
            <?php 
$item['img'] = $item['img'] ?? 'image-not-found.png';
?>
            <img src="/uploads/<?=$item['img']?>" class="card-img-top" 
                 alt="<?=htmlspecialchars($item['title'])?> - Storage item image"
                 loading="lazy">
            <div class="card-body">
                <h2 class="card-title h5">
                    <?=htmlspecialchars($item['title'])?>
                </h2>
                <p class="card-text qty">
                    <small class="text-muted">Number of items: </small>
                    <span class="badge badge-primary" aria-label="<?=$item['qty']?> items">
                        <?=$item['qty']?>
                    </span>
                </p>
                <p class="card-text description">
                    <?=htmlspecialchars($description)?>
                </p>
                <p class="card-text">
                    <small class="text-muted">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        Last updated: <?=date('M j, Y g:i A', strtotime($item['updated_at']))?>
                    </small>
                </p>
            </div>
            <footer class="card-footer">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <a class="btn btn-primary btn-view btn-block" 
                           href="<?=$_SERVER['PHP_SELF']?>?script=viewItem&id=<?=$item['id']?>"
                           aria-label="View details for <?=htmlspecialchars($item['title'])?>">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            View
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item['id']?>" 
                           class="btn btn-success btn-edit btn-block"
                           aria-label="Edit <?=htmlspecialchars($item['title'])?>">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </footer>
        </article>
</div>
        <?php endforeach; ?>
    </div>
</main>
</div>