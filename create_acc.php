<?php
require_once('config.php');

// Function to validate email address using regular expression
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to check if the email exists
function emailExists($email) {
    global $connection; // Use the global connection variable
    
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->conn->query($query);
    return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all fields are filled
    if (empty($_POST["first_name"]) || empty($_POST["middle_name"]) || empty($_POST["last_name"]) || empty($_POST["username"]) || empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["confirm_password"])) {
        echo "Please fill in all fields.";
    } else {
        // Validate email format
        $email = $_POST["email"];
        if (!isValidEmail($email)) {
            echo "Invalid email address.";
        } else {
            // Check if email already exists
            if (emailExists($email)) {
                echo "Email already exists.";
            } else {
                // Check if password matches confirm password
                $password = $_POST["password"];
                $confirmPassword = $_POST["confirm_password"];
                if ($password !== $confirmPassword) {
                    echo "Passwords do not match.";
                } else {
                    // Insert user into database
                    $first_name = $_POST["first_name"];
                    $middle_name = $_POST["middle_name"];
                    $last_name = $_POST["last_name"];
                    $username = $_POST["username"];
                    
                    // Hash the password before storing it
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Prepare and bind SQL statement
                    $stmt = $connection->conn->prepare("INSERT INTO users (first_name, middle_name, last_name, username, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $first_name, $middle_name, $last_name, $username, $email, $hashed_password);
                    
                    // Execute the statement
                    if ($stmt->execute()) {
                        echo "Account created successfully.";
                    } else {
                        echo "Error: " . $stmt->error;
                    }

                    // Close statement
                    $stmt->close();
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?> 
<body class="hold-transition ">
  
<style>
    html, body{
      height:100%;
      width:100%;
      margin: 0;
      padding: 0;
    }
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
    }
    .login-title{
      text-shadow: 2px 2px black;
    }
    #login{
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    #logo-img{
        height:150px;
        width:150px;
        object-fit:scale-down;
        object-position:center center;
        border-radius:100%;
    }
    #login .col-7,#login .col-5{
      width: 100%;
      max-width:unset;
    }
</style>
<div id="login">
    <div class="col-7 d-flex justify-content-center align-items-center">
      <div>
        <center><img src="<?= validate_image($_settings->info('logo')) ?>" alt="" id="logo-img"></center>
        <h1 class="text-center py-5 login-title"><b><?php echo $_settings->info('name') ?>  </b></h1>
      </div>
    </div>
    <div class="col-5 bg-gradient">
        <div class="d-flex w-100 h-100 justify-content-center align-items-center">
            <div class="card col-sm-12 col-md-6 col-lg-3 card-outline card-primary rounded-0 shadow">
                <div class="card-header rounded-0">
                    <h4 class="text-purle text-center"><b>Create New Account</b></h4>
                </div>
                <div class="card-body rounded-0">
                    <form id="create-account-frm" action="create-account-submit.php" method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" autofocus name="first_name" placeholder="First Name" maxlength="35" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="middle_name" placeholder="Middle Name" maxlength="35" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="last_name" placeholder="Last Name" maxlength="35" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="username" placeholder="Username" maxlength="15" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <a href="<?php echo base_url ?>">Go Back Home</a>
                            </div>
                            <div class="col-4">
                                <button type="submit" name="create_account" class="btn btn-primary btn-block btn-flat">Create Account</button>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-12 text-center">
                             <p class="mb-0">Already have an account?</p>
                            <a href="admin/login.php">Sign in here</a>
                             </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("togglePassword").addEventListener("click", function () {
        var passwordField = document.getElementById("password");
        var buttonIcon = document.querySelector("#togglePassword i");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            buttonIcon.classList.remove("fa-eye");
            buttonIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            buttonIcon.classList.remove("fa-eye-slash");
            buttonIcon.classList.add("fa-eye");
        }
    });

    document.getElementById("toggleConfirmPassword").addEventListener("click", function () {
        var confirmPasswordField = document.getElementById("confirm_password");
        var buttonIcon = document.querySelector("#toggleConfirmPassword i");

        if (confirmPasswordField.type === "password") {
            confirmPasswordField.type = "text";
            buttonIcon.classList.remove("fa-eye");
            buttonIcon.classList.add("fa-eye-slash");
        } else {
            confirmPasswordField.type = "password";
            buttonIcon.classList.remove("fa-eye-slash");
            buttonIcon.classList.add("fa-eye");
        }
    });
</script>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
