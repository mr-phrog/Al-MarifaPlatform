<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();

$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

$select_books = $conn->prepare("SELECT * FROM `books` WHERE tutor_id = ?");
$select_books->execute([$tutor_id]);
$total_books = $select_books->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="لوحة التحكم" data-en="dashboard"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="dashboard">

   <h1 class="heading" data-ar="لوحة التحكم" data-en="dashboard"></h1>

   <div class="box-container">

      <div class="box">
         <h3 data-ar="مرحبًا" data-en="welcome!"></h3>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="profile.php" class="btn" data-ar="عرض الملف الشخصي" data-en="view profile"></a>
      </div>

      <div class="box">
         <h3><?= $total_contents; ?></h3>
         <p data-ar="إجمالي المحتويات" data-en="total contents"></p>
         <a href="add_content.php" class="btn" data-ar="إضافة محتوى جديد" data-en="add new content"></a>
      </div>

      <div class="box">
         <h3><?= $total_playlists; ?></h3>
         <p data-ar="إجمالي قوائم التشغيل" data-en="total playlists"></p>
         <a href="add_playlist.php" class="btn" data-ar="إضافة قائمة تشغيل جديدة" data-en="add new playlist"></a>
      </div>

      <div class="box">
         <h3><?= $total_likes; ?></h3>
         <p data-ar="مجموع الاعجابات" data-en="total likes"></p>
         <a href="contents.php" class="btn" data-ar="عرض المحتويات" data-en="view contents"></a>
      </div>

      <div class="box">
         <h3><?= $total_comments; ?></h3>
         <p data-ar="إجمالي التعليقات" data-en="total comments"></p>
         <a href="comments.php" class="btn" data-ar="عرض التعليقات" data-en="view comments"></a>
      </div>
      <div class="box">
         <h3><?= $total_books; ?></h3> <!-- add book section-->
         <p data-ar="إجمالي الكتب" data-en="total books"></p>
         <a href="add_book.php" class="btn" data-ar="اضافة الكتب" data-en="add new books"></a>
      </div>
<!-- 
      <div class="box">
         <h3>quick select</h3>
         <p>login or register</p>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div> -->

   </div>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>


</body>
</html>