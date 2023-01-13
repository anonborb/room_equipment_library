<?php
require_once __DIR__.'/../../data/DataHandler.php';
$DB = new DataHandler;
$list = $DB->get_all_rooms();

?><!DOCTYPE html>
<html>
    <head>
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
        </style>
    </head>
<body>

<h1>Show Room</h1>
<h2>Number of Items: <?=count($list)?></h2>
<pre>
<table style="width:100%">
    <tr>
            <th>Room</th>
            <th>Maximum Space</th>
            <th>Free Space</th>
    </tr>
<?php
    foreach ($list as $id => $room) {    

        echo "<tr><td><a href='equipment.php?room_id={$room->get_label()}'>{$room->get_label()}</a></td>";
        echo "<td>", $room->get_max_capacity(), "</td>";
        echo "<td>", $room->get_curr_capacity(), "</td></tr>";        
    }
?>
</table>
<form action="add.php">
    <input type="submit" value="Add Room">
</form>


</pre>
</body>
</html>