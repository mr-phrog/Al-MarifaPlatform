<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
   if($select_user->rowCount() > 0){
     setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
     header('location:home.php');
   }else{
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تسجيل الدخول" data-en="login"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3 data-ar="مرحبًا بك، مجددًا!" data-en="welcome back!"></h3>
      <p data-ar="بريدك الالكتروني" data-en="your email"> <span>*</span></p>
      <input type="email" name="email" data-en="enter your email" data-ar="أدخل بريدك الإلكتروني" maxlength="50" required class="box">
      <p data-ar="كلمة السر" data-en="your password"> <span>*</span></p>
      <input type="password" name="pass" data-en="enter your password" data-ar="ادخل كلمة السر" maxlength="20" required class="box">
      <p class="link" data-ar="ليس لديك حساب؟" data-en="don't have an account?"> <a href="register.php" data-ar="سجل الآن" data-en="register now"></a></p>
      <input type="submit" name="submit"  data-en="login now" data-ar="تسجيل الدخول الآن" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>