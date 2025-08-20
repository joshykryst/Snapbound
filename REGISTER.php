<?php
session_start();
include('config.php');
error_reporting(0);

$error = "";
$emailValue = "";

if(isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $emailValue = $email;

    $ret = "SELECT * FROM userdata WHERE UserEmail=:uemail";
    $queryt = $dbh->prepare($ret);
    $queryt->bindParam(':uemail', $email, PDO::PARAM_STR);
    $queryt->execute();

    if($queryt->rowCount() == 0) {
        $sql = "INSERT INTO userdata(UserEmail, LoginPassword) VALUES(:uemail, :upassword)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':uemail', $email, PDO::PARAM_STR);
        $query->bindParam(':upassword', $password, PDO::PARAM_STR);
        $query->execute();
        
        if ($dbh->lastInsertId()) {
            $_SESSION['uemail'] = $email;
            header("Location: register_step1.php");
            exit();
        }
    } else {
        $error = "Email already exists.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
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

        .login-link {
            margin-top: 20px;
        }

        .login-link a {
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

        #email-availability-status {
            font-size: 14px;
            margin-top: -20px;
            margin-bottom: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <h1>Registration</h1>
            
            <?php if($error) { ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php } ?>

            <div class="input-box">
                <input type="email" id="register-email" name="email" placeholder="Email" 
                    onBlur="checkEmailAvailability()" 
                    value="<?php echo htmlspecialchars($emailValue); ?>"
                    required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div id="email-availability-status"></div>
            
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="submit" name="signup" class="btn">Register</button>
            
            <p>or register with social platforms</p>
            <div class="social-icons">
                <a href="#"><i class='bx bxl-google'></i></a>
                <a href="#"><i class='bx bxl-github'></i></a>
                <a href="#"><i class='bx bxl-linkedin'></i></a>
            </div>
            
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script>
    function checkEmailAvailability() {
        const email = document.getElementById("register-email").value;
        const status = document.getElementById("email-availability-status");
        
        if(email) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "check_email.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if(xhr.readyState === 4 && xhr.status === 200) {
                    status.innerHTML = xhr.responseText;
                }
            };
            
            xhr.send("email=" + email);
        } else {
            status.innerHTML = "";
        }
    }
    </script>
</body>
</html>