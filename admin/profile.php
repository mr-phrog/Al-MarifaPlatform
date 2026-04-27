<?php

   include '../components/connect.php';

   if(isset($_COOKIE['tutor_id'])){
      $tutor_id = $_COOKIE['tutor_id'];
   }else{
      $tutor_id = '';
      header('location:login.php');
   }

   $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $select_playlists->execute([$tutor_id]);
   $total_playlists = $select_playlists->rowCount();

   $select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
   $select_contents->execute([$tutor_id]);
   $total_contents = $select_contents->rowCount();

   $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
   $select_likes->execute([$tutor_id]);
   $total_likes = $select_likes->rowCount();

   $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
   $select_comments->execute([$tutor_id]);
   $total_comments = $select_comments->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="الملف الشخصي" data-en="Profile"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="tutor-profile" style="min-height: calc(100vh - 19rem);"> 

   <h1 class="heading" data-ar="تفاصيل الملف الشخصي" data-en="profile details"></h1>

   <div class="details">
      <div class="tutor">
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span data-ar="<?= htmlspecialchars($fetch_profile['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_profile['profession']); ?>">
            <?= htmlspecialchars($fetch_profile['profession_ar']); // default to Arabic ?> 
         </span>   
         <a href="update.php" class="inline-btn" data-ar="تحديث الملف الشخصي" data-en="update profile"></a>
      </div>
      <div class="flex">
         <div class="box">
            <span><?= $total_playlists; ?></span>
            <p data-ar="إجمالي قوائم التشغيل" data-en="total playlists"></p>
            <a href="playlists.php" class="btn" data-ar="عرض قوائم التشغيل" data-en="view playlists"></a>
         </div>
         <div class="box">
            <span><?= $total_contents; ?></span>
            <p data-ar="إجمالي الفيديوهات" data-en="total videos"></p>
            <a href="contents.php" class="btn" data-ar="عرض المحتويات" data-en="view contents"></a>
         </div>
         <div class="box">
            <span><?= $total_likes; ?></span>
            <p data-ar="إجمالي الاعجابات" data-en="total likes"></p>
            <a href="books.php" class="btn" data-ar="عرض الكتب" data-en="view books"></a>
         </div>
         <div class="box">
            <span><?= $total_comments; ?></span>
            <p data-ar="إجمالي التعليقات" data-en="total comments"></p>
            <a href="comments.php" class="btn" data-ar="عرض التعليقات" data-en="view comments"></a>
         </div>
      </div>
   </div>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>