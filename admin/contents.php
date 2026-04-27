<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_POST['delete_video'])){
   $delete_id = $_POST['video_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $verify_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
   $verify_video->execute([$delete_id]);
   if($verify_video->rowCount() > 0){
      $delete_video_thumb = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video_thumb->execute([$delete_id]);
      $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
      $delete_video = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $delete_video->execute([$delete_id]);
      $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_video['video']);
      $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
      $delete_likes->execute([$delete_id]);
      $delete_mcq = $conn->prepare("DELETE FROM `mcq` WHERE content_id = ?");
      $delete_mcq->execute([$delete_id]);
      $delete_mcq = $conn->prepare("DELETE FROM `mcq_options` WHERE mcq_id = ?");
      $delete_mcq->execute([$delete_id]);
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);
      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);
      $message[] = '<span data-ar="تم حذف الفيديو!" data-en="video deleted!"></span>';
   }else{
      $message[] = '<span data-ar="تم حذف الفيديو بالفعل!" data-en="video already deleted!"></span>';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="المحتويات" data-en="contents"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="contents">

   <h1 class="heading" data-ar="محتوياتك" data-en="your contents"></h1>

   <div class="box-container">

   <div class="box" style="text-align: center;">
      <h3 class="title" style="margin-bottom: .5rem;" data-ar="إنشاء محتوى جديد" data-en="create new content"></h3>
      <a href="add_content.php" class="btn" data-ar="إضافة محتوى" data-en="add content"></a>
   </div>

   <?php
      $select_videos = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? ORDER BY date DESC");
      $select_videos->execute([$tutor_id]);
      if($select_videos->rowCount() > 0){
         while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
            $video_id = $fecth_videos['id'];
   ?>
      <div class="box">
         <div class="flex">
         <div>
            <i class="fas fa-dot-circle" style="color: <?php echo $fecth_videos['status'] == 'active' ? 'limegreen' : 'red'; ?>;"></i>
            <span 
               data-ar="<?php echo $fecth_videos['status'] == 'active' ? 'نشطة' : 'غير نشطة'; ?>" 
               data-en="<?php echo $fecth_videos['status'] == 'active' ? 'Active' : 'De-active'; ?>" 
               style="color: <?php echo $fecth_videos['status'] == 'active' ? 'limegreen' : 'red'; ?>;">
               <?php echo $fecth_videos['status'] == 'active' ? ($current_language == 'ar' ? 'نشطة' : 'Active') : ($current_language == 'ar' ? 'غير نشطة' : 'De-active'); ?>
            </span>
         </div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_videos['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $fecth_videos['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fecth_videos['title']; ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn" data-ar="تحديث" data-en="update"></a>
            <input type="submit" value="delete" data-ar="حذف" data-en="delete" class="delete-btn" onclick="return confirmAction (event,'delete_video');" name="delete_video">
         </form>
         <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn" data-ar="عرض المحتوى" data-en="view content"></a>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty" data-ar="لا توجد محتويات مضافة حتى الآن!" data-en="no contents added yet!"></p>';
      }
   ?>

   </div>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>