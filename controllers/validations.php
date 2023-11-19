<?php

session_start();
global $connection;
global $email_to_reset;
global $code;
include "../settings/db_connection.php";

//These Validations are used to validate the inputs on the Index (Login Page)
if (!empty($_POST["login_button"])){

    if(!empty($_POST["email"]) and (!empty($_POST["password"]))){
        $correo = $connection->real_escape_string($_POST["email"]);
        $contrasena = $connection->real_escape_string($_POST["password"]);

        $check_email = $connection->query("SELECT * FROM Usuarios WHERE correo='$correo'");

        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        }else {
            echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
        }

        if(mysqli_num_rows($check_email) > 0){
            $fetch = mysqli_fetch_assoc($check_email);
            $fetch_password = $fetch['contrasena'];
            if(password_verify($contrasena, $fetch_password)){
                $check_email_for_password = $connection->query("SELECT * FROM Usuarios WHERE correo='$correo'");
                if ($datos=$check_email_for_password->fetch_object()) {
                    $_SESSION["id"] = $datos->id;
                    $_SESSION["nombre"] = $datos->nombre;
                    $_SESSION["apellido"] = $datos->apellido;
                    header("location: screens/home.php");
                }
            }else{
                echo "<div class= 'alert alert-danger'>Incorrect Email or Password!</div>";
            }
        }else{
            echo "<div class= 'alert alert-danger'>Incorrect Email or Password!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please enter a value for Email and Password!</div>";
    }
}


require '../vendor/autoload.php';
use \SendGrid\Mail\Mail;

//This Validation is used for the Reset Password Page
if (!empty($_POST["reset_password_button"])) {
    if (!empty($_POST["email"])) {
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $email_to_reset = $connection->real_escape_string($_POST["email"]);
            $email_to_reset_check = $connection->query("SELECT * FROM Usuarios WHERE correo='$email_to_reset'");
        }else {
            echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
        }
        if ($datos = $email_to_reset_check->fetch_object()) {
            echo "<div class= 'alert alert-success'>Recovery code sent to your email! Please check your Inboxes.</div>";
            try {
                $code = $connection->real_escape_string(bin2hex(random_bytes(5)));
            } catch (Exception $e) {
            }

            $insert_code = "UPDATE Usuarios SET codigo = '$code' WHERE correo='$email_to_reset'";
            $run_query = mysqli_query($connection, $insert_code);
            if ($run_query) {
                $ConfigFile = parse_ini_file(realpath("../settings/config.ini"), true);
                $sendgrid = new SendGrid($ConfigFile['SendGrid']['apikey']);
                $email = new Mail();
                $email->addTo("{$email_to_reset}", "Usuario");
                $email->setFrom("farmaciasemg@gmail.com", "HG Pharmacy");
                $email->setSubject("Password Recovery Code");
                $email->AddContent("text/html","<strong>Please use the following code to Reset your Password: {$code}</strong>");
                $sendgrid->send($email);
                header( "refresh:5;url=../screens/code_validation.php" );
            } else {
                echo "<div class= 'alert alert-danger'>There has been a problem sending your code! Please try again later.</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Incorrect Email!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
    }
}


//This Validation is used for the addition of new users
if (!empty($_POST["save_user_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["password1"])) and (!empty($_POST["password2"])) and (!empty($_POST["roles"]))){
        $name = $connection->real_escape_string($_POST['name']);
        $lastname = $connection->real_escape_string($_POST['lastname']);
        $email = $connection->real_escape_string($_POST['email']);
        $password1 = $connection->real_escape_string($_POST['password1']);
        $password2 = $connection->real_escape_string($_POST['password2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);
        $roles = $connection->real_escape_string($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)){
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)){
                if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    if (strlen($password1) >= 8) {
                        if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                            if ($password1 !== $password2) {
                                echo "<div class= 'alert alert-danger'>Passwords entered do not match!</div>";
                            } else {
                                $check_correo = $connection->query("SELECT * FROM Usuarios WHERE correo='$email'");
                                if (mysqli_num_rows($check_correo) > 0){
                                    echo "<div class= 'alert alert-danger'>Email is already in use by another account!</div>";
                                }else{
                                    function generarID($conexion) {
                                        $numeroID = rand(1000, 999999);
                                        $query = "SELECT * FROM Usuarios WHERE id = $numeroID";
                                        $result = $conexion->query($query);

                                        if ($result->num_rows > 0) {
                                            return generarID($conexion);
                                        } else {
                                            return $numeroID;
                                        }
                                    }
                                    $numeroID = generarID($connection);
                                    $insert_user = "INSERT INTO Usuarios (id,nombre,apellido,correo,contrasena,roles) VALUES ('$numeroID','$name','$lastname','$email','$crypt_password','$roles')";
                                    $response = mysqli_query($connection, $insert_user);

                                    if ($response === TRUE) {
                                        echo "<div class= 'alert alert-success'>New user added Successfully!</div>";
                                    } else {
                                        echo "<div class= 'alert alert-danger'>An Error was generated when Adding the User!</div>";
                                    }
                                }
                            }
                        } else {
                            echo "<div class= 'alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>Password most have at least 8 characters!</div>";
                    }
                }else{
                    echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Please enter a valid value for Last Name!</div>";
            }
        }else{
            echo "<div class= 'alert alert-danger'>Please enter a valid value for Name!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill the blanks with the requested info!</div>";
    }
}


//This validation is used to verify if the code provided by the user is the same we currently have on database
if (!empty($_POST["verify_code_button"])) {
    if (!empty($_POST["code"])) {
        $codigo = $connection->real_escape_string($_POST["code"]);
        $sql = $connection->query("SELECT * FROM Usuarios WHERE codigo='$codigo'");
        if ($datos = $sql->fetch_object()) {
            $_SESSION["codigo"]=$datos->codigo;
            echo "<div class= 'alert alert-success'>Code is Correct! Let's change your password!</div>";
            header( "refresh:3;url=../screens/new_password.php" );

        } else {
            echo "<div class= 'alert alert-danger'>Code is Incorrect!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill the blank with the Code!</div>";
    }
}


//THis validation is currently used for sending the Code again to the user
if (!empty($_POST["resend_code_button"])) {
    if (empty($_POST["code"])) {
        echo "<div class= 'alert alert-success'>We have resent the code to your email! Please check your Inboxes.</div>";
        $ConfigFile = parse_ini_file(realpath("../settings/config.ini"), true);
        $sendgrid = new SendGrid($ConfigFile['SendGrid']['apikey']);
        $email = new Mail();
        $email->addTo("{$email_to_reset}", "Usuario");
        $email->setFrom("farmaciasemg@gmail.com", "HG Pharmacy");
        $email->setSubject("Password Recovery Code");
        $email->AddContent("text/html","<strong>Please use the following code to Reset your Password: {$code}</strong>");
        $sendgrid->send($email);
    }
}


//This Validation is used to confirm the password change when user wants to reset password through login
if (!empty($_POST["user_password_confirmation_button"])) {
    if (!empty($_POST["newpassword1"]) and (!empty($_POST["newpassword2"]))) {

        $code = $connection->real_escape_string(0);
        $codigo = $_SESSION['codigo'];

        $password1 = $connection->real_escape_string($_POST['newpassword1']);
        $password2 = $connection->real_escape_string($_POST['newpassword2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

        if (strlen($password1) >= 8) {
            if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                if ($password1 !== $password2) {
                    echo "<div class= 'alert alert-danger'>Passwords entered do not match!</div>";
                } else {
                    $update_password = "UPDATE Usuarios SET codigo = $code, contrasena = '$crypt_password' WHERE codigo = '$codigo'";
                    $run_query = mysqli_query($connection, $update_password);
                    if ($run_query) {
                        echo "<div class= 'alert alert-success'>Password changed Successfully!</div>";
                        session_start();
                        session_destroy();
                        header("refresh:3;url=../index.php");
                    } else {
                        echo "<div class= 'alert alert-danger'>An Error occurred while changing your password! Please try again later.</div>";
                    }
                }
            }else {
                echo "<div class= 'alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
            }
        } else{
            echo "<div class= 'alert alert-danger'>Password most have at least 8 characters!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill the blanks with a valid password!</div>";
    }
}


//This Validation is used to Change users password throught account settings
if (!empty($_POST["user_password_change_button"])) {
    if ((!empty($_POST["currentpassword"])) and (!empty($_POST["newpassword1"])) and (!empty($_POST["newpassword2"]))) {
        $id_connected = $_POST['id_user'];
        $currentpassword = $connection->real_escape_string($_POST["currentpassword"]);
        $password1 = $connection->real_escape_string($_POST['newpassword1']);
        $password2 = $connection->real_escape_string($_POST['newpassword2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

        $check_password = $connection->query("SELECT * FROM Usuarios WHERE id='$id_connected'");
        $fetch = mysqli_fetch_assoc($check_password);
        $fetch_password = $fetch['contrasena'];

        if(password_verify($currentpassword, $fetch_password)){
            $check_password2 = $connection->query("SELECT * FROM Usuarios WHERE id='$id_connected'");
            if ($datos=$check_password2->fetch_object()) {
                if (strlen($password1) >= 8) {
                    if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                        if ($password1 !== $password2) {
                            echo "<div class= 'alert alert-danger'>Passwords entered do not match!</div>";
                        } else {
                            $update_password = "UPDATE Usuarios SET contrasena = '$crypt_password' WHERE id = '$id_connected'";
                            $run_query = mysqli_query($connection, $update_password);
                            if ($run_query) {
                                echo "<div class= 'alert alert-success'>Password changed Successfully!</div>";
                                header("refresh:3;url=../screens/home.php");
                            } else {
                                echo "<div class= 'alert alert-danger'>An Error occurred while changing your password! Please try again later.</div>";
                            }
                        }
                    }else {
                        echo "<div class= 'alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
                    }
                } else{
                    echo "<div class= 'alert alert-danger'>Password most have at least 8 characters!</div>";
                }
            }
        }else{
            echo "<div class= 'alert alert-danger'>Current Password is Incorrect!</div>";
        }

    }else{
        echo "<div class= 'alert alert-danger'>Please fill the blanks with a valid password!</div>";
    }
}


//This Validation is used to save a new product to the inventory
if (!empty($_POST["save_product_button"])) {
    if ((!empty($_POST["numero"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))){
        $number = $connection->real_escape_string($_POST["numero"]);
        $description = $connection->real_escape_string($_POST["description"]);
        $name = $connection->real_escape_string($_POST["name"]);
        $quantity = $connection->real_escape_string($_POST["quantity"]);
        $price = $connection->real_escape_string($_POST["price"]);
        $presentation = $connection->real_escape_string($_POST["presentation"]);
        $expiration_date = $connection->real_escape_string($_POST["expiration_date"]);
        $administration_form = $connection->real_escape_string($_POST["administration_form"]);
        $storage = $connection->real_escape_string($_POST["storage"]);

        $check_number = $connection->query("SELECT * FROM Inventario WHERE id_producto='$number'");
        if (mysqli_num_rows($check_number) > 0){
            echo "<div class= 'alert alert-danger'>Product Number is already assigned to another Product!</div>";
        }else{
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)){
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u",$description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($quantity) <= 6 && preg_match("/^\d+$/", $quantity)){
                            if(strlen($price) <= 9 && preg_match("/^\d+(\.\d+)?$/", $price)){
                                if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$presentation)) {
                                    if (preg_match("/^(?:\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})$/",$expiration_date)) {
                                        if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$administration_form)) {
                                            if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$storage)) {
                                                $insert_product = "INSERT INTO Inventario (id_producto,nombre_producto,descripcion,existencia_producto,precio,presentacion_producto,fecha_vencimiento,forma_administracion,almacenamiento) VALUES ('$number','$name','$description','$quantity','$price','$presentation','$expiration_date','$administration_form','$storage')";
                                                $response = mysqli_query($connection, $insert_product);
                                                if ($response === TRUE) {
                                                    echo "<div class= 'alert alert-success'>New Product added Succesfully!</div>";
                                                } else {
                                                    echo "<div class= 'alert alert-danger'>An Error has occured while adding the Product! Please try again later.</div>";
                                                }
                                            }else{
                                                echo "<div class= 'alert alert-danger'>Storage information is not Valid!</div>";
                                            }
                                        }else{
                                            echo "<div class= 'alert alert-danger'>Way of Administration is not Valid!</div>";
                                        }

                                    }else {
                                        echo "<div class= 'alert alert-danger'>Expiration Date is not Valid!</div>";
                                    }
                                }else{
                                    echo "<div class= 'alert alert-danger'>Presentation type is not Valid!</div>";
                                }

                            }else{
                                echo "<div class= 'alert alert-danger'>Price is not Valid!</div>";
                            }

                        }else{
                            echo "<div class= 'alert alert-danger'>Existence is not Valid!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>Product Name is too large, or it may contain invalid characters!</div>";
                    }
                }else{
                    echo "<div class= 'alert alert-danger'>Description is too large, or it may contain invalid characters!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Product Number is Invalid, or it contains more than 6 digits</div>";
            }
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This Validation is used for editing products
if (!empty($_POST["edit_product_button"])){
    if ((!empty($_POST["number"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))){
        $id=$_POST['id_producto'];
        $number = $connection->real_escape_string($_POST["number"]);
        $description = $connection->real_escape_string($_POST["description"]);
        $name = $connection->real_escape_string($_POST["name"]);
        $quantity = $connection->real_escape_string($_POST["quantity"]);
        $price = $connection->real_escape_string($_POST["price"]);
        $presentation = $connection->real_escape_string($_POST["presentation"]);
        $expiration_date = $connection->real_escape_string($_POST["expiration_date"]);
        $administration_form = $connection->real_escape_string($_POST["administration_form"]);
        $storage = $connection->real_escape_string($_POST["storage"]);

        $check_number = $connection->query("SELECT * FROM Inventario WHERE id_producto='$number'");
        if (mysqli_num_rows($check_number) > 0 && ($_GET['id_producto'] != $number)){
            echo "<div class= 'alert alert-danger'>Product Number is already assigned to another Product!</div>";
        }else{
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)){
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u",$description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($quantity) <= 6 && preg_match("/^\d+$/", $quantity)){
                            if(strlen($price) <= 9 && preg_match("/^\d+(\.\d+)?$/", $price)){
                                if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$presentation)) {
                                    if (preg_match("/^(?:\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})$/",$expiration_date)) {
                                        if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$administration_form)) {
                                            if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$storage)) {
                                                $edit_product=("UPDATE Inventario SET id_producto='$number',nombre_producto='$name',descripcion='$description',existencia_producto='$quantity',precio='$price',presentacion_producto='$presentation',fecha_vencimiento='$expiration_date',forma_administracion='$administration_form',almacenamiento='$storage' WHERE id_producto='$id'");
                                                $response = mysqli_query($connection, $edit_product);
                                                if ($response === TRUE) {
                                                    echo "<div class= 'alert alert-success'>Product editted Succesfully!</div>";
                                                    header("refresh:3;url=inventory_control.php");
                                                } else {
                                                    echo "<div class= 'alert alert-danger'>An Error has occured while editing the Product! Please try again later.</div>";
                                                }
                                            }else{
                                                echo "<div class= 'alert alert-danger'>Storage information is not Valid!</div>";
                                            }
                                        }else{
                                            echo "<div class= 'alert alert-danger'>Way of Administration is not Valid!</div>";
                                        }
                                    }else {
                                        echo "<div class= 'alert alert-danger'>Expiration Date is not Valid!</div>";
                                    }
                                }else{
                                    echo "<div class= 'alert alert-danger'>Presentation type is not Valid!</div>";
                                }
                            }else{
                                echo "<div class= 'alert alert-danger'>Price is not Valid!</div>";
                            }
                        }else{
                            echo "<div class= 'alert alert-danger'>Existence is not Valid!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>Product Name is too large, or it may contain invalid characters!</div>";
                    }
                }else{
                    echo "<div class= 'alert alert-danger'>Description is too large, or it may contain invalid characters!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Product Number is Invalid, or it contains more than 6 digits</div>";
            }
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This validation is used to edit a user from the user mangament
if (!empty($_POST["edit_user_button"])){
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["roles"]))){
        $id=$_POST['id_user'];
        $name = $connection->real_escape_string($_POST['name']);
        $lastname = $connection->real_escape_string($_POST['lastname']);
        $email = $connection->real_escape_string($_POST['email']);
        $roles = $connection->real_escape_string($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)){
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $edit=$connection->query("UPDATE Usuarios SET nombre='$name',apellido='$lastname',correo='$email',roles='$roles' WHERE id='$id'");

                    if ($edit == 1){
                        echo "<div class= 'alert alert-success'>User has been modified succesfully!</div>";
                        header( "refresh:3;url=user_management.php" );
                    }else{
                        echo "<div class= 'alert alert-danger'>An Error has occurred while trying to modify this user! Please try again later.</div>";
                    }

                }else{
                    echo "<div class= 'alert alert-danger'>Email is not valid!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Last Name is not valid!</div>";
            }

        }else{
            echo "<div class= 'alert alert-danger'>Name is not valid!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


// This Validation is used to edit the users account from the account settings
if (!empty($_POST["edit_user_settings_button"])){
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"]))){
        $id=$_POST['id_user'];
        $name = $connection->real_escape_string($_POST['name']);
        $lastname = $connection->real_escape_string($_POST['lastname']);
        $email = $connection->real_escape_string($_POST['email']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)){
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)){
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $edit=$connection->query("UPDATE Usuarios SET nombre='$name',apellido='$lastname',correo='$email' WHERE id='$id'");

                    if ($edit == 1){
                        echo "<div class= 'alert alert-success'>User has been modified sucessfully!</div>";
                        header( "refresh:3;url=home.php" );
                    }else{
                        echo "<div class= 'alert alert-danger'>An Error has ocurred while editing this user! Please try again later.</div>";
                    }

                }else{
                    echo "<div class= 'alert alert-danger'>Email is not valid!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>Last Name is not valid!</div>";
            }

        }else{
            echo "<div class= 'alert alert-danger'>Name is not valid!</div>";
        }
    }else{
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This Validation is used for the Billing Module (Currently in progress)
if (!empty($_POST["new_billing_button"])) {
    if ((!empty($_POST["numero"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["packing"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))){
        $number = $connection->real_escape_string($_POST["numero"]);
        $description = $connection->real_escape_string($_POST["description"]);
        $name = $connection->real_escape_string($_POST["name"]);
        $quantity = $connection->real_escape_string($_POST["quantity"]);
        $packing = $connection->real_escape_string($_POST["packing"]);
        $price = $connection->real_escape_string($_POST["price"]);
        $presentation = $connection->real_escape_string($_POST["presentation"]);
        $expiration_date = $connection->real_escape_string($_POST["expiration_date"]);
        $administration_form = $connection->real_escape_string($_POST["administration_form"]);
        $storage = $connection->real_escape_string($_POST["storage"]);

        $check_number = $connection->query("SELECT * FROM Inventario WHERE id_producto='$number'");
        if (mysqli_num_rows($check_number) > 0){
            echo "<div class= 'alert alert-danger'>El Número de Producto ingresado se encuentra asignado a otro producto!</div>";
        }else{
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)){
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u",$description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($quantity) <= 6 && preg_match("/^\d+$/", $quantity)){
                            if (strlen($packing) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$packing)){
                                if(strlen($price) <= 9 && preg_match("/^\d+(\.\d+)?$/", $price)){
                                    if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$presentation)) {
                                        if (preg_match("/^(?:\d{4}-\d{2}-\d{2}|\d{2}\/\d{2}\/\d{4})$/",$expiration_date)) {
                                            if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$administration_form)) {
                                                if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u",$storage)) {
                                                    $insert_product = "INSERT INTO Inventario (id_producto,nombre_producto,descripcion,cantidad_producto,empaque_producto,precio,presentacion_producto,fecha_vencimiento,forma_administracion,almacenamiento) VALUES ('$number','$name','$description','$quantity','$packing','$price','$presentation','$expiration_date','$administration_form','$storage')";
                                                    $response = mysqli_query($connection, $insert_product);
                                                    if ($response === TRUE) {
                                                        echo "<div class= 'alert alert-success'>Se ha Agregado un Nuevo Producto Correctamente!</div>";
                                                    } else {
                                                        echo "<div class= 'alert alert-danger'>Se ha generado un Error al Agregar el Producto!</div>";
                                                    }
                                                }else{
                                                    echo "<div class= 'alert alert-danger'>Por favor ingrese una Forma de Almacenamiento Válida!</div>";
                                                }
                                            }else{
                                                echo "<div class= 'alert alert-danger'>Por favor ingrese una Forma de Administración Válida!</div>";
                                            }

                                        }else {
                                            echo "<div class= 'alert alert-danger'>Por favor ingrese una Fecha de Vencimiento Válida!</div>";
                                        }
                                    }else{
                                        echo "<div class= 'alert alert-danger'>Por favor ingrese un tipo de Presentación Válido!</div>";
                                    }

                                }else{
                                    echo "<div class= 'alert alert-danger'>Por favor ingrese un Precio Válido!</div>";
                                }
                            }else{
                                echo "<div class= 'alert alert-danger'>Por favor ingrese un tipo de Empaque Válido!</div>";
                            }

                        }else{
                            echo "<div class= 'alert alert-danger'>Por favor ingrese una Cantidad Válida!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>El nombre es demasiado extenso o contiene caracteres invalidos, Porfavor ingrese un Nombre Válido!</div>";
                    }
                }else{
                    echo "<div class= 'alert alert-danger'>La Descripción es demasiado extensa o Contiene Caracteres Invalidos, Porfavor intente nuevamente!</div>";
                }
            }else{
                echo "<div class= 'alert alert-danger'>El Número de Producto es Inválido o contiene mas de 6 dígitos!</div>";
            }
        }
    }else{
        echo "<div class= 'alert alert-danger'>Porfavor Rellene Todos los Campos e Intentelo Nuevamente!</div>";
    }
}