<?php

include '../components/connect.php';

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ? AND password = ? LIMIT 1");
   $select_tutor->execute([$email, $pass]);
   $row = $select_tutor->fetch(PDO::FETCH_ASSOC);
   
   if($select_tutor->rowCount() > 0){
     setcookie('tutor_id', $row['id'], time() + 60*60*24*30, '/');
     header('location:dashboard.php');
   }else{
      $message[] = '<span data-ar="البريد الالكتروني أو كلمة السر غير صحيحة!" data-en="incorrect email or password!"></span>';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تسجيل الدخول" data-en="Login"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body style="padding-right: 0; padding-left: 0;">

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message form">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<!-- register section starts  -->

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3 data-ar="مرحبًا بك، مجددًا!" data-en="welcome back!"></h3>
      <p data-ar="بريدك الالكتروني" data-en="your email"> <span>*</span></p>
      <input type="email" name="email" data-en="enter your email" data-ar="أدخل بريدك الإلكتروني" maxlength="50" required class="box">
      <p data-ar="كلمة السر الخاصة بك" data-en="your password"> <span>*</span></p>
      <input type="password" name="pass" data-en="enter your password" data-ar="ادخل كلمة السر" maxlength="20" required class="box">
      <p class="link" data-ar="ليس لديك حساب؟" data-en="don't have an account?"> <a href="register.php" data-ar="سجل الآن" data-en="register now" ></a></p>
      <input type="submit" name="submit"  data-en="login now" data-ar="تسجيل الدخول الآن" class="btn">
   </form>

</section>

<!-- registe section ends -->














<script>

let darkMode = localStorage.getItem('dark-mode');
let body = document.body;

const enabelDarkMode = () =>{
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

const disableDarkMode = () =>{
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

if(darkMode === 'enabled'){
   enabelDarkMode();
}else{
   disableDarkMode();
}

</script>
<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>