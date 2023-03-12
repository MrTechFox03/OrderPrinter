<?php
// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter your username.";
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        //thee admin
        if(md5($username) === "21232f297a57a5a743894a0e4a801fc3" && md5($password) === "d6c5e6869b2b3d4e4aaf323e6d542206"){
            $_SESSION["loggedin"] = md5("lggedin");
            $_SESSION["test"] = "hoi";
            header("location: ../Simple");
        } else{
            $password_err = "The password you entered is not valid.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            background-color: #F7F7F7;
            font-family: Arial, sans-serif;
        }
        .wrapper {
            width: 350px;
            padding: 50px;
            padding-right: 60px;
            margin: 100px auto;
            background-color: #FFFFFF;
            border: 1px solid #E0E0E0;
            box-shadow: 0px 0px 10px #E0E0E0;
            border-radius: 5px;
        }
        h2 {
            margin-top: 0px;
        }
        form {
            display: block;
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #E0E0E0;
        }
        input[type="submit"] {
            margin-top: 10px;
            background-color: #4CAF50;
            color: #FFFFFF;
            padding: 8px 16px;
            border-radius: 3px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #3E8E41;
        }
        .error {
            color: #FF0000;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $username; ?>">
            <span class="error"><?php echo $username_err; ?></span>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password">
            <span class="error"><?php echo $password_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Login">
        </div>
    </form>

