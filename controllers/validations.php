<?php

session_start();
global $connection;
global $email_to_reset;
global $code;
include "../settings/db_connection.php";

//These Validations are used to validate the inputs on the Index (Login Page)
if (!empty($_POST["login_button"])) {

    if (!empty($_POST["email"]) and (!empty($_POST["password"]))) {
        $correo = $connection->real_escape_string($_POST["email"]);
        $contrasena = $connection->real_escape_string($_POST["password"]);

        $check_email = $connection->query("SELECT * FROM Usuarios WHERE correo='$correo'");

        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        } else {
            echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
        }

        if (mysqli_num_rows($check_email) > 0) {
            $fetch = mysqli_fetch_assoc($check_email);
            $fetch_password = $fetch['contrasena'];
            if (password_verify($contrasena, $fetch_password)) {
                $check_email_for_password = $connection->query("SELECT * FROM Usuarios WHERE correo='$correo'");
                if ($data = $check_email_for_password->fetch_object()) {
                    $_SESSION["id"] = $data->id;
                    $_SESSION["nombre"] = $data->nombre;
                    $_SESSION["apellido"] = $data->apellido;
                    header("location: screens/home.php");
                }
            } else {
                echo "<div class= 'alert alert-danger'>Incorrect Email or Password!</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Incorrect Email or Password!</div>";
        }
    } else {
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
        } else {
            echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
        }
        if ($data = $email_to_reset_check->fetch_object()) {
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
                $email->AddContent("text/html", "<strong>Please use the following code to Reset your Password: {$code}</strong>");
                $sendgrid->send($email);
                header("refresh:5;url=../screens/code_validation.php");
            } else {
                echo "<div class= 'alert alert-danger'>There has been a problem sending your code! Please try again later.</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Incorrect Email!</div>";
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
    }
}


//This Validation is used for the addition of new users
if (!empty($_POST["save_user_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["password1"])) and (!empty($_POST["password2"])) and (!empty($_POST["roles"]))) {
        $name = $connection->real_escape_string($_POST['name']);
        $lastname = $connection->real_escape_string($_POST['lastname']);
        $email = $connection->real_escape_string($_POST['email']);
        $password1 = $connection->real_escape_string($_POST['password1']);
        $password2 = $connection->real_escape_string($_POST['password2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);
        $roles = $connection->real_escape_string($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)) {
                if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    if (strlen($password1) >= 8) {
                        if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                            if ($password1 !== $password2) {
                                echo "<div class= 'alert alert-danger'>Passwords entered do not match!</div>";
                            } else {
                                $check_correo = $connection->query("SELECT * FROM Usuarios WHERE correo='$email'");
                                if (mysqli_num_rows($check_correo) > 0) {
                                    echo "<div class= 'alert alert-danger'>Email is already in use by another account!</div>";
                                } else {
                                    function generarID($connection)
                                    {
                                        $numeroID = rand(1000, 999999);
                                        $query = "SELECT * FROM Usuarios WHERE id = $numeroID";
                                        $result = $connection->query($query);

                                        if ($result->num_rows > 0) {
                                            return generarID($connection);
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
                } else {
                    echo "<div class= 'alert alert-danger'>Please enter a valid value for Email!</div>";
                }
            } else {
                echo "<div class= 'alert alert-danger'>Please enter a valid value for Last Name!</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Please enter a valid value for Name!</div>";
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill the blanks with the requested info!</div>";
    }
}


//This validation is used to verify if the code provided by the user is the same we currently have on database
if (!empty($_POST["verify_code_button"])) {
    if (!empty($_POST["code"])) {
        $codigo = $connection->real_escape_string($_POST["code"]);
        $sql = $connection->query("SELECT * FROM Usuarios WHERE codigo='$codigo'");
        if ($data = $sql->fetch_object()) {
            $_SESSION["codigo"] = $data->codigo;
            echo "<div class= 'alert alert-success'>Code is Correct! Let's change your password!</div>";
            header("refresh:3;url=../screens/new_password.php");
        } else {
            echo "<div class= 'alert alert-danger'>Code is Incorrect!</div>";
        }
    } else {
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
        $email->AddContent("text/html", "<strong>Please use the following code to Reset your Password: {$code}</strong>");
        $sendgrid->send($email);
    }
}


//This Validation is used to confirm the password change when user wants to reset password through login
if (!empty($_POST["user_password_confirmation_button"])) {
    if (!empty($_POST["newPassword1"]) and (!empty($_POST["newPassword2"]))) {

        $code = $connection->real_escape_string(0);
        $codigo = $_SESSION['codigo'];

        $password1 = $connection->real_escape_string($_POST['newPassword1']);
        $password2 = $connection->real_escape_string($_POST['newPassword2']);
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
            } else {
                echo "<div class= 'alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Password most have at least 8 characters!</div>";
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill the blanks with a valid password!</div>";
    }
}


//This Validation is used to Change users password thought account settings
if (!empty($_POST["user_password_change_button"])) {
    if ((!empty($_POST["currentPassword"])) and (!empty($_POST["newPassword1"])) and (!empty($_POST["newPassword2"]))) {
        $id_connected = $_POST['id_user'];
        $currentPassword = $connection->real_escape_string($_POST["currentPassword"]);
        $password1 = $connection->real_escape_string($_POST['newPassword1']);
        $password2 = $connection->real_escape_string($_POST['newPassword2']);
        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

        $check_password = $connection->query("SELECT * FROM Usuarios WHERE id='$id_connected'");
        $fetch = mysqli_fetch_assoc($check_password);
        $fetch_password = $fetch['contrasena'];

        if (password_verify($currentPassword, $fetch_password)) {
            $check_password2 = $connection->query("SELECT * FROM Usuarios WHERE id='$id_connected'");
            if ($data = $check_password2->fetch_object()) {
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
                    } else {
                        echo "<div class= 'alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
                    }
                } else {
                    echo "<div class= 'alert alert-danger'>Password most have at least 8 characters!</div>";
                }
            }
        } else {
            echo "<div class= 'alert alert-danger'>Current Password is Incorrect!</div>";
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill the blanks with a valid password!</div>";
    }
}


//This Validation is used to save a new product to the inventory
if (!empty($_POST["save_product_button"])) {
    if ((!empty($_POST["numero"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))) {
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
        if (mysqli_num_rows($check_number) > 0) {
            echo "<div class= 'alert alert-danger'>Product Number is already assigned to another Product!</div>";
        } else {
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)) {
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u", $description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($quantity) <= 6 && preg_match("/^\d+$/", $quantity)) {
                            if (strlen($price) <= 9 && preg_match("/^\d+(\.\d+)?$/", $price)) {
                                if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $presentation)) {
                                    if (preg_match("/^(?:\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})$/", $expiration_date)) {
                                        if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $administration_form)) {
                                            if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $storage)) {
                                                $insert_product = "INSERT INTO Inventario (id_producto,nombre_producto,descripcion,cantidad_producto,precio,presentacion_producto,fecha_vencimiento,forma_administracion,almacenamiento) VALUES ('$number','$name','$description','$quantity','$price','$presentation','$expiration_date','$administration_form','$storage')";
                                                $response = mysqli_query($connection, $insert_product);
                                                if ($response === TRUE) {
                                                    echo "<div class= 'alert alert-success'>New Product added Successfully!</div>";
                                                } else {
                                                    echo "<div class= 'alert alert-danger'>An Error has occurred while adding the Product! Please try again later.</div>";
                                                }
                                            } else {
                                                echo "<div class= 'alert alert-danger'>Storage information is not Valid!</div>";
                                            }
                                        } else {
                                            echo "<div class= 'alert alert-danger'>Way of Administration is not Valid!</div>";
                                        }
                                    } else {
                                        echo "<div class= 'alert alert-danger'>Expiration Date is not Valid!</div>";
                                    }
                                } else {
                                    echo "<div class= 'alert alert-danger'>Presentation type is not Valid!</div>";
                                }
                            } else {
                                echo "<div class= 'alert alert-danger'>Price is not Valid!</div>";
                            }
                        } else {
                            echo "<div class= 'alert alert-danger'>Existence is not Valid!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>Product Name is too large, or it may contain invalid characters!</div>";
                    }
                } else {
                    echo "<div class= 'alert alert-danger'>Description is too large, or it may contain invalid characters!</div>";
                }
            } else {
                echo "<div class= 'alert alert-danger'>Product Number is Invalid, or it contains more than 6 digits</div>";
            }
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This Validation is used for editing products
if (!empty($_POST["edit_product_button"])) {
    if ((!empty($_POST["number"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))) {
        $id = $_POST['id_producto'];
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
        if (mysqli_num_rows($check_number) > 0 && ($_GET['id_producto'] != $number)) {
            echo "<div class= 'alert alert-danger'>Product Number is already assigned to another Product!</div>";
        } else {
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)) {
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u", $description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($quantity) <= 6 && preg_match("/^\d+$/", $quantity)) {
                            if (strlen($price) <= 9 && preg_match("/^\d+(\.\d+)?$/", $price)) {
                                if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $presentation)) {
                                    if (preg_match("/^(?:\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})$/", $expiration_date)) {
                                        if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $administration_form)) {
                                            if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $storage)) {
                                                $edit_product = ("UPDATE Inventario SET id_producto='$number',nombre_producto='$name',descripcion='$description',cantidad_producto='$quantity',precio='$price',presentacion_producto='$presentation',fecha_vencimiento='$expiration_date',forma_administracion='$administration_form',almacenamiento='$storage' WHERE id_producto='$id'");
                                                $response = mysqli_query($connection, $edit_product);
                                                if ($response === TRUE) {
                                                    echo "<div class= 'alert alert-success'>Product edited Successfully!</div>";
                                                    header("refresh:3;url=inventory_control.php");
                                                } else {
                                                    echo "<div class= 'alert alert-danger'>An Error has occurred while editing the Product! Please try again later.</div>";
                                                }
                                            } else {
                                                echo "<div class= 'alert alert-danger'>Storage information is not Valid!</div>";
                                            }
                                        } else {
                                            echo "<div class= 'alert alert-danger'>Way of Administration is not Valid!</div>";
                                        }
                                    } else {
                                        echo "<div class= 'alert alert-danger'>Expiration Date is not Valid!</div>";
                                    }
                                } else {
                                    echo "<div class= 'alert alert-danger'>Presentation type is not Valid!</div>";
                                }
                            } else {
                                echo "<div class= 'alert alert-danger'>Price is not Valid!</div>";
                            }
                        } else {
                            echo "<div class= 'alert alert-danger'>Existence is not Valid!</div>";
                        }
                    } else {
                        echo "<div class= 'alert alert-danger'>Product Name is too large, or it may contain invalid characters!</div>";
                    }
                } else {
                    echo "<div class= 'alert alert-danger'>Description is too large, or it may contain invalid characters!</div>";
                }
            } else {
                echo "<div class= 'alert alert-danger'>Product Number is Invalid, or it contains more than 6 digits</div>";
            }
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This validation is used to edit a user from the user mangament
if (!empty($_POST["edit_user_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["roles"]))) {
        $id = $_POST['id_user'];
        $name = $connection->real_escape_string($_POST['name']);
        $lastname = $connection->real_escape_string($_POST['lastname']);
        $email = $connection->real_escape_string($_POST['email']);
        $roles = $connection->real_escape_string($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $edit = $connection->query("UPDATE Usuarios SET nombre='$name',apellido='$lastname',correo='$email',roles='$roles' WHERE id='$id'");

                    if ($edit == 1) {
                        echo "<div class= 'alert alert-success'>User has been modified successfully!</div>";
                        header("refresh:3;url=user_management.php");
                    } else {
                        echo "<div class= 'alert alert-danger'>An Error has occurred while trying to modify this user! Please try again later.</div>";
                    }
                } else {
                    echo "<div class= 'alert alert-danger'>Email is not valid!</div>";
                }
            } else {
                echo "<div class= 'alert alert-danger'>Last Name is not valid!</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Name is not valid!</div>";
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


// This Validation is used to edit the users account from the account settings
if (!empty($_POST["edit_user_settings_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"]))) {
        $id = $_POST['id_user'];
        $name = $connection->real_escape_string($_POST['name']);
        $lastname = $connection->real_escape_string($_POST['lastname']);
        $email = $connection->real_escape_string($_POST['email']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $edit = $connection->query("UPDATE Usuarios SET nombre='$name',apellido='$lastname',correo='$email' WHERE id='$id'");

                    if ($edit == 1) {
                        echo "<div class= 'alert alert-success'>User has been modified successfully!</div>";
                        header("refresh:3;url=home.php");
                    } else {
                        echo "<div class= 'alert alert-danger'>An Error has ocurred while editing this user! Please try again later.</div>";
                    }
                } else {
                    echo "<div class= 'alert alert-danger'>Email is not valid!</div>";
                }
            } else {
                echo "<div class= 'alert alert-danger'>Last Name is not valid!</div>";
            }
        } else {
            echo "<div class= 'alert alert-danger'>Name is not valid!</div>";
        }
    } else {
        echo "<div class= 'alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This Validation is used for the Billing Module - Creates new receipts/invoices
if (!empty($_POST["new_billing_button"])) {
    // Validate required fields for receipt creation
    if (!empty($_POST["name"]) && !empty($_POST["date_time_invoice"]) && !empty($_POST["cashier"]) && !empty($_POST["payment_method"])) {

        try {
            // Start transaction for data integrity
            $connection->begin_transaction();

            // Get and validate form data
            $customer_name = trim($_POST["name"]);
            $rtn = !empty($_POST["rtn"]) ? trim($_POST["rtn"]) : NULL;
            $date_time = trim($_POST["date_time_invoice"]);
            $cashier_name = trim($_POST["cashier"]);
            $payment_method = trim($_POST["payment_method"]);
            $user_id = $_SESSION["id"];

            // Validate customer name
            if (strlen($customer_name) > 255 || !preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜñÑ]+$/u", $customer_name)) {
                throw new Exception("Por favor ingrese un nombre de cliente válido!");
            }

            // Validate RTN if provided
            if ($rtn && !preg_match("/^[0-9-]+$/", $rtn)) {
                throw new Exception("Por favor ingrese un RTN válido!");
            }

            // Validate payment method exists
            $stmt_check_payment = $connection->prepare("SELECT formas_pago FROM Metodos_Pago WHERE formas_pago = ?");
            $stmt_check_payment->bind_param("s", $payment_method);
            $stmt_check_payment->execute();
            $result_payment = $stmt_check_payment->get_result();

            if ($result_payment->num_rows == 0) {
                throw new Exception("Método de pago no válido!");
            }
            $stmt_check_payment->close();

            // Get shopping cart items for this user
            $stmt_cart = $connection->prepare("SELECT producto_id, nombre_producto, cantidad, precio_unitario, subtotal FROM Shopping_Cart WHERE usuario_id = ?");
            $stmt_cart->bind_param("i", $user_id);
            $stmt_cart->execute();
            $cart_result = $stmt_cart->get_result();

            // Check if cart is empty
            if ($cart_result->num_rows == 0) {
                throw new Exception("El carrito está vacío! Por favor agregue productos antes de generar la factura.");
            }

            // Calculate total and prepare cart items
            $cart_items = [];
            $total = 0.00;

            while ($item = $cart_result->fetch_assoc()) {
                $cart_items[] = $item;
                $total += floatval($item['subtotal']);

                // Verify product stock availability
                $stmt_stock = $connection->prepare("SELECT cantidad_producto FROM Inventario WHERE id_producto = ?");
                $stmt_stock->bind_param("i", $item['producto_id']);
                $stmt_stock->execute();
                $stock_result = $stmt_stock->get_result();

                if ($stock_result->num_rows == 0) {
                    throw new Exception("Producto ID " . $item['producto_id'] . " no encontrado en inventario!");
                }

                $stock_data = $stock_result->fetch_assoc();
                if ($stock_data['cantidad_producto'] < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para " . $item['nombre_producto'] . ". Disponible: " . $stock_data['cantidad_producto']);
                }
                $stmt_stock->close();
            }
            $stmt_cart->close();

            // Insert receipt header into Facturas table
            $stmt_factura = $connection->prepare("INSERT INTO Facturas (fecha_hora, cliente, rtn, cajero, usuario_id, estado, metodo_pago, total) VALUES (?, ?, ?, ?, ?, 'Completada', ?, ?)");
            $stmt_factura->bind_param("ssssisd", $date_time, $customer_name, $rtn, $cashier_name, $user_id, $payment_method, $total);

            if (!$stmt_factura->execute()) {
                throw new Exception("Error al crear la factura: " . $stmt_factura->error);
            }

            $factura_id = $connection->insert_id;
            $stmt_factura->close();

            // Insert receipt details and update inventory
            $stmt_details = $connection->prepare("INSERT INTO Factura_Detalles (factura_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_update_stock = $connection->prepare("UPDATE Inventario SET cantidad_producto = cantidad_producto - ? WHERE id_producto = ?");

            foreach ($cart_items as $item) {
                // Insert detail line
                $stmt_details->bind_param("iisidd",
                    $factura_id,
                    $item['producto_id'],
                    $item['nombre_producto'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal']
                );

                if (!$stmt_details->execute()) {
                    throw new Exception("Error al insertar detalles de factura: " . $stmt_details->error);
                }

                // Update inventory stock
                $stmt_update_stock->bind_param("ii", $item['cantidad'], $item['producto_id']);

                if (!$stmt_update_stock->execute()) {
                    throw new Exception("Error al actualizar inventario: " . $stmt_update_stock->error);
                }
            }

            $stmt_details->close();
            $stmt_update_stock->close();

            // Clear shopping cart after successful receipt creation
            $stmt_clear_cart = $connection->prepare("DELETE FROM Shopping_Cart WHERE usuario_id = ?");
            $stmt_clear_cart->bind_param("i", $user_id);
            $stmt_clear_cart->execute();
            $stmt_clear_cart->close();

            // Commit transaction
            $connection->commit();

            echo "<div class='alert alert-success'>¡Factura #" . $factura_id . " generada exitosamente! Total: Lps. " . number_format($total, 2) . "</div>";
            echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";

        } catch (Exception $e) {
            // Rollback transaction on error
            $connection->rollback();
            echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Por favor complete todos los campos requeridos (Nombre del Cliente, Fecha/Hora, Cajero, Método de Pago)!</div>";
    }
}
