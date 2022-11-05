<?php

	/*
	=================================================
	== type Page
  == Add | edit | Delete type
	=================================================
	*/

	ob_start(); // OutPut Buffering Start
	session_start();
	$pageTitle = 'Admin | Types';

    if (isset($_SESSION['Username'])) {
      include 'init.php';
      $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

	    if ($do == 'Manage') {

      	// Select All Users Except Admin
      	$stmt = $con->prepare("SELECT * FROM `type` ORDER BY type_id DESC");
      	$stmt->execute();
      	$types = $stmt->fetchAll();

      	if (! empty($types)) { ?>

					<h1 class="text-center">Manage Types</h1>
					<div class="container">
						<div class="members-options">
							<a class="btn btn-md btn-primary" href="type.php?do=Add"><i class="fa fa-plus"></i> New Type</a>
							<a class="btn btn-primary btn-md" href="dashboard.php">back 
								<i class="fa fa-chevron-right fa-xs"></i>
							</a>
						</div>
						<div class="table-responsive">
							<table class="main-table text-center table table-bordered">
								<tr>
									<td>type_id</td>
									<td>type name</td>
									<td>Control</td>
								</tr>

								<?php
									foreach ($types as $type) {
										echo "<tr>";
											echo "<td>" . $type['type_id'] . "</td>";
											echo "<td>" . $type['type_name'] . "</td>";
											echo "<td>";
												echo "<a href='type.php?do=Edit&type_id=" . $type['type_id'] . "' class='btn btn-success' data-toggle='tooltip' data-placement='left' title='edit status name'><i class='fa fa-edit'></i> Edit</a>";
												echo "<a href='type.php?do=Delete&type_id=" . $type['type_id'] . "' class='btn btn-danger confirm' data-toggle='tooltip' data-placement='left' title='Deletes the status from database'><i class='fa fa-close'></i> Delete</a>";
											echo "</td>";
										echo "</tr>";
									}
								?>

							</table>
						</div>
					</div>

			<?php } else {

				echo '<div class="container">';
					echo '<div class="nice-message">There\'s No Status To Show</div>';
					echo '<a href="type.php?do=Add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> New type</a>';
				echo '</div>';


			} ?>

		<?php 


	  	} elseif ($do == 'Add') { ?>

	  		<h1 class="text-center">Add New Type</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
					<!-- Start type Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Type Name</label>
						<div class="col-sm-10 col-md-8">
							<input type="text" name="name" class="form-control" 
								required="required" placeholder="Insert Type Name" />
						</div>
					</div>
          <!-- END type Name Field -->
					<!-- Start submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="submit" value="Add status" class="btn btn-primary btn-sm" />
							<a class="btn btn-primary btn-sm" href="status.php">back 
								<i class="fa fa-chevron-right fa-xs"></i>
							</a>
						</div>
					</div>
					<!-- END submit Field -->
				</form>
			</div>

		<?php 


	    } elseif ($do == 'Insert') {

      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	      		echo '<h1 class="text-center">Insert Type</h1>';
	      		echo '<div class="container">';

	      			// Get Variables From The Form
	      			$name = $_POST['name'];
	      			// Validate The Form
	      			$formErrors = array();
	      			if (empty($name)) { $formErrors[] = 'Name Can\'t be <strong>Empty</strong>'; }
	      			// Loop Into Errors Array And Echo It
	      			foreach($formErrors as $error) { echo '<div class="alert alert-danger">' . $error . '</div>'; }

	      			// Check If There's No Error Proceed The Update Operation
	      			if (empty($formErrors)):
								// Insert Userinfo To Database
								$stmt = $con->prepare("INSERT INTO type(type_name) VALUES(:zname)");
								$stmt->execute(array('zname' => $name ));
								// Echo Success Message
								$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Inserted</div>';
								redirectHome($theMsg, 'back');
		      		endif;

      		} else {

	      			echo '<div class="container">';
								$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';
								redirectHome($theMsg);
		      		echo '</div>';

      		}

      			echo '</div>';


	    } elseif ($do == 'Edit') {

			// Check If Get Request status Is Numberic & Get The Integer Value of it.
			$type_id = isset($_GET['type_id']) && is_numeric($_GET['type_id']) ? intval($_GET['type_id']) : 0;

			// Select All Data Depend on This ID
			$stmt = $con->prepare("SELECT * FROM type WHERE type_id = ?");
			$stmt->execute([$type_id]);
			$stat = $stmt->fetch();
			$count = $stmt->rowCount();

			// If There\s Such ID Show The Form
			if($count > 0) { ?>

				<h1 class="text-center">Edit Type</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST">
						<input type="hidden" name="type_id" value="<?php echo $type_id ?>" />
						<!-- Start Name Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Type Name</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="name" class="form-control" required="required" value="<?php echo $stat['type_name'] ?>" />
							</div>
						</div>
						<!-- END Name Field -->
						<!-- Start submit Field -->
						<div class="form-group form-group-lg">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Save Status" class="btn btn-primary btn-sm">
								<a class="btn btn-primary btn-sm" href="status.php">back <i class="fa fa-chevron-right fa-xs"></i></a>
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

	    	echo '<h1 class="text-center">Update status</h1>';
      		echo '<div class="container">';

      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      			// Get Variables From The Form
      			$stat_id        = $_POST['stat_id'];
      			$stat_name      = $_POST['name'];
      			// Validate The Form
      			$formErrors = array();
      			if (empty($stat_name)) { $formErrors[] = 'Name Can\'t be <strong>Empty</strong>'; }

      			// Loop Into Errors Array And Echo It
      			foreach($formErrors as $error) { echo '<div class="alert alert-danger">' . $error . '</div>'; }

      			// Check If There's No Error Proceed The Update Operation
      			if (empty($formErrors)) {

	      			// Update The Database With This Info
	      			$stmt = $con->prepare("UPDATE status SET stat_name = ? WHERE stat_id = ?");
	      			$stmt->execute([$stat_name, $stat_id]);

	       			// Echo Success Message
	      			$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
	      			redirectHome($theMsg, 'back');

      			}

      		} else {

      			$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';
      			redirectHome($theMsg);

      		}

      		echo '</div>';

	    } elseif ($do == 'Delete') {

				echo '<h1 class="text-center">Delete status</h1>';
      	echo '<div class="container">';

					// Check If Get Request status Is Numberic & Get The Integer Value of it.
					$type_id = isset($_GET['type_id']) && is_numeric($_GET['type_id']) ? intval($_GET['type_id']) : 0;

					// Select All Data Depend on This ID
					$check = checkItem('type_id', 'type', $type_id);

					// If There's Such ID Show The Form
					if ($check > 0) {
	                    // begin to Delete
						$stmt = $con->prepare("DELETE FROM type WHERE type_id = ?");
						$stmt->execute([$type_id]);
	                    // Echo msg
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