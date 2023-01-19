<?php
require_once __DIR__.'/../../data/RoomHandler.php';
require_once '../../utility/FormHandler.php';
$DB = new RoomHandler;

?><!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <h1>Add room</h1>
        <form method='POST'>
            <label for="new_room_id">Enter new Room-ID:</label><br>
            <input type="text" id="new_room_id" name="new_room_id"><br>

            <label for="room_cap">Set room capacity:</label><br>
            <input type="text" id="room_cap" name="room_cap"><br>

            <label for="overwrite_box">Allow overwrite?</label>
            <input type="checkbox" id="overwrite_box" name="overwrite"><br>

            <br><input type="submit" value="Enter">
        </form>

        <?php
        $fHandler = new FormHandler($_POST);
        
        if ($fHandler->valid_addRoom()) {
            $overwrite = $_POST['overwrite'] ? true : false;
            $room = new Room($_POST['new_room_id'], $_POST['room_cap']);
            echo $_POST['new_room_id'], ($DB->add($room, $overwrite) ? " successfully added to the database." : " already exists in the database.");
        } else {
            $fHandler->errors();
        }

        ?>
        <br><a href='all.php'>View all rooms</a>

    </body>
</html>