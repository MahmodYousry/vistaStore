<?php

	/*
	=================================================
	== Brands Page
	== Mange All Brands Add - Edit - Delete
	=================================================
	*/

	ob_start(); // OutPut Buffering Start
	session_start();
	$pageTitle = 'Admin | Brands';
    if (isset($_SESSION['Username'])) {

			$pageName = 'brand';

      include 'init.php';
      $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

	    if ($do == 'Manage') {

	    	$sort = 'ASC';
	    	$sort_array = array('ASC', 'DESC');
	    	if (isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {
	    		$sort = $_GET['sort'];
	    	}

	      	$stmt2 = $con->prepare("SELECT * FROM brand ORDER BY id $sort");
	      	$stmt2->execute();
	      	$brands = $stmt2->fetchAll();

	      	?>

	      	<h1 class="text-center">Manage Brands</h1>
			  
	      	<div class="container categories">
			  	<div class="members-options">
			  		<a class="btn btn-primary" href="brand.php?do=Add"/><i class="fa fa-plus"></i> Add New Brand</a>
						<a class="btn btn-primary btn-md" href="dashboard.php">back <i class='fa fa-chevron-right'></i></a>
					</div>
			  	
	      		<div class="panel panel-default">
	      			<div class="panel-heading">
		      			<i class="fa fa-edit"></i> Manage Brands
		      			<div class="option pull-right">
		      				<i class="fa fa-sort"></i> Ordering: [
		      				<a class="<?php if ($sort == 'ASC') { echo 'active'; } ?>" href="?sort=ASC" />Asc</a>
		      				<a class="<?php if ($sort == 'DESC') { echo 'active'; } ?>" href="?sort=DESC" />Desc</a> ]
		      			</div>
	      			</div>
	      			<div class="panel-body">
	      				<?php 
	      					foreach ($brands as $brand) {
	      						echo "<div class='cat'>";
	      							echo "<div class='hidden-buttons'>";
	      								echo "<a href='brand.php?do=Edit&brand_id=" . $brand['id'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i> Edit</a>";
	      								echo "<a href='brand.php?do=Delete&brand_id=" . $brand['id'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i> Delete</a>";
	      							echo "</div>";
		      						echo "<h3>" . $brand['name'] . "</h3>";
	      						echo "</div>";
										echo "<hr>";
									}
	      				?>
	      			</div>
	      		</div>
	      		
	      	</div>

	      	<?php

	  	} elseif ($do == 'Add') { ?>

			<h1 class="text-center">Add New Brand</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST">
					<!-- Start Brand Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Brand Name</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="name" class="form-control" autocomplete="off" required="required" placeholder="Name Of The Category" />
						</div>
					</div>
					<!-- END Brand Name Field -->
					<!-- Start submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="submit" value="Add Category" class="btn btn-primary btn-md" />
							<a class="btn btn-primary btn-md" href="<?php echo $pageName; ?>.php">back <i class='fa fa-chevron-right'></i></a>
						</div>
					</div>
					<!-- END submit Field -->
				</form>
			</div>

	  		<?php

	    } elseif ($do == 'Insert') {

				// Insert Members Page

      	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

					echo '<h1 class="text-center">Insert Brand</h1>';
					echo '<div class="container">';

						// Get Variables From The Form
						$name = $_POST['name'];

						// Check If Category Is Exist In Database
						$check = checkItem("name", "brand", $name);
						if ($check == 1) { // if this name exist already do not insert

							$theMsg = "<div class='alert alert-danger'>Sorry This Category Is Exist</div>";
							redirectHome($theMsg, 'back');

						} else { // if this name is new then insert it into the db

							// Insert info To Database
							$stmt = $con->prepare("INSERT INTO brand(name) VALUES(:zname)");
							$stmt->execute(array( 'zname' => $name ));

							// Echo Success Message
							$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Inserted</div>';
							redirectHome($theMsg, 'back');

						}
      			
      	} else {

						echo '<div class="container">';
							$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';
							redirectHome($theMsg, 'back');
						echo '</div>';

      	}

      		echo '</div>';


	    } elseif ($do == 'Edit') {

				// Check If Get Request catid Is Numberic & Get The Integer Value of it.
				$brand_id = isset($_GET['brand_id']) && is_numeric($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;
				// Select All Data Depend on This ID
				$stmt = $con->prepare("SELECT * FROM brand WHERE id = ?");
				// Excute Query
				$stmt->execute(array($brand_id));
				// Fetch The Data
				$cat = $stmt->fetch();
				// The Row Count
				$count = $stmt->rowCount();

				// If There\s Such ID Show The Form
				if($count > 0) { ?>

					<h1 class="text-center">Edit Brand</h1>
					<div class="container">
						<form class="form-horizontal" action="?do=Update" method="POST">
							<input type="hidden" name="brand_id" value="<?php echo $brand_id ?>" />
							<!-- Start Brand Name Field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Brand Name</label>
								<div class="col-sm-10 col-md-6">
									<input type="text" name="name" class="form-control" required="required" Value="<?php echo $cat['name'] ?>" />
								</div>
							</div>
							<!-- END Brand Name Field -->						
							<!-- Start submit Field -->
							<div class="form-group form-group-lg">
								<div class="col-sm-offset-2 col-sm-10">
									<input type="submit" value="Save" class="btn btn-primary btn-md" />
									<a class="btn btn-primary btn-md" href="<?php echo $pageName; ?>.php">back <i class="fa fa-chevron-right fa-xs"></i></a>
								</div>
							</div>
							<!-- END submit Field -->
						</form>
					</div>

      <?php

      		// If Theres No such ID show Error Message
      		} else {

      			echo '<div class="container">';
      			$theMsg = '<div class="alert alert-danger">There\'s no such id</div>';
      			redirectHome($theMsg);
      			echo '</div>';

      		}

	    } elseif ($do == 'Update') {

	    		echo '<h1 class="text-center">Update Brand</h1>';
      		echo '<div class="container">';

						if ($_SERVER['REQUEST_METHOD'] == 'POST') {

							// Get Variables From The Form
							$brand_id = $_POST['brand_id'];
							$name 		= $_POST['name'];

							// Update Database With This Info
							$stmt = $con->prepare("UPDATE brand SET `name` = ? WHERE id = ?");
							$stmt->execute([$name, $brand_id]);

							// Echo Success Message
							$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
							redirectHome($theMsg, 'back');

						} else {

							$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';

							redirectHome($theMsg);

						}

      		echo '</div>';


	    } elseif ($do == 'Delete') {

	    	echo '<h1 class="text-center">Delete Category</h1>';
      	echo '<div class="container">';

					// Check If Get Request catid Is Numberic & Get The Integer Value of it.
					$brand_id = isset($_GET['brand_id']) && is_numeric($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;

					// Select All Data Depend on This ID
					$check = checkItem('id', 'brand', $brand_id);

					// If There's Such ID Show The Form

				if($check > 0) { // if there is data with this id delete it

					$stmt = $con->prepare("DELETE FROM brand WHERE id = :zid");
					$stmt->bindParam(":zid", $brand_id);
					$stmt->execute();

					$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Deleted</div>';
					redirectHome($theMsg, 'back');

				} else { // if there is no data found with this id echo this msg

					$theMsg = '<div class="alert alert-danger">This ID Is Not Exist</div>';
					redirectHome($theMsg);

				}

			echo '</div>';

		}

		include $tpl . 'footer.php';

	} else {

  		header('Location: index.php');

  		exit();

  }

	ob_end_flush();

?>