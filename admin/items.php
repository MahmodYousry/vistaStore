<?php

	/*
	=================================================
	== Items Page
	=================================================
	*/

	ob_start(); // OutPut Buffering Start
	session_start();
	$pageTitle = 'Admin | Items';

    if (isset($_SESSION['Username'])) {
      include 'init.php';
      $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

	    if ($do == 'Manage') {

			// variables
			$datatable = "items";
			$nextPage = "";
			$results_per_page = 2;

			// Define pages with numbers
			if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
			$start_from = ($page-1) * $results_per_page;

			// get data from courses by course_ID limit start from 0 and to all items in the page
			$sql = "SELECT items.*,
							brand.name AS brand_name,
							users.Username,
							type.type_name
							FROM ".$datatable." 
							JOIN brand ON brand.id = items.brand_id 
							JOIN users ON users.UserID = items.Member_ID
							JOIN type ON type.type_id = items.type_id
							ORDER BY Item_ID DESC LIMIT $start_from, ".$results_per_page;
			$rs_result = $con->query($sql);
			$rowCout = $rs_result->fetchAll();
			
			$countAllVid = "SELECT *, COUNT(*) as how_many_tags FROM $datatable
							INNER JOIN brand ON brand.id = items.brand_id
							INNER JOIN users ON users.UserID = items.Member_ID "; 
			$result1 = $con->query($countAllVid);
			$rowls = $result1->fetch();
			$total_pages = ceil($rowls["how_many_tags"] / $results_per_page);

			if (isset($rowCout[0])) {?>

				<h1 class="text-center">Manage Items</h1>
				<div class="container">
					<div class="members-options">
						<a class="btn btn-md btn-primary" href="items.php?do=Add"><i class="fa fa-plus"></i> New Item</a>
						<a class="btn btn-primary btn-md" href="dashboard.php">back 
							<i class="fa fa-chevron-right fa-xs"></i>
						</a>
					</div>
					<div class="table-responsive">
						<table class="main-table text-center table table-bordered">
							<tr>
								<td>#ID</td>
								<td>Name</td>
								<td>Number</td>
								<td>Approve</td>
								<td>Price</td>
								<td>Last_price_date</td>
								<!-- <td>item_location</td> -->
								<td>Item Img</td>
								<td>Brand</td>
								<td>Member</td>
								<td>Type</td>
								<td>Options</td>
							</tr>
							
							<?php

								foreach ($rowCout as $item) {

									// get the images in item_imgs table by item id
									$stmtimg = $con->prepare("SELECT * FROM item_imgs WHERE item_ID = ?");
									$stmtimg->execute([$item['Item_ID']]);
									$itemImgs = $stmtimg->fetchAll();
									$imgRowCount = $stmtimg->rowCount();

									echo "<tr>";
										echo "<td>" . $item['Item_ID'] . "</td>";
										echo '<td>' . $item['item_name'] . '</td>';
										echo "<td>" . $item['number'] . "</td>";
										echo "<td>" . $item['Approve'] . "</td>";
										echo "<td>" . $item['price'] . "</td>";
										echo "<td>" . $item['Last_price_date'] . "</td>";
										//echo "<td>" . $item['item_location'] . "</td>";

										echo "<td>";
												
											if (empty($itemImgs)) {
												echo '<div class="alert alert-info" role="alert">No Images For This Item</div>';
											} else {
												echo '<div class="imgs_cont">';
													foreach ($itemImgs as $itemImg) {
														echo "<img src='../products/" . $itemImg["img_src"] . "'>";
													}
												echo '</div>';
											}
												
										echo "</td>";

										echo "<td>" . $item['brand_name'] . "</td>";
										echo "<td>" . $item['Username'] . "</td>";
										echo "<td>" . $item['type_name'] . "</td>";
										echo "<td>
												<a href='items.php?do=Edit&itemid=" . $item['Item_ID'] . "' class='btn btn-success' data-toggle='tooltip' data-placement='left' title='Edit this item'><i class='fa fa-edit'></i> Edit</a>";

												
												echo "<a href='items.php?do=setimg&itemid=" . $item['Item_ID'] . "' class='btn btn-primary' data-toggle='tooltip' data-placement='left' title='set image for this item'><i class='fa fa-edit'></i> Edit Images</a>";
												
												echo "<button id='deleteImgs" . $item['Item_ID'] . "' onclick='deleteImgs(this.id)' class='btn btn-danger' data-toggle='tooltip' data-placement='left' title='Delete This item images From Database and From the Server'><i class='fa fa-close'></i> Delete Images</button>"; 
												
												echo "<a id='" . $item['Item_ID'] . "' onclick='deleteAll(this.id);' href='#' class='btn btn-danger' data-toggle='tooltip' data-placement='left' title='Delete This item And Its Image From Database and From the Server'><i class='fa fa-close'></i> Delete all</a>";
												?>
												<div id="options_con"> <?php
													if ($item['Approve'] == 0) { // if disapproved show approve button
														echo "<button id='approve" . $item['Item_ID'] . "' onclick='approveItem(this.id)' class='btn btn-info' data-toggle='tooltip' data-placement='left' title='item will not be seen without this option make sure to approve your item'><i class='fa fa-check'></i> Approve</button>"; 
														echo "<button id='disapprove" . $item['Item_ID'] . "' onclick='disapproveItem(this.id)' class='btn btn-info text-capitalize hideThis' data-toggle='tooltip' data-placement='left' title='make sure to approve your item to be seen'><i class='fa fa-close'></i> dispprove</button>"; 
														
													} else { // if approved show disapprove button
														echo "<button id='approve" . $item['Item_ID'] . "' onclick='approveItem(this.id)' class='btn btn-info hideThis' data-toggle='tooltip' data-placement='left' title='item will not be seen without this option make sure to approve your item'><i class='fa fa-check'></i> Approve</button>"; 
														echo "<button id='disapprove" . $item['Item_ID'] . "' onclick='disapproveItem(this.id)' class='btn btn-info text-capitalize' data-toggle='tooltip' data-placement='left' title='make sure to approve your item to be seen'><i class='fa fa-close'></i> dispprove</button>"; 
													}
												?>
												</div>
												
												<?php
										echo "</td>";
									echo "</tr>";
								}
							?>

						</table>
					</div>
				</div>

			<?php 
			
					echo '<div class="pagesContain">';
						echo '<div class="container">';
							echo '<nav aria-label="Page navigation example">';
								echo '<ul class="pagination">';
									$pageName = "items.php";
									$prevPage = $page - '1';
									$itszero = '';
									if ($prevPage == 0) { $prevPage = 1; } else { $prevPage = $page - '1'; $itszero = 'disabled';}
									if ($page == 1) { $itszero = 'disabled';} else {$itszero = '';}

									echo '<li class="page-item ' .$itszero. '"><a class="page-link" href="'.$pageName.'?page=' . $prevPage . '">Previous</a></li>';
									for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages

										if ($i==$page) {
											echo '<li class="page-item active"><a class="page-link" href="'.$pageName.'?page=' . $i . '">' . $i . '</a></li>';
										} else {
											echo '<li class="page-item"><a class="page-link" href="'.$pageName.'?page=' . $i . '">' . $i . '</a></li>';
										}
										
										$nextPage = $page + '1';
										
									};
									// NEXT button function
									if ($page == $total_pages) { // if we are ate the last page - disable the next button
										echo '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
									} else { // if not last page make next button work
										echo '<li class="page-item"><a class="page-link" href="'.$pageName.'?page=' . $nextPage . '">Next</a></li>';
									}
									
								echo '</ul>';
							echo '</nav>';
						echo '</div>';
					echo '</div>';

			} else {

				echo '<div class="container">';
					echo '<div class="nice-message">There\'s No Items To Show</div>'; 
					?>
						<div class="members-options">
							<a class="btn btn-md btn-primary" href="items.php?do=Add"><i class="fa fa-plus"></i> New Item</a>
							<a class="btn btn-primary btn-md" href="dashboard.php">back 
								<i class="fa fa-chevron-right fa-xs"></i>
							</a>
						</div>
					<?php 
				echo '</div>';

			}


	  	} elseif ($do == 'Add') { ?>

	  		<h1 class="text-center">Add New Item</h1>
			<div class="container">
				<form id="add_new_item" class="form-horizontal add-new-item" method="POST" enctype="multipart/form-data">
					<!-- Start upload pic Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="filepic">img</label>
						<div class="col-sm-10 col-md-8 upload_section">

							<input type="file" class="form-control" id="gallery-photo-add" name="productpic" multiple>

							<div id="progress_bar" class="progress add-item-progress">
								<div id="progress_bar_process" class="progress-bar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
							</div>

							<div id="uploaded_image" class="gallery"></div>
							<div class="clearfix"></div>
							<div id="upload_status"></div>

						</div>
					</div>
					<!-- END upload pic Field -->
					<!-- Start Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10 col-md-8">
							<input type="text" name="name" class="form-control" required="required" placeholder="Item Name" />
						</div>
					</div>
					<!-- END Name Field -->
					<!-- Start Description Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Count</label>
						<div class="col-sm-10 col-md-8">
							<input type="text" name="number" class="form-control" required="required" placeholder="How Many we have" />
						</div>
					</div>
					<!-- END Description Field -->
					<!-- Start Approve Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Approve</label>
						<div class="col-sm-10 col-md-8">
							<input type="text" name="approve" class="form-control" required="required" placeholder="Is it approved ?" />
						</div>
					</div>
					<!-- END Approve Field -->
					<!-- Start Price Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Price</label>
						<div class="col-sm-10 col-md-8">
							<input type="text" name="price" class="form-control" required="required" placeholder="Item Price" />
						</div>
					</div>
					<!-- END Price Field -->
					<!-- Start price date time Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Last Price date</label>
						<div class="col-sm-10 col-md-8">
							<input type="date" class="form-control" name="lastPriceDate" value="" />
						</div>
					</div>
					<!-- END price date time Field -->

					<!-- Start brand id Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Brand</label>
						<div class="col-sm-10 col-md-8">
							<select name="brand_id">
								<option value="0">...</option>
								<?php
									$allbrands = getAllFrom("*", "brand", "", "", "id");
									foreach ($allbrands as $brand) {
										echo "<option value='" . $brand['id'] . "'>" . $brand['name'] . "</option>";
									}
								?>
							</select>
						</div>
					</div>
					<!-- END brand id Field -->
					<!-- Start Members Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Member</label>
						<div class="col-sm-10 col-md-8">
							<select name="member">
								<option value="0">...</option>
								<?php
									$allMembers = getAllFrom("*", "users", "", "", "UserID");
									foreach ($allMembers as $user) {
										echo "<option value='" . $user['UserID'] . "'>" . $user['Username'] . "</option>";
									}
								?>
							</select>
						</div>
					</div>
					<!-- END Members Field -->
					<!-- Start Categories Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Type</label>
						<div class="col-sm-10 col-md-8">
							<select name="Type">
								<option value="0">...</option>
								<?php
									$alltypes = getAllFrom("*", "type", "", "", "type_id");
									foreach ($alltypes as $type) {
										echo "<option value='" . $type['type_id'] . "'>" . $type['type_name'] . "</option>";
									}
								?>
							</select>
						</div>
					</div>
					<!-- END Categories Field -->
					<!-- Start Tags Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Tags</label>
						<div class="col-sm-10 col-md-8">
							<input type="text" name="tags" class="form-control" placeholder="Separate Tags With Comma (,)" />
						</div>
					</div>
					<!-- END Tags Field -->
					<!-- Start submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input id="item_sumbit_button" type="submit" value="Add Item" class="btn btn-primary btn-sm" />
							<a class="btn btn-primary btn-sm" href="items.php">back 
								<i class="fa fa-chevron-right fa-xs"></i>
							</a>
							
						</div>
					</div>
					<!-- END submit Field -->
					<!-- Start error Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<div class="signup_messages"></div>
						</div>
					</div>
					<!-- END error Field -->
				</form>
			</div>

		<?php 


	    } elseif ($do == 'setimg') {
				// get item id from GET method
				$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
				// get all images bounded with this item id
				$stmt2 = $con->prepare("SELECT * FROM item_imgs WHERE item_ID = ? ORDER BY row_id DESC");
				$stmt2->execute([$itemid]);
				$item_images = $stmt2->fetchAll();

			?>

	    	
			<h1 class="text-center text-capitalize">upload Image for item</h1>
			<div class="container">

				<input type="hidden" id="itemid" value="<?php echo $itemid; ?>">


				<!-- Start form-img upload Field -->
				<form id="setImgForitem-form" class="form-horizontal" enctype="multipart/form-data">

					<progress id="progressBar" style="width: 100%;height: 50px;color: #f90;" value="0" max="100" ></progress>
					<div class="stltx alert alert-info text-capitalize" id="stltx"></div>
					<p id="loading_n_total"></p>

					<!-- Start post-img Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-10">
							<input type="hidden" name="itemid" class="form-control" value="<?php echo $itemid ?>" autocomplete="off" />
						</div>
					</div>
					<!-- END post-img Field -->
					<!-- Start post-img Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Image</label>
						<div class="col-sm-10 col-md-10">
							<input type="file" class="form-control" id="setImages" name="images[]" multiple>
						</div>
					</div>
					<!-- END post-img Field -->
					<!-- Start submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<!-- <input type="submit" value="Upload" class="btn btn-primary btn-lg" /> -->
							<button type="submit" class="btn btn-primary btn-md"><i class="fa fa-check fa-fw"></i> Upload</button>

							<a class="btn btn-primary btn-md" href="items.php">back 
								<i class="fa fa-chevron-right fa-xs"></i>
							</a>
							<button type="button" class="btn btn-primary" id="refreshImages" onclick="refreshImagesArea();"><i class="fa fa-refresh fa-fw"></i> refresh</button>
						</div>
					</div>
					<!-- END submit Field -->
				</form>
				<!-- END form-img upload Field -->

				<?php if (!empty($item_images)) {?>
					<!-- Start Images Show -->
					
					<div class="item-img-container">
						<?php
							foreach ($item_images as $item_image) {
								echo '<div class="relative" id="itemid'.$itemid.'">';
									echo '<span onclick="deleteSingleRows(this.id);" id="row_id'.$item_image['row_id'].'">';
										echo '<i class="fa fa-close"></i> Delete';
									echo '</span>';
									
									echo '<img src="../products/'.$item_image['img_src'].'">';
								echo '</div>';
							}
						?>
					</div>
					<!-- Start Images Show -->
				<?php } else {
						echo '<div class="alert alert-success text-capitalize"><i class="fa fa-info-circle fa-fw"></i> No Images for this Item</div>';
					}
				?>
			</div>
      	


<?php   } elseif ($do == 'removeimg') {


			echo '<h1 class="text-center">Delete Item Image</h1>';
      		echo '<div class="container">';

      		
			$itemofid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

	  		$getAny = $con->prepare("SELECT * FROM items WHERE Item_ID = ?");
			$getAny->execute(array($itemofid));
			$lnks = $getAny->fetchAll();

      		foreach ($lnks as $lnk) { ?>

				<div class="message ms-delimg"></div>
				<!-- Start form-img Field -->
				<form id="del-item-image-form" class="form-horizontal" enctype="multipart/form-data">
					<!-- Start post-img Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Image Address</label>
						<div class="col-sm-10 col-md-6">
							<input id="delitemImageAddress" type="text" value="<?php echo $lnk['Image']; ?>" name="deladdresImg" class="form-control" autocomplete="off" />
						</div>
					</div>
					<!-- END post-img Field -->
					<!-- Start submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input id="subdeliImage" type="submit" value="Delete" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- END submit Field -->
				</form>
				<!-- END form-img Field -->
 
		<?php }  echo "</div>";


		} elseif ($do == 'Edit') {

			// Check If Get Request item Is Numberic & Get The Integer Value of it.
			$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

			// Select All Data Depend on This ID
			$stmt = $con->prepare("SELECT * FROM items WHERE Item_ID = ?");
			$stmt->execute(array($itemid));
			$item = $stmt->fetch();
			$count = $stmt->rowCount();

			// If There\s Such ID Show The Form

			if($count > 0) { ?>

				<h1 class="text-center">Edit Item</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=Update" method="POST">
						<input type="hidden" name="itemid" value="<?php echo $itemid ?>" />
						<!-- Start Name Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Item Name</label>
							<div class="col-sm-10 col-md-6">
								<input 
									type="text" 
									name="name" 
									class="form-control"
									required="required" 
									placeholder="Name of The Item"
									value="<?php echo $item['item_name'] ?>" />
							</div>
						</div>
						<!-- END Name Field -->
						<!-- Start Description Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Number</label>
							<div class="col-sm-10 col-md-6">
									<input type="text" name="number" class="form-control"
										required="required" placeholder="Name of The Item" value="<?php echo $item['number'] ?>" />
							</div>
						</div>
						<!-- END Description Field -->
						<!-- Start approve Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Approve</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="approve" class="form-control" 
									required="required" placeholder="Price of The Item" value="<?php echo $item['Approve'] ?>" />
							</div>
						</div>
						<!-- END approve Field -->
						<!-- Start Price Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Price</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="price" class="form-control" 
									required="required" placeholder="Price of The Item" value="<?php echo $item['price'] ?>" />
							</div>
						</div>
						<!-- END Price Field -->
						<!-- Start Last Price date Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Last Price date</label>
							<div class="col-sm-10 col-md-6">
								<input type="date" class="form-control" name="lastPriceDate" value="<?php echo $item['Last_price_date'] ?>" />
							</div>
						</div>
						<!-- END Last Price date Field -->
						
						<!-- Start Members Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Member</label>
							<div class="col-sm-10 col-md-6">
								<select name="member" class="form-control">
									<?php

										$stmt = $con->prepare("SELECT * FROM users");
										$stmt->execute();
										$users = $stmt->fetchAll();

										foreach ($users as $user) {
											echo "<option value='" . $user['UserID'] . "'"; 
											if ($item['Member_ID'] == $user['UserID']) { echo 'selected'; } 
											echo ">" . $user['Username'] . "</option>";
										}

									?>
								</select>
							</div>
						</div>
						<!-- END Members Field -->
						<!-- Start brand Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">brand</label>
							<div class="col-sm-10 col-md-6">
								<select name="brand" class="form-control">
									<?php

										$stmt2 = $con->prepare("SELECT * FROM `brand`");
										$stmt2->execute();
										$brands = $stmt2->fetchAll();

										foreach ($brands as $brand) {
											echo "<option value='" . $brand['id'] . "'";
											if ($item['type_id'] == $brand['id']) { echo 'selected'; }
											echo ">" . $brand['name'] . "</option>";
										}

									?>
								</select>
							</div>
						</div>
						<!-- END brand Field -->
						<!-- Start type Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Type</label>
							<div class="col-sm-10 col-md-6">
								<select name="type" class="form-control">
									<?php

										$stmt2 = $con->prepare("SELECT * FROM `type`");
										$stmt2->execute();
										$types = $stmt2->fetchAll();

										foreach ($types as $type) {
											echo "<option value='" . $type['type_id'] . "'";
											if ($item['type_id'] == $type['type_id']) { echo 'selected'; }
											echo ">" . $type['type_name'] . "</option>";
										}

									?>
								</select>
							</div>
						</div>
						<!-- END type Field -->
						<!-- Start Tags Field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Tags</label>
							<div class="col-sm-10 col-md-6">
								<input type="text" name="tags" class="form-control"
									placeholder="Separate Tags With Comma (,)" value="<?php echo $item['tags'] ?>" />
							</div>
						</div>
						<!-- END Tags Field -->
						<!-- Start submit Field -->
						<div class="form-group form-group-lg">
							<div class="col-sm-offset-2 col-sm-10">
								<input type="submit" value="Save Item" class="btn btn-primary btn-sm">
								<a class="btn btn-primary btn-sm" href="items.php">back <i class="fa fa-chevron-right fa-xs"></i></a>
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

	    	echo '<h1 class="text-center">Update item</h1>';
      		echo '<div class="container">';

      		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      			// Get Variables From The Form

      			$id 			= $_POST['itemid'];
      			$name 		= $_POST['name'];
      			$number		= $_POST['number'];
      			$price 		= $_POST['price'];
      			$approve 	= $_POST['approve'];
      			$date			= $_POST['lastPriceDate'];
      			$member 	= $_POST['member'];
      			$brand		= $_POST['brand'];
      			$type			= $_POST['type'];
      			$tags 		= $_POST['tags'];

      			// Validate The Form

      			$formErrors = array();

      			if (empty($name)) { $formErrors[] = 'Name Can\'t be <strong>Empty</strong>'; }
						if (empty($number)) { $formErrors[] = 'number Can\'t be <strong>Empty</strong>'; }
      			if (empty($approve)) { $formErrors[] = 'approve Can\'t be <strong>Empty</strong>'; }
      			if (empty($price)) { $formErrors[] = 'Price Can\'t be <strong>Empty</strong>'; }
						if ($member == 0) { $formErrors[] = 'You Must Choose The <strong>Member</strong>'; }
						if ($brand == 0) { $formErrors[] = 'You Must Choose The <strong>Category</strong>'; }

      			// Loop Into Errors Array And Echo It
      			foreach($formErrors as $error) {
      				echo '<div class="alert alert-danger">' . $error . '</div>';
      			}

      			// Check If There's No Error Proceed The Update Operation
      			if (empty($formErrors)) {

	      			// Update The Database With This Info
	      			$stmt = $con->prepare("UPDATE items SET 
					      								   		item_name = ?,
					      								   		`number` = ?,
					      								   		Approve = ?, 
					      								   		price = ?,
					      								   		Last_price_date = ?,
					      								   		type_id = ?,
					      								   		brand_id = ?,
					      								   		Member_ID = ?,
					      								   		tags = ?
					      								   WHERE 
					      								   		Item_ID = ?");

	      			$stmt->execute(array($name, $number, $approve, $price, $date, $member, $brand, $type, $tags, $id));

	       			// Echo Success Message
	      			$theMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . ' Record Updated</div>';
	      			redirectHome($theMsg, 'back');

      			}

      		} else {

      			$theMsg = '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';
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