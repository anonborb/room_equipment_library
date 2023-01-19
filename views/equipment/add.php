<?php
require_once __DIR__.'/../../data/EquipmentHandler.php';
require_once '../../utility/FormHandler.php';   
$DB = new EquipmentHandler;

?><!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <h1>Add Equipment</h1>
        <form method='POST'>
            <label for="equip_id">Enter new Equipment-ID:</label><br />
            <input type="text" id="equip_id" name="equip_id" /><br />
            <label for="users">Enter number of users required:</label><br />
            <input type="text" id="users" name="users" /><br />
            <label for="storage">Enter required storage space:</label><br />
            <input type="text" id="storage" name="storage" /><br />
            <label for="room_optional">Set location:</label><br />
            <input type="text" id="room_optional" name="room_optional" placeholder="(Optional)" /><br />
            <label for="overwrite_box">Allow overwrite?</label>
            <input type="checkbox" id="overwrite_box" name="overwrite" /><br />

            <br /><input type="submit" value="Enter" />
        </form>

        <?php

        $fHandler = new FormHandler($_POST);
        if ($fHandler->valid_addEquip()) {
            $equip = new Equipment($_POST['equip_id'], $_POST['users'], $_POST['storage'], $_POST['room_optional']);
            $DB->add($equip, $_POST['room_optional']);

            echo $_POST['equip_id'], " successfully added.";
        } else {
            $fHandler->errors();
        }
        
        ?>
        <br /><a href='inventory.php'>View Inventory</a>
    </body>
    
</html>