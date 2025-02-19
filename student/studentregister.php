<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: studentregister.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="../resources/style.css">
</head>
<body>
    <div class="form">
        <div class="container">
            <?php
            if (isset($_POST["submit"])) {
            $fullName = $_POST["fullname"];
            $studentid = $_POST["studentid"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();
            
            if (empty($fullName) OR empty($studentid) OR empty($password) OR empty($passwordRepeat)) {
                array_push($errors,"All fields are required");
            }
            if (strlen($password)<8) {
                array_push($errors,"Password must be at least 8 charactes long");
            }
            if ($password!==$passwordRepeat) {
                array_push($errors,"Password does not match");
            }
            require_once "../connection/database.php";
            $sql = "SELECT * FROM student WHERE studentid = '$studentid'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount>0) {
                array_push($errors,"Id already exists!");
            }
            if (count($errors)>0) {
                foreach ($errors as  $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            }else{
                
                $sql = "INSERT INTO student (full_name, studentid, password) VALUES ( ?, ?, ? )";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt,"sss",$fullName, $studentid, $passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>You are registered successfully.</div>";
                }else{
                    die("Something went wrong");
                }
            }
            
            }
            ?>
            <form action="studentregister.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="studentid" placeholder="Student Id:">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password:">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:">
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Register" name="submit">
                </div>
            </form>
            <div>
            <div><p>Already Registered <a href="studentlogin.php">Login Here</a></p></div>
        </div>
        </div>
    </div>    
</body>
</html>