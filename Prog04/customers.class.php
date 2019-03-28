<?php

class Customer {
    public $id;
    public $name;
    public $email;
    public $mobile;
    public $password;
    public $password_hashed;
    public $username = "";
    public $upload = "";
    private $uploadError = null;
    private $noerrors = true;
    private $nameError = null;
    private $emailError = null;
    private $mobileError = null;
    private $passwordError = null;
    private $title = "Customer";
    private $tableName = "customers";

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
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_form_group("password", $this->passwordError, $this->password, "", "password");
        $this->generate_html_bottom (1);
    } // end function create_record()

    function read_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(2);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile, "disabled");
        $this->generate_html_bottom(2);
    } // end function read_record()

    function update_record($id) { // display "update" form
        if($this->noerrors) $this->select_db_record($id);
        $this->generate_html_top(3, $id);
        $this->generate_form_group("name", $this->nameError, $this->name, "autofocus onfocus='this.select()'");
        $this->generate_form_group("email", $this->emailError, $this->email);
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile);
        $this->generate_html_bottom(3);
    } // end function update_record()

    function delete_record($id) { // display "delete" form
        $this->select_db_record($id);
        $this->generate_html_top(4, $id);
        $this->generate_form_group("name", $this->nameError, $this->name, "disabled");
        $this->generate_form_group("email", $this->emailError, $this->email, "disabled");
        $this->generate_form_group("mobile", $this->mobileError, $this->mobile, "disabled");
        $this->generate_html_bottom(4);
    } // end function delete_record()

    function display_upload_form($id) { // display "upload" form
        echo '<!DOCTYPE html>
        <html>
            <body>
                <form method="post" action="upload03.php?id=' . $id . '"
                      onsubmit="return Validate(this);"
                      enctype="multipart/form-data">
                    <p>File</p>
                    <input type="file" required
                        name="Filename">
                    <br/>
                    <input TYPE="submit" name="upload" value="Submit"/>
                </form>

                <script>
                    var _validFileExtensions = [".jpg", ".jpeg", ".gif", ".png"];
                    function Validate(oForm) {
                        var arrInputs = oForm.getElementsByTagName("input");
                        for (var i = 0; i < arrInputs.length; i++) {
                            var oInput = arrInputs[i];
                            if (oInput.type == "file") {
                                var sFileName = oInput.value;
                                if (sFileName.length > 0) {
                                    var blnValid = false;
                                    for (var j = 0; j < _validFileExtensions.length; j++) {
                                        var sCurExtension = _validFileExtensions[j];
                                        if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                                            blnValid = true;
                                            break;
                                        }
                                    }

                                    if (!blnValid) {
                                        alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                                        return false;
                                    }
                                }
                            }
                        }

                        return true;
                    }
                </script>

            </body>

        </html>';
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
            $this->password_hashed = MD5($this->password);
            $sql = "INSERT INTO $this->tableName (name,mobile,email,password_hash) values(?, ?, ?, ?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->name, $this->mobile, $this->email, $this->password_hashed));
            Database::disconnect();
            header("Location: $this->tableName.php"); // go back to "list"
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
        $sql = "SELECT * FROM $this->tableName where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->mobile = $data['mobile'];
    } // function select_db_record()

    function update_db_record ($id) {
        $this->id = $id;
        if ($this->fieldsAllValid()) {
            $this->noerrors = true;
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE $this->tableName  set name = ?, email = ?, mobile = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->name,$this->email,$this->mobile,$this->id));
            Database::disconnect();
            header("Location: $this->tableName.php");
        }
        else {
            $this->noerrors = false;
            $this->update_record($id);  // go back to "update" form
        }
    } // end function update_db_record

    function delete_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM $this->tableName WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        header("Location: $this->tableName.php");
    } // end function delete_db_record()

    function upload_a_file($id) {
        header("Location:upload03.php");
        $fileName = $_POST['Filename'];
        $pdo = Database::connect();
        $sql = "INSERT INTO customers (filename, filesize, filetype, filecontents) "
            . "VALUES ('$fileName', '$fileSize', '$fileType', '$content')";
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $q = $pdo->prepare($sql);
        $q->execute(array());

        // disconnect
        Database::disconnect();
        header("Location: customers.php");
    }

    private function generate_html_top ($fun, $id=null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Create a $this->title";
                $funNext = "insert_db_record";
                break;
            case 2: // read
                $funWord = "Read a $this->title";
                $funNext = "none";
                break;
            case 3: // update
                $funWord = "Update a $this->title";
                $funNext = "update_db_record&id=" . $id;
                break;
            case 4: // delete
                $funWord = "Delete a $this->title";
                $funNext = "delete_db_record&id=" . $id;
                break;
            default:
                echo "Error: Invalid function: generate_html_top()";
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord</title>
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
                            <h3>$funWord</h3>
                        </p>
                        <form class='form-horizontal' action='$this->tableName.php?fun=$funNext' method='post'>
                    ";
    } // end function generate_html_top()

    private function generate_html_bottom ($fun) {
        $backButton = "<a class='btn btn-secondary' href='$this->tableName.php'>Back</a>";
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
    } // end function generate_form_group()

    private function fieldsAllValid () {
        $valid = true;
        echo $this->password;
        if (empty($this->name)) {
            $this->nameError = 'Please enter Name';
            $valid = false;
        }
        if (empty($this->email)) {
            $this->emailError = 'Please enter Email Address';
            $valid = false;
        }
        else if ( !filter_var($this->email,FILTER_VALIDATE_EMAIL) ) {
            $this->emailError = 'Please enter a valid email address: me@mydomain.com';
            $valid = false;
        }
        if (empty($this->mobile)) {
            $this->mobileError = 'Please enter Mobile phone number';
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
                    <a class='btn btn-success' href='https://github.com/jawsdaws/CIS355/tree/master/Prog03'>Github Repo</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog03/uml.png'>UML</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog03/read.png'>Read</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog03/update.png'>Update</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog03/delete.png'>Delete</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog03/login.png'>Login</a>
                    <a class='btn btn-success' href='https://csis.svsu.edu/~jpdaws/CIS355/Prog03/logout.png'>Logout</a>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                        <h4>Logged in as " . "$this->username" . "</h4>
                    </p>
                    <p>
                        <a href='$this->tableName.php?fun=display_create_form' class='btn btn-success'>Create</a>
                        <a href='$this->tableName.php?fun=logout' class='btn btn-success'>Logout</a>
                    </p>
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                    <th>Thumb</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM $this->tableName ORDER BY id DESC";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
            echo "<td>". $row["name"] . "</td>";
            echo "<td>". $row["email"] . "</td>";
            echo "<td>". $row["mobile"] . "</td>";
            echo "<td width=250>";
            echo "<a class='btn btn-info' href='$this->tableName.php?fun=display_read_form&id=".$row["id"]."'>Read</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-warning' href='$this->tableName.php?fun=display_update_form&id=".$row["id"]."'>Update</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->tableName.php?fun=display_delete_form&id=".$row["id"]."'>Delete</a>";
            echo "&nbsp;";
            echo "<br>";
            echo "<a class='btn btn-danger' href='$this->tableName.php?fun=display_upload_form&id=".$row["id"]."'>Upload</a>";
            echo "</td>";
            echo "<td><img width=100 src='data:image/jpeg;base64," . base64_encode( $row['filecontents'] ) . "' />";
            echo "<br><a href='" . $row['description'] . "' >" . $row['description'] . "</a> </td>";
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
?>
