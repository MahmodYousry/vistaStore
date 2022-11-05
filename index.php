<?php
	ob_start();
	session_start();

    $pageTitle = 'Homepage';
	include 'init.php';

?>

<div class="container">
	<div class="row">
		<?php 
			// get item info with no image
			$stmt = $con->prepare("SELECT * FROM items 
					JOIN brand ON brand.id = items.brand_id
					JOIN type ON type.type_id = items.type_id
					ORDER BY Item_ID DESC");
			$stmt->execute();
			$allItems = $stmt->fetchAll();

			foreach ($allItems as $item) {
				
				// get item images by item id
				$stmtimg = $con->prepare("SELECT * FROM item_imgs WHERE item_ID = ?");
				$stmtimg->execute([$item['Item_ID']]);
				$itemImgs = $stmtimg->fetch();

				if (isset($itemImgs[1])) {
					$firstImg = $itemImgs[1];
				} else {
					$firstImg = 'def.webp';
				}

				echo '<div class="col-sm-6 col-md-3">';
					echo '<div class="thumbnail item-box">';
						echo '<span class="price-tag">جنيه ' . $item['price'] . '</span>';
						echo '<a href="items.php?itemid=' . $item['Item_ID'] . '">';
							echo '<img class="img-responsive product-img" src="products/' . $firstImg . '" alt="" />';
						echo '</a>';
						echo '<div class="caption">';
							echo '<h3 class="text-center">';
								echo '<a href="items.php?itemid=' . $item['Item_ID'] . '">' . $item['item_name'] . '</a>';
							echo '</h3>';
							echo '<p class="text-center">We Have ' . $item['number'] . ' Items</p>';
							echo '<p class="text-center">' . $item['name'] . '</p>';
							echo '<p class="text-center">' . $item['type_name'] . '</p>';
							echo '<div class="date">' . $item['Last_price_date'] . '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		?>
	</div>
</div>

<?php	
	include $tpl . 'footer.php'; 
	ob_end_flush();
?>