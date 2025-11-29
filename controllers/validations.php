<?php

session_start();
global $connection;
global $email_to_reset;
global $code;
include "../settings/db_connection.php";

// Security helper functions
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function generateSecureId($connection) {
    // Use cryptographically secure random number
    $maxAttempts = 100;
    $attempts = 0;

    do {
        $numeroID = random_int(100000, 999999);
        $stmt = $connection->prepare("SELECT id FROM Usuarios WHERE id = ?");
        $stmt->bind_param("i", $numeroID);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        $attempts++;

        if ($attempts >= $maxAttempts) {
            throw new Exception("Unable to generate unique ID after $maxAttempts attempts");
        }
    } while ($exists);

    return $numeroID;
}

function generateSecureCode() {
    return bin2hex(random_bytes(16)); // 32 character hex string
}

// CSRF Token functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Error logging function
function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $logMessage .= " - Context: " . json_encode($context);
    }
    error_log($logMessage);
}

//These Validations are used to validate the inputs on the Index (Login Page)
if (!empty($_POST["login_button"])) {

    if (!empty($_POST["email"]) and (!empty($_POST["password"]))) {
        $correo = trim($_POST["email"]);
        $contrasena = $_POST["password"];

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='alert alert-danger'>Please enter a valid value for Email!</div>";
        } else {
            // Use Prepared Statement
            $stmt = $connection->prepare("SELECT id, nombre, apellido, correo, contrasena, roles FROM Usuarios WHERE correo = ?");
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $data = $result->fetch_object();
                if (password_verify($contrasena, $data->contrasena)) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION["id"] = $data->id;
                    $_SESSION["nombre"] = $data->nombre;
                    $_SESSION["apellido"] = $data->apellido;
                    $_SESSION["roles"] = $data->roles;

                    header("location: screens/home.php");
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>Incorrect Email or Password!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Incorrect Email or Password!</div>";
            }
            $stmt->close();
        }
    } else {
        echo "<div class='alert alert-danger'>Please enter a value for Email and Password!</div>";
    }
}


require '../vendor/autoload.php';

use \SendGrid\Mail\Mail;

//This Validation is used for the Reset Password Page
if (!empty($_POST["reset_password_button"])) {
    if (!empty($_POST["email"])) {
        $email_to_reset = trim($_POST["email"]);

        if (!filter_var($email_to_reset, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='alert alert-danger'>Please enter a valid value for Email!</div>";
        } else {
            // Use Prepared Statement
            $stmt = $connection->prepare("SELECT id, correo FROM Usuarios WHERE correo = ?");
            $stmt->bind_param("s", $email_to_reset);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($data = $result->fetch_object()) {
                echo "<div class='alert alert-success'>Recovery code sent to your email! Please check your Inboxes.</div>";

                try {
                    $code = generateSecureCode();
                    $code_hash = password_hash($code, PASSWORD_DEFAULT);
                    $code_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    // Store hashed code with expiration
                    $stmt_update = $connection->prepare("UPDATE Usuarios SET codigo = ?, codigo_expires = ? WHERE correo = ?");
                    $stmt_update->bind_param("sss", $code_hash, $code_expires, $email_to_reset);

                    if ($stmt_update->execute()) {
                        $ConfigFile = parse_ini_file(realpath("../settings/config.ini"), true);
                        $sendgrid = new SendGrid($ConfigFile['SendGrid']['apikey']);
                        $email = new Mail();
                        $email->addTo($email_to_reset, "Usuario");
                        $email->setFrom("farmaciasemg@gmail.com", "HG Pharmacy");
                        $email->setSubject("Password Recovery Code");
                        $email->AddContent("text/html", "<strong>Please use the following code to Reset your Password: {$code}</strong><br><br>This code expires in 1 hour.");
                        $sendgrid->send($email);

                        // Store email in session for code verification
                        $_SESSION['reset_email'] = $email_to_reset;
                        header("refresh:5;url=../screens/code_validation.php");
                    } else {
                        logError("Failed to update recovery code", ['email' => $email_to_reset]);
                        echo "<div class='alert alert-danger'>There has been a problem sending your code! Please try again later.</div>";
                    }
                    $stmt_update->close();
                } catch (Exception $e) {
                    logError("Error generating recovery code: " . $e->getMessage());
                    echo "<div class='alert alert-danger'>There has been a problem sending your code! Please try again later.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Incorrect Email!</div>";
            }
            $stmt->close();
        }
    } else {
        echo "<div class='alert alert-danger'>Please enter a valid value for Email!</div>";
    }
}


//This Validation is used for the addition of new users
if (!empty($_POST["save_user_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["password1"])) and (!empty($_POST["password2"])) and (!empty($_POST["roles"]))) {
        $name = trim($_POST['name']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];
        $roles = trim($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if (strlen($password1) >= 8) {
                        if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                            if ($password1 !== $password2) {
                                echo "<div class='alert alert-danger'>Passwords entered do not match!</div>";
                            } else {
                                // Use Prepared Statement to check email
                                $stmt_check = $connection->prepare("SELECT id FROM Usuarios WHERE correo = ?");
                                $stmt_check->bind_param("s", $email);
                                $stmt_check->execute();
                                $result = $stmt_check->get_result();

                                if ($result->num_rows > 0) {
                                    echo "<div class='alert alert-danger'>Email is already in use by another account!</div>";
                                } else {
                                    try {
                                        $numeroID = generateSecureId($connection);
                                        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

                                        // Validate role exists
                                        $stmt_role = $connection->prepare("SELECT nombre_rol FROM Roles WHERE nombre_rol = ?");
                                        $stmt_role->bind_param("s", $roles);
                                        $stmt_role->execute();
                                        $role_result = $stmt_role->get_result();

                                        if ($role_result->num_rows == 0) {
                                            echo "<div class='alert alert-danger'>Invalid role selected!</div>";
                                        } else {
                                            // Use Prepared Statement to insert user
                                            $stmt_insert = $connection->prepare("INSERT INTO Usuarios (id, nombre, apellido, correo, contrasena, roles) VALUES (?, ?, ?, ?, ?, ?)");
                                            $stmt_insert->bind_param("isssss", $numeroID, $name, $lastname, $email, $crypt_password, $roles);

                                            if ($stmt_insert->execute()) {
                                                echo "<div class='alert alert-success'>New user added Successfully!</div>";
                                            } else {
                                                logError("Failed to insert user", ['email' => $email]);
                                                echo "<div class='alert alert-danger'>An Error was generated when Adding the User!</div>";
                                            }
                                            $stmt_insert->close();
                                        }
                                        $stmt_role->close();
                                    } catch (Exception $e) {
                                        logError("Error creating user: " . $e->getMessage());
                                        echo "<div class='alert alert-danger'>An Error was generated when Adding the User!</div>";
                                    }
                                }
                                $stmt_check->close();
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Password most have at least 8 characters!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Please enter a valid value for Email!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Please enter a valid value for Last Name!</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Please enter a valid value for Name!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill the blanks with the requested info!</div>";
    }
}


//This validation is used to verify if the code provided by the user is the same we currently have on database
if (!empty($_POST["verify_code_button"])) {
    if (!empty($_POST["code"])) {
        $codigo = trim($_POST["code"]);
        $reset_email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';

        if (empty($reset_email)) {
            echo "<div class='alert alert-danger'>Session expired. Please request a new code!</div>";
        } else {
            // Get user with the email from session
            $stmt = $connection->prepare("SELECT id, codigo, codigo_expires FROM Usuarios WHERE correo = ?");
            $stmt->bind_param("s", $reset_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($data = $result->fetch_object()) {
                // Check if code is expired
                if (strtotime($data->codigo_expires) < time()) {
                    echo "<div class='alert alert-danger'>Code has expired! Please request a new one.</div>";
                } elseif (password_verify($codigo, $data->codigo)) {
                    $_SESSION["codigo_verified"] = true;
                    $_SESSION["reset_user_id"] = $data->id;
                    echo "<div class='alert alert-success'>Code is Correct! Let's change your password!</div>";
                    header("refresh:3;url=../screens/new_password.php");
                } else {
                    echo "<div class='alert alert-danger'>Code is Incorrect!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Code is Incorrect!</div>";
            }
            $stmt->close();
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill the blank with the Code!</div>";
    }
}


//This validation is currently used for sending the Code again to the user
if (!empty($_POST["resend_code_button"])) {
    if (empty($_POST["code"])) {
        $reset_email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';

        if (!empty($reset_email)) {
            try {
                $code = generateSecureCode();
                $code_hash = password_hash($code, PASSWORD_DEFAULT);
                $code_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $stmt = $connection->prepare("UPDATE Usuarios SET codigo = ?, codigo_expires = ? WHERE correo = ?");
                $stmt->bind_param("sss", $code_hash, $code_expires, $reset_email);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>We have resent the code to your email! Please check your Inboxes.</div>";
                    $ConfigFile = parse_ini_file(realpath("../settings/config.ini"), true);
                    $sendgrid = new SendGrid($ConfigFile['SendGrid']['apikey']);
                    $email = new Mail();
                    $email->addTo($reset_email, "Usuario");
                    $email->setFrom("farmaciasemg@gmail.com", "HG Pharmacy");
                    $email->setSubject("Password Recovery Code");
                    $email->AddContent("text/html", "<strong>Please use the following code to Reset your Password: {$code}</strong><br><br>This code expires in 1 hour.");
                    $sendgrid->send($email);
                }
                $stmt->close();
            } catch (Exception $e) {
                logError("Error resending code: " . $e->getMessage());
                echo "<div class='alert alert-danger'>Error resending code. Please try again.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Session expired. Please start over.</div>";
        }
    }
}


//This Validation is used to confirm the password change when user wants to reset password through login
if (!empty($_POST["user_password_confirmation_button"])) {
    if (!empty($_POST["newPassword1"]) and (!empty($_POST["newPassword2"]))) {

        // Verify code was validated
        if (empty($_SESSION['codigo_verified']) || empty($_SESSION['reset_user_id'])) {
            echo "<div class='alert alert-danger'>Session expired. Please request a new code!</div>";
        } else {
            $user_id = $_SESSION['reset_user_id'];
            $password1 = $_POST['newPassword1'];
            $password2 = $_POST['newPassword2'];

            if (strlen($password1) >= 8) {
                if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                    if ($password1 !== $password2) {
                        echo "<div class='alert alert-danger'>Passwords entered do not match!</div>";
                    } else {
                        $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

                        // Use Prepared Statement
                        $stmt = $connection->prepare("UPDATE Usuarios SET codigo = NULL, codigo_expires = NULL, contrasena = ? WHERE id = ?");
                        $stmt->bind_param("si", $crypt_password, $user_id);

                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>Password changed Successfully!</div>";
                            // Clear session data
                            unset($_SESSION['codigo_verified']);
                            unset($_SESSION['reset_user_id']);
                            unset($_SESSION['reset_email']);
                            session_destroy();
                            header("refresh:3;url=../index.php");
                        } else {
                            logError("Failed to update password", ['user_id' => $user_id]);
                            echo "<div class='alert alert-danger'>An Error occurred while changing your password! Please try again later.</div>";
                        }
                        $stmt->close();
                    }
                } else {
                    echo "<div class='alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Password most have at least 8 characters!</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill the blanks with a valid password!</div>";
    }
}


//This Validation is used to Change users password thought account settings
if (!empty($_POST["user_password_change_button"])) {
    if ((!empty($_POST["currentPassword"])) and (!empty($_POST["newPassword1"])) and (!empty($_POST["newPassword2"]))) {
        $id_connected = intval($_POST['id_user']);

        // Verify user is changing their own password
        if ($id_connected != $_SESSION["id"]) {
            echo "<div class='alert alert-danger'>Unauthorized action!</div>";
        } else {
            $currentPassword = $_POST["currentPassword"];
            $password1 = $_POST['newPassword1'];
            $password2 = $_POST['newPassword2'];

            // Use Prepared Statement
            $stmt = $connection->prepare("SELECT contrasena FROM Usuarios WHERE id = ?");
            $stmt->bind_param("i", $id_connected);
            $stmt->execute();
            $result = $stmt->get_result();
            $fetch = $result->fetch_assoc();
            $stmt->close();

            if (password_verify($currentPassword, $fetch['contrasena'])) {
                if (strlen($password1) >= 8) {
                    if (preg_match("/^[a-zA-Z0-9@_.!]+$/", $password1)) {
                        if ($password1 !== $password2) {
                            echo "<div class='alert alert-danger'>Passwords entered do not match!</div>";
                        } else {
                            $crypt_password = password_hash($password1, PASSWORD_DEFAULT);

                            // Use Prepared Statement
                            $stmt_update = $connection->prepare("UPDATE Usuarios SET contrasena = ? WHERE id = ?");
                            $stmt_update->bind_param("si", $crypt_password, $id_connected);

                            if ($stmt_update->execute()) {
                                echo "<div class='alert alert-success'>Password changed Successfully!</div>";
                                header("refresh:3;url=../screens/home.php");
                            } else {
                                logError("Failed to change password", ['user_id' => $id_connected]);
                                echo "<div class='alert alert-danger'>An Error occurred while changing your password! Please try again later.</div>";
                            }
                            $stmt_update->close();
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Special Characters or Spaces are not allowed!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Password most have at least 8 characters!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Current Password is Incorrect!</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill the blanks with a valid password!</div>";
    }
}


//This Validation is used to save a new product to the inventory
if (!empty($_POST["save_product_button"])) {
    if ((!empty($_POST["numero"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))) {
        $number = trim($_POST["numero"]);
        $description = trim($_POST["description"]);
        $name = trim($_POST["name"]);
        $quantity = trim($_POST["quantity"]);
        $price = trim($_POST["price"]);
        $presentation = trim($_POST["presentation"]);
        $expiration_date = trim($_POST["expiration_date"]);
        $administration_form = trim($_POST["administration_form"]);
        $storage = trim($_POST["storage"]);

        // Use Prepared Statement to check product number
        $stmt_check = $connection->prepare("SELECT id_producto FROM Inventario WHERE id_producto = ?");
        $stmt_check->bind_param("i", $number);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            echo "<div class='alert alert-danger'>Product Number is already assigned to another Product!</div>";
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
                                                // Use Prepared Statement
                                                $stmt_insert = $connection->prepare("INSERT INTO Inventario (id_producto, nombre_producto, descripcion, cantidad_producto, precio, presentacion_producto, fecha_vencimiento, forma_administracion, almacenamiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                                $stmt_insert->bind_param("issidssss", $number, $name, $description, $quantity, $price, $presentation, $expiration_date, $administration_form, $storage);

                                                if ($stmt_insert->execute()) {
                                                    echo "<div class='alert alert-success'>New Product added Successfully!</div>";
                                                } else {
                                                    logError("Failed to add product", ['product_number' => $number]);
                                                    echo "<div class='alert alert-danger'>An Error has occurred while adding the Product! Please try again later.</div>";
                                                }
                                                $stmt_insert->close();
                                            } else {
                                                echo "<div class='alert alert-danger'>Storage information is not Valid!</div>";
                                            }
                                        } else {
                                            echo "<div class='alert alert-danger'>Way of Administration is not Valid!</div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>Expiration Date is not Valid!</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>Presentation type is not Valid!</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Price is not Valid!</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Existence is not Valid!</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Product Name is too large, or it may contain invalid characters!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Description is too large, or it may contain invalid characters!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Product Number is Invalid, or it contains more than 6 digits</div>";
            }
        }
        $stmt_check->close();
    } else {
        echo "<div class='alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This Validation is used for editing products
if (!empty($_POST["edit_product_button"])) {
    if ((!empty($_POST["number"])) and (!empty($_POST["name"])) and (!empty($_POST["description"])) and (!empty($_POST["quantity"])) and (!empty($_POST["price"])) and (!empty($_POST["presentation"])) and (!empty($_POST["expiration_date"])) and (!empty($_POST["administration_form"])) and (!empty($_POST["storage"]))) {
        $id = intval($_POST['id_producto']);
        $number = trim($_POST["number"]);
        $description = trim($_POST["description"]);
        $name = trim($_POST["name"]);
        $quantity = intval($_POST["quantity"]);
        $price = floatval($_POST["price"]);
        $presentation = trim($_POST["presentation"]);
        $expiration_date = trim($_POST["expiration_date"]);
        $administration_form = trim($_POST["administration_form"]);
        $storage = trim($_POST["storage"]);

        // Use Prepared Statement to check product number conflict
        $stmt_check = $connection->prepare("SELECT id_producto FROM Inventario WHERE id_producto = ? AND id_producto != ?");
        $new_number = intval($number);
        $stmt_check->bind_param("ii", $new_number, $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            echo "<div class='alert alert-danger'>Product Number is already assigned to another Product!</div>";
        } else {
            if (preg_match("/^[0-9]+$/", $number) && (strlen($number) <= 6)) {
                if ((strlen($description) <= 500) && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9\/!(),.]+$/u", $description)) {
                    if (strlen($name) <= 30 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                        if (strlen($_POST["quantity"]) <= 6 && preg_match("/^\d+$/", $_POST["quantity"])) {
                            if (strlen($_POST["price"]) <= 9 && preg_match("/^\d+(\.\d+)?$/", $_POST["price"])) {
                                if (strlen($presentation) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $presentation)) {
                                    if (preg_match("/^(?:\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})$/", $expiration_date)) {
                                        if (strlen($administration_form) <= 20 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $administration_form)) {
                                            if (strlen($storage) <= 25 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ0-9]+$/u", $storage)) {
                                                // Use Prepared Statement
                                                $stmt_update = $connection->prepare("UPDATE Inventario SET id_producto = ?, nombre_producto = ?, descripcion = ?, cantidad_producto = ?, precio = ?, presentacion_producto = ?, fecha_vencimiento = ?, forma_administracion = ?, almacenamiento = ? WHERE id_producto = ?");
                                                $stmt_update->bind_param("issidssssi", $new_number, $name, $description, $quantity, $price, $presentation, $expiration_date, $administration_form, $storage, $id);

                                                if ($stmt_update->execute()) {
                                                    echo "<div class='alert alert-success'>Product edited Successfully!</div>";
                                                    header("refresh:3;url=inventory_control.php");
                                                } else {
                                                    logError("Failed to edit product", ['product_id' => $id]);
                                                    echo "<div class='alert alert-danger'>An Error has occurred while editing the Product! Please try again later.</div>";
                                                }
                                                $stmt_update->close();
                                            } else {
                                                echo "<div class='alert alert-danger'>Storage information is not Valid!</div>";
                                            }
                                        } else {
                                            echo "<div class='alert alert-danger'>Way of Administration is not Valid!</div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>Expiration Date is not Valid!</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>Presentation type is not Valid!</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Price is not Valid!</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Existence is not Valid!</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Product Name is too large, or it may contain invalid characters!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Description is too large, or it may contain invalid characters!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Product Number is Invalid, or it contains more than 6 digits</div>";
            }
        }
        $stmt_check->close();
    } else {
        echo "<div class='alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


//This validation is used to edit a user from the user management
if (!empty($_POST["edit_user_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"])) and (!empty($_POST["roles"]))) {
        $id = intval($_POST['id_user']);
        $name = trim($_POST['name']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $roles = trim($_POST['roles']);

        if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
            if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Validate role exists
                    $stmt_role = $connection->prepare("SELECT nombre_rol FROM Roles WHERE nombre_rol = ?");
                    $stmt_role->bind_param("s", $roles);
                    $stmt_role->execute();
                    $role_result = $stmt_role->get_result();

                    if ($role_result->num_rows == 0) {
                        echo "<div class='alert alert-danger'>Invalid role selected!</div>";
                    } else {
                        // Use Prepared Statement
                        $stmt = $connection->prepare("UPDATE Usuarios SET nombre = ?, apellido = ?, correo = ?, roles = ? WHERE id = ?");
                        $stmt->bind_param("ssssi", $name, $lastname, $email, $roles, $id);

                        if ($stmt->execute()) {
                            echo "<div class='alert alert-success'>User has been modified successfully!</div>";
                            header("refresh:3;url=user_management.php");
                        } else {
                            logError("Failed to edit user", ['user_id' => $id]);
                            echo "<div class='alert alert-danger'>An Error has occurred while trying to modify this user! Please try again later.</div>";
                        }
                        $stmt->close();
                    }
                    $stmt_role->close();
                } else {
                    echo "<div class='alert alert-danger'>Email is not valid!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Last Name is not valid!</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Name is not valid!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill all the blanks to continue!</div>";
    }
}


// This Validation is used to edit the users account from the account settings
if (!empty($_POST["edit_user_settings_button"])) {
    if (!empty($_POST["name"]) and (!empty($_POST["lastname"])) and (!empty($_POST["email"]))) {
        $id = intval($_POST['id_user']);

        // Verify user is editing their own account
        if ($id != $_SESSION["id"]) {
            echo "<div class='alert alert-danger'>Unauthorized action!</div>";
        } else {
            $name = trim($_POST['name']);
            $lastname = trim($_POST['lastname']);
            $email = trim($_POST['email']);

            if (strlen($name) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $name)) {
                if (strlen($lastname) <= 15 && preg_match("/^[A-Za-z\sáéíóúÁÉÍÓÚüÜ]+$/u", $lastname)) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        // Use Prepared Statement
                        $stmt = $connection->prepare("UPDATE Usuarios SET nombre = ?, apellido = ?, correo = ? WHERE id = ?");
                        $stmt->bind_param("sssi", $name, $lastname, $email, $id);

                        if ($stmt->execute()) {
                            // Update session data
                            $_SESSION["nombre"] = $name;
                            $_SESSION["apellido"] = $lastname;
                            echo "<div class='alert alert-success'>User has been modified successfully!</div>";
                            header("refresh:3;url=home.php");
                        } else {
                            logError("Failed to edit user settings", ['user_id' => $id]);
                            echo "<div class='alert alert-danger'>An Error has occurred while editing this user! Please try again later.</div>";
                        }
                        $stmt->close();
                    } else {
                        echo "<div class='alert alert-danger'>Email is not valid!</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Last Name is not valid!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Name is not valid!</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill all the blanks to continue!</div>";
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
                    throw new Exception("Stock insuficiente para " . sanitizeOutput($item['nombre_producto']) . ". Disponible: " . $stock_data['cantidad_producto']);
                }
                $stmt_stock->close();
            }
            $stmt_cart->close();

            // Insert receipt header into Facturas table
            $stmt_factura = $connection->prepare("INSERT INTO Facturas (fecha_hora, cliente, rtn, cajero, usuario_id, estado, metodo_pago, total) VALUES (?, ?, ?, ?, ?, 'Completada', ?, ?)");
            $stmt_factura->bind_param("ssssisd", $date_time, $customer_name, $rtn, $cashier_name, $user_id, $payment_method, $total);

            if (!$stmt_factura->execute()) {
                throw new Exception("Error al crear la factura.");
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
                    throw new Exception("Error al insertar detalles de factura.");
                }

                // Update inventory stock
                $stmt_update_stock->bind_param("ii", $item['cantidad'], $item['producto_id']);

                if (!$stmt_update_stock->execute()) {
                    throw new Exception("Error al actualizar inventario.");
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
            logError("Billing error: " . $e->getMessage(), ['user_id' => $_SESSION['id']]);
            echo "<div class='alert alert-danger'>" . sanitizeOutput($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Por favor complete todos los campos requeridos (Nombre del Cliente, Fecha/Hora, Cajero, Método de Pago)!</div>";
    }
}
