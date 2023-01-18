<?php

/** Adds a room to the database */

require_once __DIR__.'/../../../../data/DataHandler.php';
$db= new DataHandler;
$room_id1 = 'room100';
$room_id2 = 'room200';
$db->add_room(new Room($room_id1, 40));
$db->add_room(new Room($room_id2, 20));