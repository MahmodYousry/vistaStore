<?php 

    include '../connect.php';
	include "../includes/functions/functions.php";

    // ajaxfuntions.js data gets from there and from items.php

    if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_POST['name'])) {

        // variables
        $name 		    = $_POST['name'];
        $number         = $_POST['number'];
        $approve        = $_POST['approve'];
        $price          = $_POST['price'];
        $lastPriceDate  = $_POST['lastPriceDate'];
        $brand_id       = $_POST['brand_id'];
        $member         = $_POST['member'];
        $Type           = $_POST['Type'];
        $tags           = $_POST['tags'];

        // get the last id of item
        $stmt1 = $con->prepare("SELECT * FROM items ORDER BY Item_ID DESC LIMIT 1");
        $stmt1->execute();
        $lastItemid = $stmt1->fetch();
        // the is the new id we made for the new item
        $main_id = $lastItemid['Item_ID']; // Last id that been created in upload_item_images.php
        
        // Validate The Form
        $formErrors = array();

        if (empty($name)) { $formErrors[] = 'Name Can\'t be <strong>Empty</strong>'; }
        if (empty($number)) { $formErrors[] = 'number Can\'t be <strong>Empty</strong>'; }
        if (empty($price)) { $formErrors[] = 'Price Can\'t be <strong>Empty</strong>'; }
        if (empty($approve)) { $formErrors[] = 'approve Can\'t be <strong>Empty</strong>'; }
        if (empty($lastPriceDate)) { $formErrors[] = 'last Price Date Can\'t be <strong>Empty</strong>'; }
        if (empty($Type)) { $formErrors[] = 'Type Can\'t be <strong>Empty</strong>'; }

        if ($member == 0) { $formErrors[] = 'You Must Choose The <strong>Member</strong>'; }
        if ($brand_id == 0) { $formErrors[] = 'You Must Choose The <strong>Category</strong>'; }

        // Loop Into Errors Array And Echo It
        foreach($formErrors as $error) { echo '<div class="alert alert-danger">' . $error . '</div>'; }

        // Check If There's No Error Proceed The Update Operation
        if (empty($formErrors)) {

            // update item ino To Database

            $stmt = $con->prepare(" UPDATE `items`
                                    SET
                                        `item_name` = ?,
                                        `number` = ?,
                                        Approve = ?,
                                        `price` = ?,
                                        `Last_price_date` = ?,
                                        brand_id = ?,
                                        Member_ID = ?,
                                        `type_id` = ?,
                                        tags = ? WHERE `Item_ID` = ?");

            $stmt->execute([$name, $number, $approve, $price, $lastPriceDate, $brand_id, $member, $Type, $tags, $main_id]);

            // if Success
            if ($stmt->rowCount() == 1) {
                echo 'success';
            } else { // if failed
                echo 'failed';
            }

          }


    } else {echo '<div class="alert alert-danger">Sorry You Can\'t Browse This Page Directly</div>';}

