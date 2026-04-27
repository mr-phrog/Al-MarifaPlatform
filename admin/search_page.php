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
      $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
      $delete_comments->execute([$delete_id]);
      $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
      $delete_content->execute([$delete_id]);
      $message[] = '<span data-ar="تم حذف الفيديو!" data-en="video deleted!"></span>';
   }else{
      $message[] = '<span data-ar="تم حذف الفيديو بالفعل!" data-en="video already deleted!"></span>';
   }
}

if(isset($_POST['delete_playlist'])){
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

if(isset($_POST['delete_book_file'])){
   $delete_id = $_POST['book_file_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $verify_book_file = $conn->prepare("SELECT * FROM `books` WHERE id = ? LIMIT 1");
   $verify_book_file->execute([$delete_id]);
   if($verify_book_file->rowCount() > 0){
      $delete_book_file_book_image = $conn->prepare("SELECT * FROM `books` WHERE id = ? LIMIT 1");
      $delete_book_file_book_image->execute([$delete_id]);
      $fetch_book_image = $delete_book_file_book_image->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_book_image['book_image']);
      $delete_book_file = $conn->prepare("SELECT * FROM `books` WHERE id = ? LIMIT 1");
      $delete_book_file->execute([$delete_id]);
      $fetch_book_file = $delete_book_file->fetch(PDO::FETCH_ASSOC);
      unlink('../uploaded_files/'.$fetch_book_file['book_file']);
      $delete_books = $conn->prepare("DELETE FROM `books` WHERE id = ?");
      $delete_books->execute([$delete_id]);
      $message[] = '<span data-ar="تم حذف الكتاب!" data-en="book deleted!"></span>';
   }else{
      $message[] = '<span data-ar="الكتاب محذوف بالفعل!" data-en="book already deleted!"></span>';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="البحث" data-en="Search"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="contents">

   <h1 class="heading" data-ar="المحتويات" data-en="contents"></h1>

   <div class="box-container">

   <?php
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search'];
         // Use a prepared statement with a placeholder for the search term
         $select_videos = $conn->prepare("SELECT * FROM `content` WHERE title LIKE ? AND tutor_id = ? ORDER BY date DESC");
         // Execute the prepared statement with the search term wrapped in wildcards for partial matching
         $select_videos->execute(['%' . $search . '%', $tutor_id]);
         if($select_videos->rowCount() > 0){
            while($fecth_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){ 
               $video_id = $fecth_videos['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span data-ar="نشطة" data-en="active" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= htmlspecialchars($fecth_videos['status']); ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= htmlspecialchars($fecth_videos['date']); ?></span></div>
         </div>
         <img src="../uploaded_files/<?= htmlspecialchars($fecth_videos['thumb']); ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($fecth_videos['title']); ?></h3>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="video_id" value="<?= htmlspecialchars($video_id); ?>">
            <a href="update_content.php?get_id=<?= htmlspecialchars($video_id); ?>" class="option-btn" data-ar="تحديث" data-en="update"> </a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirmAction (event,'delete_video');" data-ar="حذف" data-en="delete" name="delete_video">
         </form>
         <a href="view_content.php?get_id=<?= htmlspecialchars($video_id); ?>" class="btn" data-ar="عرض المحتوى" data-en="view content"></a>
      </div>
   <?php
            }
         }else{
            echo '<p class="empty" data-ar="لا توجد محتويات!" data-en="no contents found!"></p>';
         }
      }else{
         echo '<p class="empty" data-ar="الرجاء البحث عن شيء!" data-en="please search something!"></p>';
      }
   ?>

   </div>

</section>

<section class="playlists">

   <h1 class="heading" data-ar="قوائم التشغيل" data-en="playlists"></h1>

   <div class="box-container">
   
      <?php
      if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search'];
         // Use a prepared statement with a placeholder for the search term
         $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE ? AND tutor_id = ? ORDER BY date DESC");
         // Execute the prepared statement with the search term wrapped in wildcards for partial matching
         $select_playlist->execute(['%' . $search . '%', $tutor_id]);
         if($select_playlist->rowCount() > 0){
            while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
               $playlist_id = $fetch_playlist['id'];
               $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
               $count_videos->execute([$playlist_id]);
               $total_videos = $count_videos->rowCount();
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span data-ar="نشطة" data-en="active" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span><?= $total_videos; ?></span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn" data-ar="تحديث" data-en="update"></a>
            <input type="submit" value="delete_playlist" class="delete-btn" onclick="return confirmAction (event,'delete_playlist');" name="delete" data-ar="حذف" data-en="delete">
         </form>
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn" data-ar="عرض قائمة التشغيل" data-en="view playlist"> </a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty" data-ar="لا توجد قوائم تشغيل!" data-en="no playlists found!"></p>';
      }}else{
         echo '<p class="empty" data-ar="الرجاء البحث عن شيء!" data-en="please search something!"></p>';
      }
      ?>

   </div>

</section>

<section class="books">

   <h1 class="heading" data-ar="الكتب" data-en="books"></h1>
   <div class="box-container">
      <?php
         if(isset($_POST['search']) or isset($_POST['search_btn'])){
         $search = $_POST['search'];
         $select_book__file = $conn->prepare("SELECT * FROM `books` WHERE title LIKE ? AND tutor_id = ? ORDER BY upload_date DESC");
         $select_book__file->execute(['%' . $search . '%', $tutor_id]);
         if($select_book__file->rowCount() > 0){
            while($fecth_book__file = $select_book__file->fetch(PDO::FETCH_ASSOC)){ 
               $book_file_id = $fecth_book__file['id'];
      ?>
      <div class="box">
         <div class="flex">
         <div>
            <i class="fas fa-calendar"></i>
            <span>
                  <?php
                  $date = new DateTime($fecth_book__file['upload_date']);
                  echo $date->format('Y-m-d');
                  ?>
            </span>
         </div>
      </div>
      <img src="../uploaded_files/<?= $fecth_book__file['book_image']; ?>" class="book-image" alt="">
      <h3 class="title"><?= $fecth_book__file['title']; ?></h3>
      <form action="" method="post" class="flex-btn">
         <input type="hidden" name="book_file_id" value="<?= $book_file_id; ?>">
         <a href="update_book.php?get_id=<?= $book_file_id; ?>" class="option-btn" data-ar="تحديث" data-en="update"></a>
         <input type="submit" value="delete" class="delete-btn" name="delete_book_file" onclick="return confirmAction (event,'delete_book');" data-ar="حذف الكتاب" data-en="delete book">
      </form>
      <?php
      if (file_exists('../uploaded_files/' . $fecth_book__file['book_file'])) {
         echo '<a href="../uploaded_files/' . htmlspecialchars($fecth_book__file['book_file']) . '" target="_blank" class="btn" data-ar="قراءة" data-en="Read"></a>';
      } else {
         echo '<p>File not found: ' . htmlspecialchars($fecth_book__file['book_file']) . '</p>';
      }
      ?>                
      </div>
   <?php
         }
      }else{
         echo '<p class="empty" data-ar="لم يتم إيجاد الكتاب!" data-en="no books founds!"></p>';
      }
   }else{
      echo '<p class="empty" data-ar="الرجاء البحث عن شيء!" data-en="please search something!"></p>';
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