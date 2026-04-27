<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
    exit(); // Added exit to prevent further execution if not logged in
}

if (isset($_POST['submit'])) {

    $id = unique_id();
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $playlist = $_POST['playlist'];
    $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);

    $thumb = $_FILES['thumb']['name'];
    $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
    $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
    $rename_thumb = unique_id() . '.' . $thumb_ext;
    $thumb_size = $_FILES['thumb']['size'];
    $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
    $thumb_folder = '../uploaded_files/' . $rename_thumb;

    $video = $_FILES['video']['name'];
    $video = filter_var($video, FILTER_SANITIZE_STRING);
    $video_ext = pathinfo($video, PATHINFO_EXTENSION);
    $rename_video = unique_id() . '.' . $video_ext;
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_folder = '../uploaded_files/' . $rename_video;

    if ($thumb_size > 2000000) {
        $message[] = '<span data-ar="حجم الصورة كبير جدًا" data-en="Image size is too large!"></span>';
    } else {
        // Start a transaction
        $conn->beginTransaction();

        try {
            // Insert the content without requiring MCQs
            $add_playlist = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, video, thumb, status) VALUES(?,?,?,?,?,?,?,?)");
            $add_playlist->execute([$id, $tutor_id, $playlist, $title, $description, $rename_video, $rename_thumb, $status]);
            $last_id = $conn->lastInsertId();  // Get the last inserted ID
            
            move_uploaded_file($thumb_tmp_name, $thumb_folder);
            move_uploaded_file($video_tmp_name, $video_folder);

            // Check if the admin opted to add MCQs
            if (isset($_POST['add_mcq']) && !empty($_POST['questions'])) {
                $questions = $_POST['questions'];

                // Insert questions and options into the database
                foreach ($questions as $question) {
                    $question_text = filter_var($question['question'], FILTER_SANITIZE_STRING);

                    // Insert question into the `mcq` table
                    $insert_mcq = $conn->prepare("INSERT INTO `mcq` (content_id, question) VALUES (?, ?)");
                    $insert_mcq->execute([$last_id, $question_text]);
                    $mcq_id = $conn->lastInsertId();

                    // Insert options for this question
                    foreach ($question['options'] as $index => $option_text) {
                        $option_text = filter_var($option_text, FILTER_SANITIZE_STRING);
                        
                        // Adjust to ensure the correct option is set based on 1-based index
                        $is_correct = ($index == ($question['correct_option'])) ? 1 : 0;

                        $insert_option = $conn->prepare("INSERT INTO `mcq_options` (mcq_id, option_text, is_correct) VALUES (?, ?, ?)");
                        $insert_option->execute([$mcq_id, $option_text, $is_correct]);
                    }
                }
            }

            // Commit the transaction
            $conn->commit();

            $message[] = '<span data-ar="تم رفع فيديو جديد!" data-en="New video uploaded!"></span>';

        } catch (Exception $e) {
            // Rollback the transaction if an error occurred
            $conn->rollback();
            $message[] = '<span data-ar="حدث خطأ أثناء التحميل!" data-en="An error occurred during the upload!"></span>';
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
   <title data-ar="إضافة محتوى" data-en="Add content"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="video-form">

   <h1 class="heading" data-ar="إنشاء محتوى" data-en="upload content"></h1>

   <form action="" method="post" enctype="multipart/form-data">
      <p data-ar="حالة الفيديو" data-en="video status"> <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled data-ar="-- اختيار الحالة" data-en="-- select status"></option>
         <option value="active" data-ar="نشطة" data-en="active"></option>
         <option value="deactive" data-ar="غير نشطة" data-en="deactive"></option>
      </select>
      <p data-ar="اسم الفيديو" data-en="video title"> <span>*</span></p>
      <input type="text" name="title" maxlength="100" required data-ar="ادخل اسم الفيديو" data-en="enter video title" class="box">
      <p data-ar="وصف الفيديو" data-en="video description"> <span>*</span></p>
      <textarea name="description" class="box" required data-ar="اكتب الوصف" data-en="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p data-ar="فيديو قائمة التشغيل" data-en="video playlist"> <span>*</span></p>
      <select name="playlist" class="box" required>
         <option value="" disabled selected data-ar="--اختيار قائمة التشغيل" data-en="--select playlist"></option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         ?>
         <?php
         }else{
            echo '<option value="" disabled data-ar="لا توجد قائمة تشغيل مضافة!" data-en="no playlist created yet!"></option>';
         }
         ?>
      </select>
      <p data-ar="اختيار الصورة الرمزية" data-en="select thumbnail"> <span>*</span></p>
      <input type="file" name="thumb" accept="image/*" required class="box">
      <p data-ar="اختيار الفيديو" data-en="select video"> <span>*</span></p>
      <input type="file" name="video" accept="video/*" required class="box">
      
      <!-- Checkbox for adding MCQs -->
      <p>
         <input type="checkbox" id="add_mcq" name="add_mcq">
         <label for="add_mcq" data-ar="إضافة أسئلة اختيار متعدد" data-en="Add Multiple Choice Questions">Add Multiple Choice Questions</label>
      </p>

    <!-- MCQ Section -->
    <div id="mcq_section" style="display:none;">
        <!-- Loop for 4 Questions -->
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="mcq-question">
                <hr>
                <p data-ar="السؤال <?php echo $i; ?>" data-en="Question <?php echo $i; ?>"> <span>*</span></p>
                <input data-ar="ادخل السؤال" data-en="enter the question" type="text" name="questions[<?php echo $i; ?>][question]" class="box" maxlength="255">
                
                <!-- Loop for 4 Options -->
                <?php for ($j = 1; $j <= 4; $j++): ?>
                    <p data-ar="الخيار <?php echo $j; ?>" data-en="Option <?php echo $j; ?>"> <span>*</span></p>
                    <input data-ar="ادخل الاجابة" data-en="enter the answer" type="text" name="questions[<?php echo $i; ?>][options][<?php echo $j; ?>]" class="box" maxlength="255">
                <?php endfor; ?>
                
                <!-- Correct Answer Select -->
                <p data-ar="اختر الإجابة الصحيحة" data-en="Select the Correct Answer"> <span>*</span></p>
                <select name="questions[<?php echo $i; ?>][correct_option]" class="box">
                    <?php for ($k = 1; $k <= 4; $k++): ?>
                        <option value="<?php echo $k; ?>"><?php echo $k; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        <?php endfor; ?>
    </div>

      <input type="submit" data-ar="رفع الفيديو" data-en="upload video" value="upload content" name="submit" class="btn">
   </form>

</section>

<!-- Add your custom JavaScript for the page -->
<script>
document.addEventListener("DOMContentLoaded", function() {
   // Show/Hide the MCQ section based on the checkbox status
   const addMcqCheckbox = document.querySelector("#add_mcq");
   const mcqSection = document.querySelector("#mcq_section");

   addMcqCheckbox.addEventListener("change", function() {
      mcqSection.style.display = this.checked ? "block" : "none";
   });
});
</script>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>

</body>
</html>
