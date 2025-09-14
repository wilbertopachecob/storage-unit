<?php
//session_start();
//require 'lib/db/connection.php';

function search($user_id, $searchTerm, $conn): array
{
    $conexion = $conn->getConnection();
    /**
     * Doing a LIKE inside PDO is complicated when you want to allow a
     * literal % or _ character in the search string, without having it act as a
     * wildcard. More info here https://stackoverflow.com/questions/583336/how-do-i-create-a-pdo-parameterized-query-with-a-like-statement
     */
    $sql = $conexion->prepare('SELECT * FROM items WHERE user_id = ? AND title LIKE ? ORDER BY id DESC');
    $sql->execute(array(
        $user_id,
        "%$searchTerm%",
    ));
    return $sql->fetchAll();
}

$user_id = $_SESSION['user_id'];
$searchTerm = $_POST['searchTerm'];
$conn = new \StorageUnit\Database\Connection;

$items = search($user_id, $searchTerm, $conn);
if (count($items) > 0):
?>

<div class="views">
    <p> Views: <i class="fas fa-th-large" title="Grid view"></i> | <i title="List view" class="fas fa-list-ul"></i></p>
    <div class="dropdown-divider"></div>
</div>
<div class="container pt-3 pb-3 cards">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mt-5 mb-5" style="color: #111; font-family: 'Rancho', serif; font-weight: bolder;">
                Search results:
            </h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="/export/search.php?q=<?= urlencode($searchTerm) ?>" class="btn btn-success btn-lg">
                <i class="fas fa-download"></i> Export Results (CSV)
            </a>
        </div>
    </div>
    <div class="cards-container row">
        <?php
foreach ($items as $item):
    if (strlen($item['description']) > 100):
        //substr ( string $string , int $start [, int $length ] ) : string
        $description = substr($item['description'], 0, 100) . ' ...';
    else:
        $description = $item['description'];
    endif;
    ?>
	    <div class="col-md-4 col-6 col-sm-12 mb-5">
	            <div class="card h-100">
	            <!-- <div class="view-card" style="position: absolute; display: none;">
	            <h2 class="solid">View item</h2>
	            </div> -->
	            <?php
    $item['img'] = $item['img'] ?? 'image-not-found.png';
    ?>
	            <img src="/uploads/<?=$item['img']?>" class="card-img-top" alt="<?=$item['title']?>">
	            <div class="card-body">
	                <h5 class="card-title" style="font-family: 'Rancho', serif; font-size:2em;">
	                    <?=$item['title']?>
	                </h5>
	                <p class="card-text qty"><small class="text-muted">Number of items: </small><span class="badge badge-primary">
	                        <?=$item['qty']?></span></p>
	                <p class="card-text description">
	                    <?=$description?>
	                </p>
	                <p class="card-text">
	                    <small class="text-muted">
	                        <i class="fas fa-clock" aria-hidden="true"></i>
	                        Last updated: <?=date('M j, Y g:i A', strtotime($item['updated_at']))?>
	                    </small>
	                </p>
	            </div>
	            <div class="card-footer">
	                <div class="row">
	                    <div class="col-sm-6 mb-2">
                        <a class="btn btn-primary btn-view btn-block" href="<?=$_SERVER['PHP_SELF']?>?script=viewItem&id=<?=$item['id']?>">
                            <i class="fas fa-eye"></i>
                            View
                        </a>
	                    </div>
	                    <div class="col-sm-6">
	                        <a href="<?=$_SERVER['PHP_SELF']?>?script=editItem&id=<?=$item['id']?>" class="btn btn-success btn-edit btn-block"
	                            style="opacity:1;">
	                            <i class="fas fa-edit"></i>
	                            Edit
	                        </a>
	                    </div>
	                </div>
	            </div>
	        </div>
	</div>
	        <?php endforeach;?>
    </div>
</div>
</div>
<?php
else:
?>
<div class="col-sm-12">
    <div class="jumbotron">
    <h1 class="display-4">No results</h1>
    <p class="lead">This search returns no result, try it again.</p>
    </div>
</div>
<?php
endif;
?>
