<?php
session_start();
include('config.php');
error_reporting(0);

$error = "";

if(isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM userdata WHERE UserEmail=:uemail AND LoginPassword=:upassword";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uemail', $email, PDO::PARAM_STR);
    $query->bindParam(':upassword', $password, PDO::PARAM_STR);
    $query->execute();
    
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if($user) {
        $_SESSION['userlogin'] = $email;
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: homepage.php");
        }
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
            list-style: none;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(90deg, #e2e2e2,#DFE2DF);
        }

        .container {
            width: 400px;
            background: #F8FAF8;
            border-radius: 30px;
            box-shadow: 0 0 30px rgba(0, 0, 0, .2);
            padding: 40px;
            text-align: center;
        }

        .container h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .container p {
            font-size: 14.5px;
            margin: 15px 0;
        }

        .input-box {
            position: relative;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .input-box input::placeholder {
            color: #888;
            font-weight: 400;
        }
        
        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .forgot-link {
            margin: -15px 0 15px;
        }

        .forgot-link a {
            font-size: 14.5px;
            color: #333;
        }

        .btn {
            width: 100%;
            height: 48px;
            background: #1e8814;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .social-icons a {
            display: inline-flex;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 24px;
            color: #333;
            margin: 0 8px;
        }

        .register-link {
            margin-top: 20px;
        }

        .register-link a {
            color: #5A4381;
            font-weight: 600;
        }

        .error-message {
            color: red;
            background-color: #ffebeb;
            border: 1px solid #ffbdbd;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <h1>Login</h1>
            
            <?php if($error) { ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php } ?>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <div class="forgot-link">
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" name="signin" class="btn">Login</button>
            
            <p>or login with social platforms</p>
            <div class="social-icons">
                <a href="#"><i class='bx bxl-google'></i></a>
                <a href="#"><i class='bx bxl-github'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
            </div>
            
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>