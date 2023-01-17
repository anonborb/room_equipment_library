<?php
//==================================
// Class Definition of DataHandler
//==================================
require_once __DIR__.'/../utility/Equipment.php';
require_once __DIR__.'/../utility/Room.php';
session_start();


class DataHandler {
    
    const MAX_SPACE = 1000;
    const WAREHOUSE = "warehouse";
    const NONE = "";

    private array $room_list, $equip_list; 

    /**
     * Constructor for the Handler.
     * Initializes two arrays containing all equipment and all rooms.
     * Automatically creates the warehouse.
     */
    public function __construct() {
        $_SESSION['room_list']  ?? $_SESSION['room_list'] = [self::WAREHOUSE => new Room(self::WAREHOUSE, self::MAX_SPACE)];
        $_SESSION['equip_list'] ?? $_SESSION['equip_list'] = [];
        $this->room_list = $_SESSION['room_list'];
        $this->equip_list = $_SESSION['equip_list'];
    }
    
    /************** Equipment Methods ****************/
    /**
     * Adds an equipment to the database. Returns false if equipment already exists.
     * If overwrite flag is set to true, the old equipment will be overwritten.
     * @param  Equipment $equip to be added
     * @param  string $room Optional room location. 
     * @param  bool $overwrite Default will not allow overwriting, if set to true, overwriting will be allowed.
     * @return bool
     */
    public function add_equipment(Equipment $new_equip, String $room = self::NONE, bool $overwrite = false) : bool {
        if (!isset($new_equip)) {
            throw new InvalidArgumentException("DataHandler:add_equipment, Equipment is null");
        }
        $eq_label = $new_equip->get_label();
        if (isset($this->equip_list[$eq_label]) && !$overwrite) {   // Checks if equipment already exists and whether to overwrite if it does
            return false;
        }
        if ($room != self::NONE) {
            try {
                $this->room_list[$room]->add_equipment($new_equip);
            } catch (Exception $e) {
                echo $e->getMessage(), "<br>Equipment was not added to room<br>";
            }
            $new_equip->set_location($room);
        }
        $this->equip_list[$eq_label] = $new_equip;
        return true;
    }
        
    /**
     * Removes specified equipment from the database. Will remove from current room location.
     * Returns false if Equipment does not exist in the database.
     * @param  string $equip_id
     * @return bool
     */
    public function rm_equipment(string $equip_id) : bool {
        $equip = $this->equip_list[$equip_id];

        if (isset($equip)) {
            $room_location = $equip->get_location();
            if ($room_location != self::NONE) {     // Removing from current room location
                try {
                    $this->room_list[$room_location]->rm_equipment($equip);
                } catch (Exception $e) {    // On the off chance that Room does not exist in database 
                    echo $e->getMessage();
                    echo "<br>Room does not exist.";
                }
            }
            
            unset($this->equip_list[$equip_id]);    // Removing from inventory
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Retreives an equipment from the equip_list. Returns null if not found.
     * @param  string $equip_id
     * @return Equipment
     */
    public function get_equipment(string $equip_id) : Equipment|null {
        return $this->equip_list[$equip_id];
    }
    
    /**
     * Returns an array of all equipment in the database.
     * @return array
     */
    public function get_all_equipment() : array {
        return $this->equip_list;
    }
    
    /**
     * Returns total number of Equipment in the database.
     * @return int
     */
    public function get_total_equipment() : int {
        return count($this->equip_list);
    }
    
    /**
     * Moves equipment between rooms. 
     * Removes from current room, adds to new room, sets equipment location to new location
     * Returns false if new Room location does not exist.
     * @param  mixed $equip_id
     * @param  mixed $room_id
     * @throws InvalidArgumentException
     * @return bool
     */
    public function move_equipment(Equipment $equip, string $new_room_id) : bool {     // yet to be implemented
        if (!isset($equip)) {   
            throw new InvalidArgumentException("DataHandler:move_equipment, equipment is null");
        }

        $old_location = $equip->get_location();
        if ($old_location != self::NONE) {  // Removes equipment from old location
            $old_room = $this->room_list[$old_location]->rm_equipment($equip);
        }

        $new_room = $this->room_list[$new_room_id];
        if (!isset($new_room)) {    
            return false;       // Room does not exist
        }
        $new_room->add_equipment($equip);   // Room adds equipment
        $equip->set_location($new_room_id); // Equipment location changes
        return true;

    }



    /***************** Room Methods *******************/
    /**
     * Adds the room to the room array. Returns false if room already exists.
     * If overwrite flag is set to true, the old room will be overwritten.
     * @param  Room $room to be added
     * @param  bool $overwrite By default set to false. If set to true, will allow overwriting.
     * @return bool
     */
    public function add_room(Room $room, bool $overwrite = false) : bool {
        //$_SESSION['room_list'][$room->get_label()] = $room;

        if (!isset($room)) {
            throw new InvalidArgumentException("DataHandler:add_room, Room is null");
        }
        $room_label = $room->get_label();
        if (isset($this->room_list[$room_label]) && !$overwrite) {   // Checks if equipment already exists and whether to overwrite if it does
            return false;
        }
        $this->room_list[$room_label] = $room;
        return true;
    }
    
    /**
     * Removes the room from the database. Will return all equipment to warehouse.
     * Returns false if room does not exist.
     * @param  mixed $room_id
     * @return bool
     */
    public function rm_room(string $room_id) : bool { 
        $room = $this->room_list[$room_id];
        if (!isset($room)) {
            return false;
        }
        $room->rm_equipment_all();
        unset($this->room_list[$room_id]);
        return true;
    }
    
    /**
     * Retrieves the room from the room_list. Returns null if not found.
     * @param  mixed $room_id
     * @return Room
     */
    public function get_room(string $room_id) : Room|null {
        return $this->room_list[$room_id];
    }

    
    /**
     * Returns an array of all rooms in the database.
     * @return array
     */
    public function get_all_rooms() : array {
        return $_SESSION['room_list'];
    }

    public function get_total_rooms() {
        return count($this->room_list);
    }




    /************** Printing data *****************/
    /**
     * Prints the entire database
     * @return void
     */
    public function get_status() : void {
        echo "<pre>";
        foreach ($this->room_list as $room_id => $room) {
            $room->print();
            echo "Equipment:<br>", $room->list_equipment();
            echo "___________________________________________<br>";
        }
        
        echo "Total number of rooms=", count($this->room_list), "<br>Total number of Equipment=", count($this->equip_list), '<br>';
    }
    
    /**
     * Prints out a list of all equipments in the database.
     * @return void
     */
    public function list_all_equipment() : void {
        foreach ($this->equip_list as $equipid => $equip) {
            $equip->print();
        }
    }

    /**
     * Prints out a list of all rooms in the database.
     * @return void
     */
    public function list_all_rooms() : void {
        foreach ($this->room_list as $roomid => $room) {
            $room->print();
        }
    }



}