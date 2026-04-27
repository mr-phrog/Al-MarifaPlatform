<?php
session_start(); // Start session to use $_SESSION

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
   exit();
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:dashboard.php');
   exit();
}

// Process updates if the form is submitted
if(isset($_POST['update'])) {
   // Sanitize and validate inputs
   $video_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $playlist = filter_var($_POST['playlist'], FILTER_SANITIZE_STRING);

   // Update content
   $update_content = $conn->prepare("UPDATE `content` SET title = ?, description = ?, status = ? WHERE id = ?");
   $update_content->execute([$title, $description, $status, $video_id]);

   if(!empty($playlist)){
      $update_playlist = $conn->prepare("UPDATE `content` SET playlist_id = ? WHERE id = ?");
      $update_playlist->execute([$playlist, $video_id]);
   }

   // Process thumbnail update
   $old_thumb = filter_var($_POST['old_thumb'], FILTER_SANITIZE_STRING);
   $thumb = filter_var($_FILES['thumb']['name'], FILTER_SANITIZE_STRING);
   $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
   $rename_thumb = unique_id().'.'.$thumb_ext;
   $thumb_size = $_FILES['thumb']['size'];
   $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
   $thumb_folder = '../uploaded_files/'.$rename_thumb;

   if(!empty($thumb)){
      if($thumb_size > 2000000){
         $message[] = '<span data-ar="حجم الصورة كبير جدًا" data-en="Image size is too large!"></span>';
      }else{
         $update_thumb = $conn->prepare("UPDATE `content` SET thumb = ? WHERE id = ?");
         $update_thumb->execute([$rename_thumb, $video_id]);
         move_uploaded_file($thumb_tmp_name, $thumb_folder);
         if($old_thumb != '' AND $old_thumb != $rename_thumb){
            unlink('../uploaded_files/'.$old_thumb);
         }
      }
   }

   // Process video update
   $old_video = filter_var($_POST['old_video'], FILTER_SANITIZE_STRING);
   $video = filter_var($_FILES['video']['name'], FILTER_SANITIZE_STRING);
   $video_ext = pathinfo($video, PATHINFO_EXTENSION);
   $rename_video = unique_id().'.'.$video_ext;
   $video_tmp_name = $_FILES['video']['tmp_name'];
   $video_folder = '../uploaded_files/'.$rename_video;

   if(!empty($video)){
      $update_video = $conn->prepare("UPDATE `content` SET video = ? WHERE id = ?");
      $update_video->execute([$rename_video, $video_id]);
      move_uploaded_file($video_tmp_name, $video_folder);
      if($old_video != '' AND $old_video != $rename_video){
         unlink('../uploaded_files/'.$old_video);
      }
   }


    // Handle MCQ updates
    if (isset($_POST['add_mcq'])) {
      foreach ($_POST['mcq'] as $mcq_id => $mcq_data) {
          $question = filter_var($mcq_data['question'], FILTER_SANITIZE_STRING);

          // Update MCQ question
          $update_mcq = $conn->prepare("UPDATE `mcq` SET question = ? WHERE id = ?");
          $update_mcq->execute([$question, $mcq_id]);

          // Update MCQ options
          $option_counter = 1; // Initialize option counter
          foreach ($mcq_data['options'] as $option_id => $option_text) {
              $option_text = filter_var($option_text, FILTER_SANITIZE_STRING);

              $update_option = $conn->prepare("UPDATE `mcq_options` SET option_text = ? WHERE id = ?");
              $update_option->execute([$option_text, $option_id]);
              $option_counter++; // Increment option counter
          }

          // Update correct option
          $correct_option = filter_var($mcq_data['correct_option'], FILTER_SANITIZE_NUMBER_INT);
          $update_correct_option = $conn->prepare("UPDATE `mcq_options` SET is_correct = 0 WHERE mcq_id = ?");
          $update_correct_option->execute([$mcq_id]);

          $set_correct_option = $conn->prepare("UPDATE `mcq_options` SET is_correct = 1 WHERE id = ?");
          $set_correct_option->execute([$correct_option]);
      }
  }

  $message[] = '<span data-ar="تم تحديث المحتوى!" data-en="Content updated!"></span>';
}

// Retrieve existing MCQs for the content
$mcq_data = [];
$mcq_query = $conn->prepare("SELECT * FROM `mcq` WHERE content_id = ?");
$mcq_query->execute([$get_id]);

if ($mcq_query->rowCount() > 0) {
  $mcq_data = $mcq_query->fetchAll(PDO::FETCH_ASSOC);
  $mcq_options_data = [];
  foreach ($mcq_data as $mcq) {
      $mcq_id = $mcq['id'];
      $options_query = $conn->prepare("SELECT * FROM `mcq_options` WHERE mcq_id = ?");
      $options_query->execute([$mcq_id]);
      $mcq_options_data[$mcq_id] = $options_query->fetchAll(PDO::FETCH_ASSOC);
  }
}

$currentLang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';



// Handle video deletion
if(isset($_POST['delete_video'])){
   $delete_id = filter_var($_POST['video_id'], FILTER_SANITIZE_STRING);

   $delete_video_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
   $delete_video_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
   if($fetch_thumb) {
      unlink('../uploaded_files/'.$fetch_thumb['thumb']);
   }

   $delete_video = $conn->prepare("SELECT video FROM `content` WHERE id = ? LIMIT 1");
   $delete_video->execute([$delete_id]);
   $fetch_video = $delete_video->fetch(PDO::FETCH_ASSOC);
   if($fetch_video) {
      unlink('../uploaded_files/'.$fetch_video['video']);
   }

   $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
   $delete_likes->execute([$delete_id]);
   $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
   $delete_comments->execute([$delete_id]);

   $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ?");
   $delete_content->execute([$delete_id]);
   header('location:contents.php');
   exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تحديث المحتوى" data-en="Update video"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="video-form">

   <h1 class="heading" data-ar="تحديث المحتوى" data-en="update content"></h1>

   <?php
      $select_videos = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND tutor_id = ?");
      $select_videos->execute([$get_id, $tutor_id]);
      if($select_videos->rowCount() > 0){
         while($fetch_videos = $select_videos->fetch(PDO::FETCH_ASSOC)){
            $video_id = $fetch_videos['id'];
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="video_id" value="<?= $fetch_videos['id']; ?>">
      <input type="hidden" name="old_thumb" value="<?= $fetch_videos['thumb']; ?>">
      <input type="hidden" name="old_video" value="<?= $fetch_videos['video']; ?>">
      <p data-ar="تحديث الحالة" data-en="update status"><span>*</span></p>
      <select name="status" class="box" required>
         <option value="active" <?= ($fetch_videos['status'] == 'active') ? 'selected' : ''; ?> data-ar="نشطة" data-en="active"></option>
         <option value="deactive" <?= ($fetch_videos['status'] == 'deactive') ? 'selected' : ''; ?> data-ar="غير نشطة" data-en="deactive"></option>
      </select>
      <p data-ar="تحديث العنوان" data-en="update title"><span>*</span></p>
      <input type="text" name="title" maxlength="100" required data-ar="ادخل عنوان الفيديو" data-en="enter video title" class="box" value="<?= $fetch_videos['title']; ?>">
      <p data-ar="تحديث الوصف" data-en="update description"><span>*</span></p>
      <textarea name="description" class="box" required data-ar="اكتب الوصف" data-en="write description" maxlength="1000" cols="30" rows="10"><?= $fetch_videos['description']; ?></textarea>
      <p data-ar="فيديو قائمة التشغيل" data-en="video playlist"> <span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled data-ar="--اختيار قائمة التشغيل" data-en="--select playlist"></option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>" <?= ($fetch_playlist['id'] == $fetch_videos['playlist_id']) ? 'selected' : ''; ?>><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         }else{
            echo '<option value="" disabled data-ar="لا توجد قائمة تشغيل مضافة!" data-en="no playlist created yet!"></option>';
         }
         ?>
      </select>
      <img src="../uploaded_files/<?= $fetch_videos['thumb']; ?>" alt="">
      <p data-ar="تحديث الصورة المصغرة" data-en="update thumbnail"></p>
      <input type="file" name="thumb" accept="image/*" class="box">
      <video src="../uploaded_files/<?= $fetch_videos['video']; ?>" controls></video>
      <p data-ar="تحديث الفيديو" data-en="update video"></p>
      <input type="file" name="video" accept="video/*" class="box">
      <p>
      <input type="checkbox" name="add_mcq" <?= ($mcq_query->rowCount() > 0) ? 'checked' : 'disabled'; ?>>
         <label data-ar="إضافة أسئلة اختيار من متعدد" data-en="Add Multiple-Choice Questions (MCQs)"></label>
      </p>

      <?php if ($mcq_query->rowCount() > 0): ?>
        <div class="mcq-section">
            <?php 
            $option_number = 1; // Initialize option counter for display
            foreach ($mcq_data as $mcq): ?>
               <hr>
                <p data-ar="تحديث سؤال" data-en="update question"></p>
                <input type="text" name="mcq[<?= $mcq['id'] ?>][question]" value="<?= $mcq['question'] ?>" class="box" required>
                <p data-ar="تحديث الخيارات" data-en="update options"></p>

                <?php 
                $option_counter = 1; // Reset option counter for each MCQ
                foreach ($mcq_options_data[$mcq['id']] as $option): ?>
                    <!-- Display each option -->
                    <input type="text" name="mcq[<?= $mcq['id'] ?>][options][<?= $option['id'] ?>]" value="<?= $option['option_text'] ?>" class="box" required>
                    
                    <?php
                        $option_counter++; // Increment option counter
                    ?>
                <?php endforeach; ?>

                <!-- Correct Answer Select -->
                <p data-ar="اختر الإجابة الصحيحة" data-en="Select the Correct Answer"> <span>*</span></p>
                <select name="mcq[<?= $mcq['id'] ?>][correct_option]" class="box">
                    <?php 
                    $option_counter = 1; // Reset option counter for each MCQ
                    foreach ($mcq_options_data[$mcq['id']] as $option): ?>
                        <option value="<?= $option['id'] ?>" <?= ($option['is_correct'] == 1) ? 'selected' : ''; ?>>
                            <?= $option_counter ?> 
                        </option>
                        <?php
                        $option_counter++; // Increment option counter
                        ?>
                    <?php endforeach; ?>
                </select>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <input type="submit" value="تحديث" data-ar="تحديث" data-en="Update" name="update" class="btn">
        <input type="submit" value="حذف الفيديو" data-ar="حذف الفيديو" data-en="Delete video" name="delete_video" class="delete-btn" onclick="return confirm('Delete this video?');">
    </form>
    <?php
        }
    } else {
        echo '<p class="empty" data-ar="لم يتم العثور على فيديو!" data-en="No video found!"></p>';
    }

   ?>

</section>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>