<?php

	/*
	=================================================
	== Manage Members Page
	== You Can Add | Edit | Delete Members From Here
	=================================================
	*/

	ob_start(); // OutPut Buffering Start
	session_start();
	$pageTitle = 'Admin | Members';
    
  if (isset($_SESSION['Username'])) {

		$pageName = "members";

		include 'init.php';
		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		// Start Manage Page
		if ($do == 'Manage') { // Manage Members Page
			$query = 'AND RegStatus = 1';

		if (isset($_GET['page']) && $_GET['page'] == 'Pending') {
			$query = 'AND RegStatus = 0';
		}

		// Select All Users Except Admin
		$stmt = $con->prepare("SELECT * FROM users Where GroupID != 1 $query ORDER BY UserID DESC");
		$stmt->execute();
		$rows = $stmt->fetchAll();

		if (! empty($rows)) {
		?>

			<h1 class="text-center">Manage Members</h1>
			<div class="container">
				<div class="members-options">
					<a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Member</a>
					<a class="btn btn-primary btn-md" href="dashboard.php">back <i class='fa fa-chevron-right'></i></a>
				</div>
				<div class="table-responsive">
					<table class="main-table manage-members text-center table table-bordered">
						<tr>
							<td>#ID</td>
							<td>Avatar</td>
							<td>Username</td>
							<td>Email</td>
							<td>FullName</td>
							<td>Registered Date</td>
							<td>Control</td>
						</tr>
						
						<?php
							
							foreach ($rows as $row) {
								echo "<tr id='tbody'>";
									echo "<tr>";
										echo "<td>" . $row['UserID'] . "</td>";
										echo "<td>";
										if (empty($row['avatar'])) { // if no avatar
											echo 'No Image';
										} else { // if avatar exist
											echo "<img src='uploads/avatars/" . $row['avatar'] . "' alt=''/>";
										}
										echo "</td>";
										echo "<td>" . $row['Username'] . "</td>";
										echo "<td>" . $row['Email'] . "</td>";
										echo "<td>" . $row['FullName'] . "</td>";
										echo "<td>" . $row['Date'] . "</td>";
										echo "<td>
												<a href='members.php?do=Edit&userid=" . $row['UserID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
												<a href='members.php?do=Delete&userid=" . $row['UserID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete</a>";
												if (empty($row['avatar'])) { // if no avatar
													echo "<a href='members.php?do=EditAvatar&userid=" . $row['UserID'] . "' class='btn btn-primary'><i class='fa fa-edit'></i> add avatar</a>";
												} else { // if avatar exist
													echo "<button onclick='deleteAvatar(".$row['UserID'].");' id='avatarDelete' class='btn btn-danger text-capitalize'><i class='fa fa-close'></i> delete avatar</button>";
												}

												if ($row['RegStatus'] == 0) {
													echo "<a href='members.php?do=Activate&userid=" . $row['UserID'] . "' class='btn btn-info activate'><i class='fa fa-check'></i> Activate</a>";
												}
										echo "</td>";
									echo "</tr>";
								echo "</tr>";
							}
						?>
						

					</table>
				</div>
				
			</div>

			<?php 
		} else {

			echo '<div class="container">';
				echo '<div class="nice-message">There\'s No Members To Show</div>';
				echo '<a href="members.php?do=Add" class="btn btn-primary"><i class="fa fa-plus"></i> New Member</a>';
			echo '</div>';

		}
			?>

			<?php

		} elseif ($do == 'Add') { // Add Members Page ?>

		<h1 class="text-center">Add New Member</h1>
		<div class="container">
			<form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
				<!-- Start Username Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Username</label>
					<div class="col-sm-10 col-md-8">
						<input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="Username To Login Into Shop" />
					</div>
				</div>
				<!-- END Username Field -->
				<!-- Start Password Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Password</label>
					<div class="col-sm-10 col-md-8">
						<input type="password" name="password" class="password form-control" autocomplete="new-password" required="required" placeholder="Password Must Be Hard & Complex" />
						<i class="show-pass fa fa-eye fa-2x"></i>
					</div>
				</div>
				<!-- END Password Field -->
				<!-- Start Email Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Email</label>
					<div class="col-sm-10 col-md-8">
						<input type="email" name="email" class="form-control" required="required" placeholder="Email Must Be Valid" />
					</div>
				</div>
				<!-- END Email Field -->
				<!-- Start Full Name Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Full Name</label>
					<div class="col-sm-10 col-md-8">
						<input type="text" name="full" class="form-control" required="required" placeholder="Full Name Appear In Your Profile Page" />
					</div>
				</div>
				<!-- END Full Name Field -->
				<!-- Start Avatar Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">User Avatar</label>
					<div class="col-sm-10 col-md-8">
						<input type="file" name="avatar" class="form-control"/>
					</div>
				</div>
				<!-- END Avatar Field -->
				<!-- Start submit Field -->
				<div class="form-group form-group-lg">
					<div class="col-sm-offset-2 col-sm-10">
						<input type="submit" value="Add Member" class="btn btn-primary btn-md" />
						<a class="btn btn-primary btn-md" href="members.php">back</a>
					</div>
				</div>
				<!-- END submit Field -->
			</form>
		</div>

		<?php 

		} elseif ($do == 'Insert') {

			// Insert Members Page
      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	      		echo '<h1 class="text-center">Insert Member</h1>';
	      		echo '<div class="container">';

	      		// Upload Variables
	      		$avatarName = $_FILES['avatar']['name'];
	      		$avatarSize =  $_FILES['avatar']['size'];
	      		$avatarTmp 	=  $_FILES['avatar']['tmp_name'];
	     			$avatarType =  $_FILES['avatar']['type'] . '<br>';

						// List Of Allowed File Types To Upload
						$avatarAllowedExtension = array("jpeg", "jpg", "png", "gif");

						// Get Avatar Extension
						$string = 'Osama.ahmed.sayed.ali';
						// explose shortcut for php 8 need
						$explodeThispl = explode('.', $avatarName);
						$avatarExtension = strtolower(end($explodeThispl));	      		

      			// Get Variables From The Form
      			$user 	= $_POST['username'];
      			$pass 	= $_POST['password'];
      			$email 	= $_POST['email'];
      			$name 	= $_POST['full'];
      			$hashPass = sha1($_POST['password']);

      			// Validate The Form
      			$formErrors = array();

      			if (strlen($user) < 4) {
      				$formErrors[] = '<div class="alert alert-danger">Username Can\'t Be Less Than <strong>4 Characters</strong></div>';
      			}

						if (strlen($user) > 20) { $formErrors[] = 'Username Can\'t Be More Than <strong>20 Characters</strong>'; }
      			if (empty($user)) { $formErrors[] = 'Username Can\'t Be <strong>Empty</strong>'; }
      			if (empty($pass)) { $formErrors[] = 'Password Can\'t Be <strong>Empty</strong>'; }
						if (empty($name)) { $formErrors[] = 'Full Name Can\'t Be <strong>Empty</strong>'; }
      			if (empty($email)) { $formErrors[] = 'Email Can\'t Be <strong>Empty</strong>'; }
      			if (! empty($avatarName) && ! in_array($avatarExtension, $avatarAllowedExtension)) {
	     				$formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
	     			}
	     			if ($avatarSize > 6194304) { $formErrors[] = 'Avatar Can\'t Be Larger Than <strong>6MB</strong>'; }

      			// Loop Into Errors Array And Echo It
      			foreach($formErrors as $error) { echo '<div class="alert alert-danger">' . $error . '</div>'; }

      			// Check If There's No Error Proceed The Update Operation
      			if (empty($formErrors)) {

							// if avatar was selected
							if($avatarSize == 0) { // No file was selected for upload

								// Check If User Is Exist In Database
								$check = checkItem("Username", "users", $user);

								if ($check == 1) {

									$theMsg = "<div class='alert alert-danger'>Sorry This User Is Exist</div>";
									redirectHome($theMsg, 'back');

								} else {

									// Insert Userinfo To Database
									$stmt = $con->prepare("INSERT INTO users(Username, Password, Email, FullName, RegStatus, Date) 
															VALUES(:zuser, :zpass, :zmail, :zname, 1, now()) ");
									$stmt->execute(array(
										'zuser' 	=> $user,
										'zpass' 	=> $hashPass,
										'zmail' 	=> $email,
										'zname' 	=> $name
									));

									// Echo Success Message
									$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Inserted</div>';
									redirectHome($theMsg, 'back');

								}

							} else { // if file selected do upload and save info

								$avatar = rand(0, 1000000000) . '_' . $avatarName;
								move_uploaded_file($avatarTmp, "uploads/avatars/" . $avatar);

								// Check If User Is Exist In Database
								$check = checkItem("Username", "users", $user);

								if ($check == 1) {

									$theMsg = "<div class='alert alert-danger'>Sorry This User Is Exist</div>";
									redirectHome($theMsg, 'back');

								} else {

									// Insert Userinfo To Database
									$stmt = $con->prepare("INSERT INTO users(Username, Password, Email, FullName, RegStatus, Date, avatar) 
															VALUES(:zuser, :zpass, :zmail, :zname, 1, now(), :zavatar) ");
									$stmt->execute(array(
										'zuser' 	=> $user,
										'zpass' 	=> $hashPass,
										'zmail' 	=> $email,
										'zname' 	=> $name,
										'zavatar' 	=> $avatar
									));

									// Echo Success Message
									$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Inserted</div>';
									redirectHome($theMsg, 'back');

								}

							}

      				
      			}

      		} else {

      			echo '<div class="container">';
      				$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';
      				redirectHome($theMsg);
      			echo '</div>';

      		}

      		echo '</div>';

		} elseif ($do == 'Edit') {

      // Check If Get Request useerid Is Numberic & Get The Integer Value of it.
			$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

			// Select All Data Depend on This ID
			$stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
			$stmt->execute([$userid]);
			$row = $stmt->fetch();
			$count = $stmt->rowCount();

			// If There\s Such ID Show The Form
			if($count > 0) { ?>

		   	<h1 class="text-center">Edit Member</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="userid" value="<?php echo $userid ?>" />
						<!-- Start Username Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Username</label>
							<div class="col-sm-10 col-md-8">
								<input type="text" name="username" class="form-control" value="<?php echo $row['Username'] ?>" autocomplete="off" required="required" />
							</div>
						</div>
						<!-- END Username Field -->
						<!-- Start Password Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Password</label>
							<div class="col-sm-10 col-md-8">
								<input type="hidden" name="oldpassword" value="<?php echo $row['Password'] ?>" />
								<input type="Password" name="newpassword" class="form-control" autocomplete="new-password" placeholder="Leave Blank If You Don't Want To Change" />
							</div>
						</div>
						<!-- END Password Field -->
						<!-- Start Email Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Email</label>
							<div class="col-sm-10 col-md-8">
								<input type="email" name="email" value="<?php echo $row['Email'] ?>" class="form-control" required="required" />
							</div>
						</div>
						<!-- END Email Field -->
						<!-- Start Full Name Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Full Name</label>
							<div class="col-sm-10 col-md-8">
								<input type="text" name="full" value="<?php echo $row['FullName'] ?>" class="form-control" required="required" />
							</div>
						</div>
						<!-- END Full Name Field -->
						<!-- Start Avatar Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">User Avatar</label>
							<div class="col-sm-10 col-md-8">
								<input type="file" name="avatar" class="form-control" />
							</div>
						</div>
						<!-- END Avatar Field -->
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
      		
      	} elseif($do == 'Update') { //Update Page

      		echo '<h1 class="text-center">Update Member</h1>';
      		echo '<div class="container">';

      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				
						// Upload Variables
	      		$avatarName =  $_FILES['avatar']['name'];
	      		$avatarSize =  $_FILES['avatar']['size'];
	      		$avatarTmp 	=  $_FILES['avatar']['tmp_name'];
	     			$avatarType =  $_FILES['avatar']['type'] . '<br>';

						// List Of Allowed File Types To Upload
						$avatarAllowedExtension = array("jpeg", "jpg", "png", "gif");

						// explode short cut
						$explose_thispl = explode('.', $avatarName);
						$avatarExtension = strtolower(end($explose_thispl));

      			// Get Variables From The Form
      			$id 		= $_POST['userid'];
      			$user 	= $_POST['username'];
      			$email 	= $_POST['email'];
      			$name 	= $_POST['full'];

      			// Password Trick
      			$pass = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);

      			// Validate The Form
      			$formErrors = array();

      			if (strlen($user) < 4) {
							$formErrors[] = '<div class="alert alert-danger">Username Can\'t Be Less Than <strong>4 Characters</strong></div>';
      			}

						if (strlen($user) > 20) {$formErrors[] = 'Username Can\'t Be More Than <strong>20 Characters</strong>'; }
						if (empty($user)) {$formErrors[] = 'Username Can\'t Be <strong>Empty</strong>'; }
						if (empty($name)) {$formErrors[] = 'Full Name Can\'t Be <strong>Empty</strong>'; }
						if (empty($email)) {$formErrors[] = 'Email Can\'t Be <strong>Empty</strong>';}
						if (! empty($avatarName) && ! in_array($avatarExtension, $avatarAllowedExtension)) {
							$formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
						}
						if ($avatarSize > 6194304) {$formErrors[] = 'Avatar Can\'t Be Larger Than <strong>6MB</strong>';}

      			// Loop Into Errors Array And Echo It
      			foreach($formErrors as $error) {echo '<div class="alert alert-danger">' . $error . '</div>';}

      			// Check If There's No Error Proceed The Update Operation
      			if (empty($formErrors)) {

							// check if user uploaded avatar
							if($avatarSize == 0) { // No file was selected for upload

								// Check if user is registered before
								$stmt2 = $con->prepare("SELECT * FROM users WHERE Username = ? AND UserID != ?");
								$stmt2->execute([$user, $id]);
								$count = $stmt2->rowCount();

								if ($count == 1) { // if user is exist

									$theMsg = '<div class="alert alert-danger">Sorry This User Is Exist</div>';
									redirectHome($theMsg, 'back');

								} else { // if user does not exist

									// Update The Database With This Info
									$stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ? WHERE UserID = ?");
									$stmt->execute(array($user, $email, $name, $pass, $id));

									// Echo Success Message
									$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
									redirectHome($theMsg, 'back');

								}

							} else {

								$avatar = rand(0, 1000000000) . '_' . $avatarName;
								move_uploaded_file($avatarTmp, "uploads\avatars\\" . $avatar);

								$stmt2 = $con->prepare("SELECT * FROM users WHERE Username = ? AND UserID != ?");
								$stmt2->execute([$user, $id]);
								$count = $stmt2->rowCount();

								if ($count == 1) {

									$theMsg = '<div class="alert alert-danger">Sorry This User Is Exist</div>';
									redirectHome($theMsg, 'back');

								} else {

									// get this user Avatar first // getAvatar($row, $table, $idRowName, $id);
									$avatarUR = getAvatar('avatar', 'users', 'UserID', $id);
									// prepare a good link in variable
									$avatarLink = "uploads/avatars/" . $avatarUR;
									// show the Old Avatar
									echo "<div class='alert alert-info text-capitalize'>";
										echo '<img src="'.$avatarLink.'" alt="" max-width="100%" height="150px">';
									echo " Your old avatar Deleted From the Server</div>";

									// delete the avatar from server first
									echo DeleteServFile($avatarLink); // if file exist will delete if not will return error msg

									// Update The Database With This Info
									$stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ?, avatar = ?  WHERE UserID = ?");
									$stmt->execute(array($user, $email, $name, $pass, $avatar, $id));

									// Echo Success Message
									$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' avatar Updated</div>';
									redirectHome($theMsg, 'back');

								}

							}

      			}

      		} else {

      			$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';
      			redirectHome($theMsg);

      		}

      		echo '</div>';


      	} elseif ($do == 'EditAvatar') {

					echo '<h1 class="text-center">Activate Member</h1>';
					echo '<div class="container">';
					echo '</div>';

				} elseif ($do == 'Activate') {

      		echo '<h1 class="text-center">Activate Member</h1>';
      		echo '<div class="container">';

	      		// Check If Get Request useerid Is Numberic & Get The Integer Value of it.
						$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

						// Select All Data Depend on This ID
						$check = checkItem('userid', 'users', $userid);

						// If There's Such ID Show The Form
						if($check > 0) { // if user exist do the condition

							// update the info to activate
							$stmt = $con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = ?");
							$stmt->execute(array($userid));
							// echo message for success
							$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Updated</div>';
							redirectHome($theMsg);

						} else { // if user is not exist echo msg to tell
							$theMsg = '<div class="alert alert-danger">This User Does Not Exist</div>';
							redirectHome($theMsg);
						}

					echo '</div>';

      	} elseif ($do == 'Delete') {  // Delete Members Page

					echo '<h1 class="text-center">Delete Member</h1>';
					echo '<div class="container">';

					// Check If Get Request useerid Is Numberic & Get The Integer Value of it.
					$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

					// Select All Data Depend on This ID
					$check = checkItem('userid', 'users', $userid);

					// If There's Such ID Show The Form
					if($check > 0) {

						$checkAvatar = avatarCheck('avatar', 'users', 'UserID = ', $userid);

						if ($checkAvatar > 0) { // if There is Avatar For this User

							$avatr = getUserAvatar($userid);

							echo '<div class="alert alert-success">Avatar Check Found One Avatar For This User</div>';

							if ($avatr == 0 || $avatr == NULL) {
								$avatr = '1png';
							}

							$avUrl = "uploads/avatars/" . $avatr;

							

							if (file_exists($avUrl)) { // if file exits

								echo '<div class="alert alert-primary">There is Avatar For This User</div>';

								if (unlink($avUrl)) { // Delete Avatar Photo File From the Server using the url given
									// show succes message for Avatar Deleted
									echo '<div class="alert alert-success">' . $stmt1->rowCount() . ' Avatar Deleted</div>';
									// begin to delete the user from Database
									$stmt = $con->prepare("DELETE FROM users WHERE UserID = ?");
									$stmt->execute([$userid]);
									// show succes message for User Deleted from Database
									$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' User Is Deleted from Database</div>';
									redirectHome($theMsg, 'back');
								} else {
									$theMsg1 = '<div class="alert alert-danger">Failed To Delete The Avatar or Url Not Good</div>';
									redirectHome($theMsg1, 'back');
								}
							
							} else { // if file doesnot exits
								echo '<div class="alert alert-danger">No Avatar For This User</div>';
								// begin to delete the user from Database
								$stmt = $con->prepare("DELETE FROM users WHERE UserID = ?");
								$stmt->execute([$userid]);
								// show succes message for User Deleted from Database
								$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' User Is Deleted from Database</div>';
								redirectHome($theMsg, 'back');
							}

						} else { // If there is no Avatar For This User Delete his info from Database

							$stmt = $con->prepare("DELETE FROM users WHERE UserID = ?");
							$stmt->execute([$userid]);

							$theMsg = '<div class="alert alert-success">' . $stmt->rowCount() . ' Record Deleted</div>';
							redirectHome($theMsg, 'back');
						}

			} else {

				$theMsg = '<div class="alert alert-danger">This ID Is Not Exist</div>';
				redirectHome($theMsg);

			}

		  echo '</div>';

		}

      include $tpl . "footer.php";

  } else {

  	header('Location: index.php');
    exit();
        
  }

	ob_end_flush();