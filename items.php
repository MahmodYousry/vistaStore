<?php
	ob_start();
	session_start();
    $pageTitle = 'Show Items';
	include 'init.php';

	// Check If Get Request item Is Numberic & Get The Integer Value of it.
	$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

	// Select All Data Depend on This ID
	$stmt = $con->prepare("	SELECT 	items.*,
									brand.name AS brand_name,
									brand.id AS brand_id_i,
									users.Username,
									type.type_name
							FROM items

							JOIN brand ON brand.id = items.brand_id
							JOIN users ON users.UserID = items.Member_ID
							JOIN type ON type.type_id = items.type_id

							WHERE
								Item_ID = ?
							AND 
								Approve = 1");
	
	// Excute Query
	$stmt->execute(array($itemid));
	$count = $stmt->rowCount();
	if ($count > 0) {

	// Fetch The Data
	$item = $stmt->fetch();

	?>
	<h1 class="text-center"><?php echo $item['item_name'] ?></h1>
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<?php
					// get the images in item_imgs table by itemid
					$stmtimg = $con->prepare("SELECT * FROM item_imgs WHERE item_ID = ?");
					$stmtimg->execute([$item['Item_ID']]);
					$itemImgs = $stmtimg->fetchAll();

					foreach ($itemImgs as $itemImg) {
						
					}

					if (empty($itemImgs)) { // if there is not img
						echo '<div class="alert alert-info" role="alert">No Images For This Item</div>';
					} else { // if there is image do the foreach and get them one by one 
						echo '<div class="imgs_cont">';
							foreach ($itemImgs as $itemImg) {
								echo '<img class="img-responsive img-thumbnail center-block prev-image" src="products/' . $itemImg["img_src"] . '" alt="" />';
							}
						echo '</div>';
					}



				?>
				
			</div>
			<div class="col-md-7 item-info">
				<h2><?php echo $item['item_name'] ?></h2>
				<p><?php echo $item['number'] ?></p>
				<ul class="list-unstyled">
					<li>
						<i class="fa fa-list-ol fa-fw"></i>
						<span>Count</span> : <?php echo $item['number'] ?>
					</li>
					<li>
						<i class="fa fa-calendar fa-fw"></i>
						<span>Added Date</span> : <?php echo $item['Last_price_date'] ?>
					</li>
					<li>
						<i class="fa fa-money fa-fw"></i>
						<span>Price</span> : جنيه <?php echo $item['price'] ?>
					</li>
					<li>
						<i class="fa fa-building fa-fw"></i>
						<span>type</span>: <?php echo $item['type_name'] ?>
					</li>
					<li>
						<i class="fa fa-tags fa-fw"></i>
						<span>Brand</span> : <a href="brand.php?pageid=<?php echo $item['brand_id_i'] ?>"><?php echo $item['brand_name'] ?></a>
					</li>
					<li>
						<i class="fa fa-user fa-fw"></i>
						<span>Added By</span> : <a href="userProfile.php?UserID=<?php echo $item['Username'] ?>"><?php echo $item['Username'] ?></a>
					</li>
					<li class="tags-items">
						<i class="fa fa-user fa-fw"></i>
						<span>Tags</span> : 
						<?php 
							$allTags = explode(",", $item['tags']);
							foreach ($allTags as $tag) {
								$tag = str_replace(' ', '', $tag);
								$lowertag = strtolower($tag);
								if (! empty($tag)) {
									echo "<a href='tags.php?name={$lowertag}'>" . $tag . '</a>';
								}
							}
						?>
					</li>
				</ul>
			</div>
			
		</div>
		<hr class="custom-hr">
		<?php if (isset($_SESSION['user'])) { ?>
		<!-- Start Add Comment -->
		<!-- <div class="row">
			<div class="col-md-offset-3">
				<div class="add-comment">
					<h3>Add Your Comment</h3>
					<form action="<?php //echo $_SERVER['PHP_SELF'] . '?itemid='. $item['Item_ID'] ?>" method="POST">
						<textarea name="comment" required></textarea>
						<input class="btn btn-primary"type="submit" value="Add Comment">
					</form>
					<?php 
						/* if ($_SERVER['REQUEST_METHOD'] == 'POST') {
							
							$comment 	= filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
							$itemid 	= $item['Item_ID'];
							$userid 	= $_SESSION['uid'];

							if (! empty($comment)) {

								$stmt = $con->prepare("INSERT INTO 
									comments(comment, status, comment_date, item_id, user_id) 
									VALUES(:zcomment, 0, NOW(), :zitemid, :zuserid)");

								$stmt->execute(array(

										'zcomment' => $comment,
										'zitemid' => $itemid,
										'zuserid' => $userid,
										'zcomment' => $comment,

									));

								if ($stmt) {

									echo '<div class="alert alert-success">Comment Added</div>';

								}

							}

						} */
					?>
				</div>
			</div>
		</div> -->
	<!-- End Add Comment -->
	<?php } else { echo '<a href="login.php">Login</a> Or <a href="login.php">Register</a> To Add Comment'; } ?>
	<hr class="custom-hr">
	<?php 
      	// Select All Users Except Admin

      	// $stmt = $con->prepare("SELECT comments.*, users.Username AS Member FROM comments
      	// 					   	INNER JOIN users ON users.UserID = comments.user_id
      	// 					   	WHERE item_id = ? AND status = 1 ORDER BY c_id DESC");
      	// $stmt->execute(array($item['Item_ID']));
      	// $comments = $stmt->fetchAll();

	      	
		?>
		<?php //foreach ($comments as $comment) { ?>
			<!-- <div class="comment-box">
				<div class="row">
					<div class="col-sm-2 text-center">
						<img class="img-responsive img-thumbnail img-circle center-block" src="noprofile_lg.png" alt="" />
						<?php //echo $comment['Member'] ?>
					</div>
					<div class="col-sm-10">
						<p class="lead"><?php //echo $comment['comment'] ?></p>
					</div>
				</div>
			</div>
			<hr class="custom-hr"> -->
		<?php	//} ?>
		
	
	</div>
<?php
	} else {
		echo '<div class="container">';
			echo '<div class="alert alert-danger">There\'s Is No Such ID Or This Item Is Waiting Approval</div>';
		echo '</div>';
	}
	include $tpl . 'footer.php';
	ob_end_flush();
?>