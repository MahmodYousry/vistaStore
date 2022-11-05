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
	      								echo "<a href='brand.php?do=Edit&catid=" . $brand['id'] . "' class='btn btn-xs btn-primary'><i class='fa fa-edit'></i> Edit</a>";
	      								echo "<a href='brand.php?do=Delete&catid=" . $brand['id'] . "' class='confirm btn btn-xs btn-danger'><i class='fa fa-close'></i> Delete</a>";
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

			<h1 class="text-center">Add New Category</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST">
					<!-- Start Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="name" class="form-control" autocomplete="off" required="required" placeholder="Name Of The Category" />
						</div>
					</div>
					<!-- END Name Field -->
					<!-- Start Description Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="description" class="form-control" placeholder="Describ The Category" />
						</div>
					</div>
					<!-- END Description Field -->
					<!-- Start Ordering Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Ordering</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" />
						</div>
					</div>
					<!-- END Ordering Field -->
					<!-- Start Catogry Type Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Parent?</label>
						<div class="col-sm-10 col-md-6">
							<select name="parent">
								<option value="0">None</option>
								<?php 
									$allCats = getAllFrom("*", "categories", "where parent = 0", "", "ID", "ASC");
									foreach ($allCats as $cat) {
										echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
									}

								?>
							</select>
						</div>
					</div>
					<!-- END Catogry Type Field -->
					<!-- Start Visibility Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Visible</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="vis-yes" type="radio" name="visibility" value="0" checked />
								<label for="vis-yes">Yes</label>
							</div>
							<div>
								<input id="vis-no" type="radio" name="visibility" value="1" />
								<label for="vis-no">No</label>
							</div>
						</div>
					</div>
					<!-- END Visibility Field -->
					<!-- Start Commenting Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Allow Commenting</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="com-yes" type="radio" name="commenting" value="0" checked />
								<label for="com-yes">Yes</label>
							</div>
							<div>
								<input id="com-no" type="radio" name="commenting" value="1" />
								<label for="com-no">No</label>
							</div>
						</div>
					</div>
					<!-- END Commenting Field -->
					<!-- Start Ads Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Allow Ads</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="Ads-yes" type="radio" name="ads" value="0" checked />
								<label for="Ads-yes">Yes</label>
							</div>
							<div>
								<input id="Ads-no" type="radio" name="ads" value="1" />
								<label for="Ads-no">No</label>
							</div>
						</div>
					</div>
					<!-- END Ads Field -->
					<!-- Start submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="submit" value="Add Category" class="btn btn-primary btn-md" />
							<a class="btn btn-primary btn-md" href="categories.php">back <i class='fa fa-chevron-right'></i></a>
						</div>
					</div>
					<!-- END submit Field -->
				</form>
			</div>

	  		<?php

	    } elseif ($do == 'Insert') {

			// Insert Members Page

      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	      		echo '<h1 class="text-center">Insert Category</h1>';
	      		echo '<div class="container">';

      			// Get Variables From The Form

      			$name 		= $_POST['name'];
      			$desc 		= $_POST['description'];
      			$parent 	= $_POST['parent'];
      			$order 		= $_POST['ordering'];
      			$visible 	= $_POST['visibility'];
      			$comment 	= $_POST['commenting'];
      			$ads 		= $_POST['ads'];

  				// Check If Category Is Exist In Database

  				$check = checkItem("Name", "categories", $name);

  				if ($check == 1) {

  					$theMsg = "<div class='alert alert-danger'>Sorry This Category Is Exist</div>";

  					redirectHome($theMsg, 'back');

  				} else {

	      			// Insert Category info To Database

      				$stmt = $con->prepare("INSERT INTO 
						      					categories(Name, Description, parent, Ordering, Visibility, Allow_Comment, Allow_Ads) 
					VALUES(:zname, :zdesc, :zparent, :zorder, :zvisible, :zcomment, :zads)");
      				$stmt->execute(array(

      					'zname' 	=> $name,
      					'zdesc' 	=> $desc,
      					'zparent' 	=> $parent,
      					'zorder' 	=> $order,
      					'zvisible' 	=> $visible,
      					'zcomment' 	=> $comment,
      					'zads' 		=> $ads

      					));

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
			$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

			// Select All Data Depend on This ID
			$stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");
			
			// Excute Query
			$stmt->execute(array($catid));

			// Fetch The Data
			$cat = $stmt->fetch();

			// The Row Count
			$count = $stmt->rowCount();

			// If There\s Such ID Show The Form
			if($count > 0) { ?>

				<h1 class="text-center">Edit Category</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST">
						<input type="hidden" name="catid" value="<?php echo $catid ?>" />
						<!-- Start Name Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="name" class="form-control" required="required" placeholder="Name Of The Category" Value="<?php echo $cat['Name'] ?>" />
							</div>
						</div>
						<!-- END Name Field -->
						<!-- Start Description Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="description" class="form-control" placeholder="Describ The Category" Value="<?php echo $cat['Description'] ?>" />
							</div>
						</div>
						<!-- END Description Field -->
						<!-- Start Ordering Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Ordering</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" Value="<?php echo $cat['Ordering'] ?>" />
							</div>
						</div>
						<!-- END Ordering Field -->
						<!-- Start Catogry Type Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Parent?</label>
							<div class="col-sm-10 col-md-6">
								<select name="parent">
									<option value="0">None</option>
									<?php 
										$allCats = getAllFrom("*", "categories", "where parent = 0", "", "ID", "ASC");
										foreach ($allCats as $c) {
											echo "<option value='" . $c['ID'] . "'";
											if ($cat['parent'] == $c['ID']) { echo 'selected'; }
											echo ">" . $c['Name'] . "</option>";
										}

									?>
								</select>
							</div>
						</div>
						<!-- END Catogry Type Field -->
						<!-- Start Visibility Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Visible</label>
							<div class="col-sm-10 col-md-6">
								<div>
									<input id="vis-yes" type="radio" name="visibility" value="0" <?php if ($cat['Visibility'] == 0) { echo 'checked'; } ?> />
									<label for="vis-yes">Yes</label>
								</div>
								<div>
									<input id="vis-no" type="radio" name="visibility" value="1" <?php if ($cat['Visibility'] == 1) { echo 'checked'; } ?> />
									<label for="vis-no">No</label>
								</div>
							</div>
						</div>
						<!-- END Visibility Field -->
						<!-- Start Commenting Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Allow Commenting</label>
							<div class="col-sm-10 col-md-6">
								<div>
									<input id="com-yes" type="radio" name="commenting" value="0" <?php if ($cat['Allow_Comment'] == 0) { echo 'checked'; } ?> />
									<label for="com-yes">Yes</label>
								</div>
								<div>
									<input id="com-no" type="radio" name="commenting" value="1" <?php if ($cat['Allow_Comment'] == 1) { echo 'checked'; } ?> />
									<label for="com-no">No</label>
								</div>
							</div>
						</div>
						<!-- END Commenting Field -->
						<!-- Start Ads Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Allow Ads</label>
							<div class="col-sm-10 col-md-6">
								<div>
									<input id="Ads-yes" type="radio" name="ads" value="0" <?php if ($cat['Allow_Ads'] == 0) { echo 'checked'; } ?> />
									<label for="Ads-yes">Yes</label>
								</div>
								<div>
									<input id="Ads-no" type="radio" name="ads" value="1" <?php if ($cat['Allow_Ads'] == 1) { echo 'checked'; } ?> />
									<label for="Ads-no">No</label>
								</div>
							</div>
						</div>
						<!-- END Ads Field -->
						<!-- Start submit Field -->
						<div class="form-group form-group-lg">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Save" class="btn btn-primary btn-md" />
								<a class="btn btn-primary btn-md" href="categories.php">back <i class="fa fa-chevron-right fa-xs"></i></a>
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

	    	echo '<h1 class="text-center">Update Category</h1>';
      		echo '<div class="container">';

      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      			// Get Variables From The Form

      			$id 		= $_POST['catid'];
      			$name 		= $_POST['name'];
      			$desc 		= $_POST['description'];
      			$order 		= $_POST['ordering'];
      			$parent 		= $_POST['parent'];

      			$visible 	= $_POST['visibility'];
      			$comment 	= $_POST['commenting'];
      			$ads 		= $_POST['ads'];

      				// Update Database With This Info

	      			$stmt = $con->prepare("UPDATE 
						      				categories 
						      			SET Name = ?,
						      			 	Description = ?,
						      			  	Ordering = ?,
						      			  	parent = ?,
						      			  	Visibility = ?,
						      			  	Allow_Comment = ?,
						      			  	Allow_Ads = ? 
						      			WHERE 
						      				ID = ?");

	      			$stmt->execute(array($name, $desc, $order,$parent, $visible, $comment, $ads, $id));

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

				$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

				// Select All Data Depend on This ID

				$check = checkItem('ID', 'categories', $catid);

				// If There's Such ID Show The Form

				if($check > 0) {

					$stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");

					$stmt->bindParam(":zid", $catid);

					$stmt->execute();

					$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Deleted</div>';

					redirectHome($theMsg, 'back');

				} else {

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