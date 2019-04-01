<?php

class Items {
    public $item_id;
    public $item_name;
    public $item_price;
    public $item_quantity;
    public $item_quantity_unit;
    public $store_id;
    public $password_hashed;
    public $username = "";
    private $noerrors = true;
    private $nameError = null;
    private $priceError = null;
    private $quantityError = null;
    private $passwordError = null;
    private $quantityUnitError = null;
    private $title = "Lowest Regular Price";
    private $tableName = "lrp_items";
    private $className = "items";

    /**
     * This function logs out the user directing them to logout.php.
     *
     * @return none
     */
    function logout() {
        header("Location: logout.php");
    }

    function create_record() { // display "create" form
        $this->generate_html_top (1);
        $this->generate_form_group("Item", $this->nameError, $this->item_name, "autofocus");
        $this->generate_form_group("Price", $this->priceError, $this->item_price);
        $this->generate_form_group("Quantity", $this->quantityError, $this->item_quantity);
        $this->generate_form_group("Quantity Unit", $this->quantityUnitError, $this->item_quantity_unit);
        $this->generate_drop_down("Store");
        $this->generate_html_bottom (1);
    } // end function create_record()

    function update_record($item_id) { // display "update" form
        if($this->noerrors) $this->select_db_record($item_id);
        $this->generate_html_top(3, $item_id);
        $this->generate_form_group("Item", $this->nameError, $this->item_name, "autofocus");
        $this->generate_form_group("Price", $this->priceError, $this->item_price);
        $this->generate_form_group("Quantity", $this->quantityError, $this->item_quantity);
        $this->generate_form_group("Quantity Unit", $this->quantityUnitError, $this->item_quantity_unit);
        $this->generate_drop_down("Store");
        $this->generate_html_bottom(3);
    } // end function update_record()

    function delete_record($item_id) { // display "delete" form
        $this->select_db_record($item_id);
        $this->generate_html_top(4, $item_id);
        $this->generate_form_group("Item", $this->nameError, $this->item_name, "disabled");
        $this->generate_form_group("Price", $this->priceError, $this->item_price, "disabled");
        $this->generate_form_group("Quantity", $this->quantityError, $this->item_quantity, "disabled");
        $this->generate_form_group("Quantity Unit", $this->quantityUnitError, $this->item_quantity_unit, "disabled");
        $this->generate_drop_down("Store");
        $this->generate_html_bottom(4);
    } // end function delete_record()

    /**
     * This method inserts one record into the table,
     * and redirects user to List, IF user input is valid,
     * OTHERWISE it redirects user back to Create form, with errors
     * - Input: user data from Create form
     * - Processing: INSERT (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (name, email, mobile)
     *   and database connection variables are set in datase.php.
     *   Note that $id will NOT be set because the record
     *   will be a new record so the SQL database will "auto-number"
     * - Postcondition: New record is added to the database table,
     *   and user is redirected to the List screen (if no errors),
     *   or Create form (if errors)
     *
     * @return none
     */
    function insert_db_record () {
    if ($this->fieldsAllValid ()) { // validate user input
            // if valid data, insert record into table
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO $this->tableName (item_name,item_price,store_id,item_quantity,item_quantity_unit,item_ppq) values(?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $item_ppq = $this->item_price / $this->item_quantity;
            $q->execute(array($this->item_name,$this->item_price,$this->store_id,$this->item_quantity,$this->item_quantity_unit,$item_ppq));
            Database::disconnect();
            header("Location: $this->className.php"); // go back to "list"
        }
        else {
            // if not valid data, go back to "create" form, with errors
            // Note: error fields are set in fieldsAllValid ()method
            $this->create_record();
        }
    } // end function insert_db_record

    private function select_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM $this->tableName where item_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        $this->item_name = $data['item_name'];
        $this->item_price= $data['item_price'];
        $this->item_quantity = $data['item_quantity'];
        $this->item_quantity_unit = $data['item_quantity_unit'];
        $this->store_id = $data['store_id'];
    } // function select_db_record()

    function update_db_record ($id) {
        $this->item_id = $id;
        if ($this->fieldsAllValid()) {
            $this->noerrors = true;
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE $this->tableName  set item_name = ?, item_price = ?, item_quantity = ?, item_quantity_unit = ?, store_id = ? WHERE item_id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->item_name,$this->item_price,$this->item_quantity,$this->item_quantity_unit,$this->store_id,$this->item_id));
            Database::disconnect();
            header("Location: $this->className.php");
        }
        else {
            $this->noerrors = false;
            $this->update_record($id);  // go back to "update" form
        }
    } // end function update_db_record

    function delete_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM $this->tableName WHERE item_id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        header("Location: $this->className.php");
    } // end function delete_db_record()

    private function generate_html_top ($fun, $id=null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Create"; $funNext = "insert_db_record";
                break;
            case 3: // update
                $funWord = "Update"; $funNext = "update_db_record&id=" . $id;
                break;
            case 4: // delete
                $funWord = "Delete"; $funNext = "delete_db_record&id=" . $id;
                break;
            default:
                echo "Error: Invalid function: generate_html_top()";
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord a $this->title</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    ";
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord a $this->title</h3>
                        </p>
                        <form class='form-horizontal' action='$this->className.php?fun=$funNext' method='post'>
                    ";
    } // end function generate_html_top()

    private function generate_html_bottom ($fun) {
        $backButton = "<a class='btn btn-secondary' href='$this->className.php'>Back</a>";
        switch ($fun) {
            case 1: // create
                $funButton = "<button type='submit' class='btn btn-success'>Create</button>";
                break;
            case 2: // read
                $funButton = "";
                break;
            case 3: // update
                $funButton = "<button type='submit' class='btn btn-warning'>Update</button>";
                break;
            case 4: // delete
                $funButton = "<button type='submit' class='btn btn-danger'>Delete</button>";
                break;
            default:
                echo "Error: Invalid function: generate_html_bottom()";
                exit();
                break;
        }
        echo "
                            <div class='form-actions'>
                                $funButton
                                $backButton
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
    } // end function generate_html_bottom()

    private function generate_drop_down ($label) {
        $pdo = Database::connect();
        $sql = "SELECT *
                FROM lrp_stores
                NATURAL JOIN lrp_companies";
        echo "<div class='form-group'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        echo "<select name='store_id'>";
        foreach ($pdo->query($sql) as $row) {
            echo "<option value='" . $row['store_id'] . "'>" . $row['store_city'] . " - " . $row['company_name'] . "</option>";
        };
        echo "</select> </div>";
    }

    private function generate_form_group ($label, $labelError, $val, $modifier="") {
        echo "<div class='form-group'";
        echo !empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        //echo "<div class='controls'>";
        echo "<input "
            . "name='$label' "
            . "type='text' "
            . "$modifier "
            . "placeholder='$label' "
            . "value='";
        echo !empty($val) ? $val : '';
        echo "'>";
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        //echo "</div>"; // end div: class='controls'

        echo "</div>"; // end div: class='form-group'
    } // end function generate_form_group(

    private function fieldsAllValid () {
        $valid = true;

        if (empty($this->item_name)) {
            $this->nameError = 'Please enter Name';
            $valid = false;
        }
        if (empty($this->item_price)) {
            $this->priceError = 'Please enter the price';
            $valid = false;
        }
        if (empty($this->item_quantity)) {
            $this->quantityError = 'Please enter a valid quantity.';
            $valid = false;
        }
        if (empty($this->item_quantity_unit)) {
            $this->quantityUnitError = 'Please enter a valid quantity unit.';
            $valid = false;
        }
        return $valid;
    } // end function fieldsAllValid()

    function list_records() {
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$this->title" . "s" . "</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";
        echo "
            </head>
            <body>
                <div class='container'>
                    <a class='btn btn-success' href='https://github.com/jawsdaws/CIS355/tree/master/Prog05'>Github Repo</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog05/uml.png'>UML</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog05/read.png'>Read</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog05/update.png'>Update</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog05/delete.png'>Delete</a>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                        <h4>Logged in as " . "$this->username" . "</h4>
                    </p>
                    <p>
                        <a href='$this->className.php?fun=display_create_form' class='btn btn-success'>Create</a>
                        <a href='$this->className.php?fun=logout' class='btn btn-success'>Logout</a>
                    </p>
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Store</th>
                                    <th>Price/Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "UPDATE lrp_items
                SET item_ppq = item_price/item_quantity";
        $q = $pdo->prepare($sql);
        $q->execute();

        $sql = "SELECT *
                FROM lrp_items
                NATURAL JOIN lrp_stores
                NATURAL JOIN lrp_companies
                ORDER BY item_name,item_ppq ASC";
        setlocale(LC_MONETARY, 'en_US');
        
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
            echo "<td>". $row["item_name"] . "</td>";
            echo "<td>". money_format('$%i', $row["item_price"]) . "</td>";
            echo "<td>". $row["company_name"] . " - " . $row["store_city"] . "</td>";
            $ppq = number_format((float)$row["item_ppq"], 3, '.', '');
            echo "<td>\$$ppq per " . $row['item_quantity_unit'] . "</td>";
            echo "<td width=250>";
            echo "<a class='btn btn-warning' href='$this->className.php?fun=display_update_form&id=".$row["item_id"]."'>Update</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->className.php?fun=display_delete_form&id=".$row["item_id"]."'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();
        echo "
                            </tbody>
                        </table>
                    </div>
                </div>

            </body>

        </html>
                    ";
    } // end function list_records()

} // end class Customer
