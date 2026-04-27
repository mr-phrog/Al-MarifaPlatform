<?php

include '../components/connect.php';

session_start(); // Start session management

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit(); // Ensure the script stops if not logged in
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:playlist.php');
   exit(); // Ensure the script stops if get_id is not set
}

if(isset($_POST['submit'])){

   // Sanitize and assign form data
   $title = $_POST['title'] ?? ''; 
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'] ?? ''; 
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $status = $_POST['status'] ?? ''; 
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   
   // Correct the category field name
   $new_category = $_POST['category'] ?? ''; 
   $new_category = filter_var($new_category, FILTER_SANITIZE_STRING);

   // Correct the SQL query and parameter order
   $update_playlist = $conn->prepare("UPDATE `playlist` SET title = ?, description = ?, status = ?, category_id = ? WHERE id = ?");
   $update_playlist->execute([$title, $description, $status, $new_category, $get_id]);

   // Handle the image upload
   $old_image = $_POST['old_image'] ?? ''; 
   $old_image = filter_var($old_image, FILTER_SANITIZE_STRING);
   $image = $_FILES['image']['name'] ?? ''; 
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'] ?? 0;
   $image_tmp_name = $_FILES['image']['tmp_name'] ?? ''; 
   $image_folder = '../uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = '<span data-ar="حجم الصورة كبير جدًا" data-en="Image size is too large!"></span>';
      }else{
         $update_image = $conn->prepare("UPDATE `playlist` SET thumb = ? WHERE id = ?");
         $update_image->execute([$rename, $get_id]);
         move_uploaded_file($image_tmp_name, $image_folder);
         if(!empty($old_image) && $old_image != $rename){
            if(file_exists('../uploaded_files/'.$old_image)){
               unlink('../uploaded_files/'.$old_image);
            }
         }
      }
   }

   // Success message
   $message[] = '<span data-ar="تم تحديث قائمة التشغيل!" data-en="Playlist updated!"></span>';
}

if(isset($_POST['delete'])){
   $delete_id = $_POST['playlist_id'] ?? ''; // Ensure the variable is set
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);

   if($fetch_thumb && !empty($fetch_thumb['thumb'])){
      $thumb_path = '../uploaded_files/'.$fetch_thumb['thumb'];
      if(file_exists($thumb_path)){
         unlink($thumb_path);
      }
   }

   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);

   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);

   header('location:playlists.php');
   exit(); // Ensure the script stops after deletion
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تحديث قائمة التشغيل" data-en="Update Playlist"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlist-form">

   <h1 class="heading" data-ar="تحديث قائمة التشغيل" data-en="Update Playlist"></h1>

   <?php
   $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ?");
   $select_playlist->execute([$get_id]);
   if($select_playlist->rowCount() > 0){
      while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
         $playlist_id = $fetch_playlist['id'];
         $count_videos = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ?");
         $count_videos->execute([$playlist_id]);
         $total_videos = $count_videos->rowCount();
         ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_playlist['thumb']; ?>">
      <p data-ar="حالة قائمة التشغيل" data-en="Playlist Status"><span>*</span></p>
      <select name="status" class="box" required>
         <option value="active" <?= ($fetch_playlist['status'] == 'active') ? 'selected' : ''; ?> data-ar="نشطة" data-en="active"></option>
         <option value="deactive" <?= ($fetch_playlist['status'] == 'deactive') ? 'selected' : ''; ?> data-ar="غير نشطة" data-en="deactive"></option>
      </select>
      <p data-ar="التصنيف" data-en="Category"> <span>*</span></p>
      <select name="category" class="box" required>
         <!-- Corrected the selected option -->
         <option value="<?= $fetch_playlist['category_id']; ?>" selected disabled data-ar="-- اختيار التصنيف" data-en="-- Select Category">-- Select Category --</option>
         <?php
            // جلب الفئات من جدول categories
            $select_categories = $conn->prepare("SELECT * FROM `categories`");
            $select_categories->execute();
            while($row = $select_categories->fetch(PDO::FETCH_ASSOC)){
                  // Ensure the correct display for multilingual support and selection
                  $selected = ($row['id'] == $fetch_playlist['category_id']) ? 'selected' : '';
                  echo '<option value="'.$row['id'].'" '.$selected.' data-ar="'.$row['name_ar'].'" data-en="'.$row['name_en'].'">'.$row['name'].'</option>';
            }
         ?>
      </select>

      <p data-ar="اسم قائمة التشغيل" data-en="playlist title"><span>*</span></p>
      <input type="text" name="title" maxlength="100" required data-ar="ادخل اسم قائمة التشغيل" data-en="enter playlist title" value="<?= $fetch_playlist['title']; ?>" class="box">
      <p data-ar="وصف قائمة التشغيل" data-en="playlist description"><span>*</span></p>
      <textarea name="description" class="box" required data-ar="اكتب الوصف" data-en="write description" maxlength="1000" cols="30" rows="10"><?= $fetch_playlist['description']; ?></textarea>
      <p data-ar="الصورة المصغرة لقائمة التشغيل" data-en="playlist thumbnail"><span>*</span></p>
      <div class="thumb">
         <span><?= $total_videos; ?></span>
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" value="update playlist" name="submit" data-ar="تحديث" data-en="update playlist" class="btn">
      <div class="flex-btn">
         <input type="submit" value="delete" data-ar="حذف" data-en="delete" class="delete-btn" onclick="return confirmAction (event,'delete_playlist');" name="delete">
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" data-ar="عرض قائمة التشغيل" data-en="view playlist" class="option-btn"></a>
      </div>
   </form>
   <?php
      } 
   }else{
      echo '<p class="empty" data-ar="لا توجد قائمة تشغيل مضافة حتى الآن!" data-en="no playlist added yet!" ></p>';
   }
   ?>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>