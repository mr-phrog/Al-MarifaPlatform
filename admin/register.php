<?php

include '../components/connect.php';

if(isset($_POST['submit'])){

   $id = unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $profession = $_POST['profession'];
   $profession = filter_var($profession, FILTER_SANITIZE_STRING);
   $profession_ar = $_POST['profession_ar'];
   $profession_ar = filter_var($profession_ar, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE email = ?");
   $select_tutor->execute([$email]);
   
   if($select_tutor->rowCount() > 0){
      $message[] = '<span data-ar="البريد الالكتروني تم إستعماله من قبل!" data-en="email already taken!"></span>';
   }else{
      if($pass != $cpass){
         $message[] = '<span data-ar="كلمة المرور غير مطابقة!" data-en="Passwords do not match!"></span>';
      }else{
         $insert_tutor = $conn->prepare("INSERT INTO `tutors`(id, name, profession, profession_ar, email, password, image) VALUES(?,?,?,?,?,?,?)");
         $insert_tutor->execute([$id, $name, $profession, $profession_ar, $email, $pass, $rename]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = '<span data-ar="تم تسجيل معلم جديد! يرجى تسجيل الدخول الآن" data-en="new tutor registered! please login now"></span>';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="التسجيل في المنصة" data-en="register"></title>

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

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3 data-ar="تسجيل جديد" data-en="register new"></h3>
      <div class="flex">
         <div class="col">
            <p data-ar="اسمك" data-en="your name"> <span>*</span></p>
            <input type="text" name="name" data-ar="ادخل اسمك" data-en="eneter your name" maxlength="50" required class="box">
            <p data-ar="مهنتك" data-en="your profession"> <span>*</span></p>
            <select name="profession" class="box" required onchange="updateProfessionAr()">
               <option value="" disabled selected data-ar="-- اختيار مهنتك" data-en="-- select your profession"></option>
               <option value="developer" data-ar="مطور" data-en="developer"></option>
               <option value="designer" data-ar="مصمم" data-en="designer"></option>
               <option value="musician" data-ar="موسيقي" data-en="musician"></option>
               <option value="biologist" data-ar="أحيائي" data-en="biologist"></option>
               <option value="teacher" data-ar="مدرس" data-en="teacher"></option>
               <option value="engineer" data-ar="مهندس" data-en="engineer"></option>
               <option value="lawyer" data-ar="محامي" data-en="lawyer"></option>
               <option value="accountant" data-ar="محاسب" data-en="accountant"></option>
               <option value="doctor" data-ar="طبيب" data-en="doctor"></option>
               <option value="journalist" data-ar="صحفي" data-en="journalist"></option>
               <option value="photographer" data-ar="مصور" data-en="photographer"></option>
            </select>
            <input type="hidden" name="profession_ar" id="profession_ar" value="">
            <p data-ar="بريدك الالكتروني" data-en="your email"> <span>*</span></p>
            <input type="email" name="email" data-ar="ادخل بريدك الالكتروني" data-en="enter your email" maxlength="20" required class="box">
         </div>
         <div class="col">
            <p data-ar="كلمة السر" data-en="your password"> <span>*</span></p>
            <input type="password" name="pass" data-ar="ادخل كلمة السر" data-en="enter your password" maxlength="20" required class="box">
            <p data-ar="تأكيد كلمة السر" data-en="confirm password"> <span>*</span></p>
            <input type="password" name="cpass" data-ar="تأكيد كلمة السر" data-en="confirm your password" maxlength="20" required class="box">
            <p data-ar="اختار الصورة المصغرة" data-en="select picture"> <span>*</span></p>
            <input type="file" name="image" accept="image/*" required class="box">
         </div>
      </div>
      <p class="link" data-ar="لديك حساب بالفعل؟" data-en="already have an account?"> <a href="login.php" data-ar="تسجيل الدخول الآن" data-en="login now"></a></p>
      <input type="submit" name="submit" data-ar="التسجيل الآن" data-en="register now" value="register now" class="btn">
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