<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `user_info` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];
      header('location:Profile_&_products.php');  // Redirect to Profile_&_products.php
   }else{
      $message[] = 'Incorrect password or email!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
   <style>
      body {
         background-color: #0ab1e5; /* Blue background */
         color: white;
         font-family: Arial, sans-serif;
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100vh;
         margin: 0;
      }

      .form-container {
         background-color: rgba(0, 121, 107, 0.9); /* Semi-transparent teal background */
         padding: 30px;
         border-radius: 10px;
         width: 300px;
         box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
         border: 2px solid white; /* White border */
      }

      .form-container h3 {
         text-align: center;
         margin-bottom: 20px;
         font-size: 24px;
         color: white;
      }

      .form-container .box {
         width: 100%;
         padding: 10px;
         margin: 10px 0;
         border-radius: 5px;
         border: none;
         background-color: #ffffff;
         color: #0ab1e5;
         font-size: 16px;
      }

      .form-container .btn {
         width: 100%;
         padding: 10px;
         background-color: #ffffff; /* White background */
         color: #0ab1e5; /* Blue text */
         border: none;
         border-radius: 5px;
         font-size: 16px;
         cursor: pointer;
         transition: background-color 0.3s ease;
      }

      .form-container .btn:hover {
         background-color: #eeeeee; /* Slightly darker on hover */
      }

      .form-container p {
         text-align: center;
         color: white;
      }

      .form-container p a {
         color: #ffeb3b; /* Yellow text */
         text-decoration: none;
         transition: color 0.3s ease;
      }

      .form-container p a:hover {
         color: #ffee58; /* Lighter yellow on hover */
      }

      .message {
         background-color: #f44336; /* Red background */
         color: white;
         padding: 10px;
         margin: 10px 0;
         border-radius: 5px;
         text-align: center;
         cursor: pointer;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Login Now</h3>
      <input type="email" name="email" required placeholder="Enter Email" class="box">
      <input type="password" name="password" required placeholder="Enter Password" class="box">
      <input type="submit" name="submit" class="btn" value="Login Now">
      <p>Don't have an account? <a href="register.php">Register Now</a></p>
   </form>

</div>

</body>
</html>
