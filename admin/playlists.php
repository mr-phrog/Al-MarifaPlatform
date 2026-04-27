<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND tutor_id = ? LIMIT 1");
   $verify_playlist->execute([$delete_id, $tutor_id]);

   if($verify_playlist->rowCount() > 0){

   

   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);
   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);
   $message[] = '<span data-ar="تم حذف قائمة التشغيل!" data-en="playlist deleted!"></span>';
   }else{
      $message[] = '<span data-ar="تم حذف قائمة التشغيل بالفعل!" data-en="playlist already deleted!"></span>';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="قوائم التشغيل" data-en="Playlists"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">

   <h1 class="heading" data-ar="قوائم التشغيل المضافة" data-en="added playlists"></h1>

   <div class="box-container">
   
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;" data-ar="إنشىء قائمة تشغيل جديدة" data-en="create new playlist"></h3>
         <a href="add_playlist.php" class="btn" data-ar="إضافة قائمة تشغيل" data-en="add playlist"></a>
      </div>

      <?php
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? ORDER BY date DESC");
         $select_playlist->execute([$tutor_id]);
         if($select_playlist->rowCount() > 0){
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
            $count_videos->execute([$playlist_id]);
            $total_videos = $count_videos->rowCount();
      ?>
      <div class="box">
         <div class="flex">
         <div>
            <i class="fas fa-dot-circle" style="color: <?php echo $fetch_playlist['status'] == 'active' ? 'limegreen' : 'red'; ?>;"></i>
            <span 
               data-ar="<?php echo $fetch_playlist['status'] == 'active' ? 'نشطة' : 'غير نشطة'; ?>" 
               data-en="<?php echo $fetch_playlist['status'] == 'active' ? 'Active' : 'De-active'; ?>" 
               style="color: <?php echo $fetch_playlist['status'] == 'active' ? 'limegreen' : 'red'; ?>;">
               <?php echo $fetch_playlist['status'] == 'active' ? ($current_language == 'ar' ? 'نشطة' : 'Active') : ($current_language == 'ar' ? 'غير نشطة' : 'De-active'); ?>
            </span>
         </div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span data-ar="<?= $total_videos; ?> فيديوهات" data-en="<?= $total_videos; ?> videos"><?= $total_videos; ?> videos</span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn" data-ar="تحديث" data-en="update"></a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirmAction (event,'delete_playlist');" data-ar="حذف" data-en="delete" name="delete">
         </form>
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn" data-ar="عرض قائمة التشغيل" data-en="view playlist"></a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty" data-ar="لم تتم إضافة أي قائمة تشغيل حتى الآن!" data-en="no playlist added yet!"></p>';
      }
      ?>

   </div>

</section>













<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>