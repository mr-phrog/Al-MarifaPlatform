<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
      $tutor_id = $_COOKIE['tutor_id'];
} else {
      $tutor_id = '';
      header('location:login.php');
      exit(); // Ensure the script stops executing after the redirect
}

if(isset($_POST['submit'])){

      $select_tutor = $conn->prepare("SELECT * FROM tutors WHERE id = ? LIMIT 1");
      $select_tutor->execute([$tutor_id]);
      $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

      $prev_pass = $fetch_tutor['password'];
      $prev_image = $fetch_tutor['image'];

      $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : '';
      $profession = isset($_POST['profession']) ? filter_var($_POST['profession'], FILTER_SANITIZE_STRING) : '';
      $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_STRING) : '';

      if(!empty($name)){
         $update_name = $conn->prepare("UPDATE tutors SET name = ? WHERE id = ?");
         $update_name->execute([$name, $tutor_id]);
         $message[] = '<span data-ar="تم تحديث اسم المستخدم بنجاح!" data-en="username updated successfully!"></span>';
      }

      if(!empty($profession)){
         // Map the selected profession to both English and Arabic
         $professions = [
            'developer' => 'مطور',
            'designer' => 'مصمم',
            'musician' => 'موسيقي',
            'biologist' => 'أحيائي',
            'teacher' => 'مدرس',
            'engineer' => 'مهندس',
            'lawyer' => 'محامي',
            'accountant' => 'محاسب',
            'doctor' => 'طبيب',
            'journalist' => 'صحفي',
            'photographer' => 'مصور'
         ];

         $profession_ar = isset($professions[$profession]) ? $professions[$profession] : '';

         $update_profession = $conn->prepare("UPDATE tutors SET profession = ?, profession_ar = ? WHERE id = ?");
         $update_profession->execute([$profession, $profession_ar, $tutor_id]);
         $message[] = '<span data-ar="تم تحديث المهنة بنجاح!" data-en="profession updated successfully!"></span>';
      }

   if(!empty($email)){
      $select_email = $conn->prepare("SELECT email FROM tutors WHERE id = ? AND email = ?");
      $select_email->execute([$tutor_id, $email]);
      if($select_email->rowCount() > 0){
            $message[] = '<span data-ar="البريد الالكتروني تم إستعماله من قبل!" data-en="email already taken!"></span>';
      } else {
            $update_email = $conn->prepare("UPDATE tutors SET email = ? WHERE id = ?");
            $update_email->execute([$email, $tutor_id]);
            $message[] = '<span data-ar="تم تحديث البريد الالكتروني بنجاح!" data-en="email updated successfully!"></span>';
      }
   }

   $image = isset($_FILES['image']['name']) ? filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING) : '';
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = isset($_FILES['image']['size']) ? $_FILES['image']['size'] : 0;
   $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';
   $image_folder = '../uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
            $message[] = '<span data-ar="حجم الصورة كبير جدًا!" data-en="Image size is too large!"></span>';
      } else {
            $update_image = $conn->prepare("UPDATE tutors SET image = ? WHERE id = ?");
            $update_image->execute([$rename, $tutor_id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            if($prev_image != '' AND $prev_image != $rename){
               unlink('../uploaded_files/'.$prev_image);
            }
            $message[] = '<span data-ar="تم تحديث الصورة الشخصية بنجاح!" data-en="image updated successfully!"></span>';
      }
   }

      $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
      $old_pass = isset($_POST['old_pass']) ? sha1($_POST['old_pass']) : '';
      $new_pass = isset($_POST['new_pass']) ? sha1($_POST['new_pass']) : '';
      $cpass = isset($_POST['cpass']) ? sha1($_POST['cpass']) : '';

      if($old_pass != $empty_pass){
         if($old_pass != $prev_pass){
            $message[] = '<span data-ar="كلمة المرور القديمة غير متطابقة!" data-en="old password not match!"></span>';
         } elseif($new_pass != $cpass){
            $message[] = '<span data-ar="كلمة المرور غير متطابقة!" data-en="confirm password not match!"></span>';
         } else {
            if($new_pass != $empty_pass){
                  $update_pass = $conn->prepare("UPDATE tutors SET password = ? WHERE id = ?");
                  $update_pass->execute([$cpass, $tutor_id]);
                  $message[] = '<span data-ar="تم تحديث كلمة المرور بنجاح!" data-en="password updated successfully!"></span>';
            } else {
                  $message[] = '<span data-ar="من فضلك، ادخل كلمة مرور جديدة!" data-en="enter a new password, please!"></span>';
            }
         }
      }

   }

// Fetch the profile data to display in the form
$select_profile = $conn->prepare("SELECT * FROM tutors WHERE id = ? LIMIT 1");
$select_profile->execute([$tutor_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// Determine the current language
$current_language = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'en'; // Default to English

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تحديث الملف الشخصي" data-en="Update Profile"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/all.min.css">
   <link id="style" rel="stylesheet" href="../css/admin_style-<?php echo $current_language; ?>.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<!-- register section starts  -->

<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3 data-ar="تحديث الملف الشخصي" data-en="update profile"></h3>
      <div class="flex">
         <div class="col">
            <p data-ar="اسمك" data-en="your name"></p>
            <input type="text" name="name" placeholder="<?= htmlspecialchars($fetch_profile['name']); ?>" maxlength="50" class="box">
            <p data-ar="مهنتك" data-en="your profession"></p>
            <select name="profession" class="box">
               <?php
               $professions = [
                  'developer' => 'مطور',
                  'designer' => 'مصمم',
                  'musician' => 'موسيقي',
                  'biologist' => 'أحيائي',
                  'teacher' => 'مدرس',
                  'engineer' => 'مهندس',
                  'lawyer' => 'محامي',
                  'accountant' => 'محاسب',
                  'doctor' => 'طبيب',
                  'journalist' => 'صحفي',
                  'photographer' => 'مصور'
               ];

               foreach ($professions as $key => $value) {
                  $selected = ($current_language == 'ar' && $fetch_profile['profession_ar'] == $value) || ($current_language == 'en' && $fetch_profile['profession'] == $key) ? 'selected' : '';
                  echo '<option value="' . $key . '" data-ar="' . $value . '" data-en="' . $key . '" ' . $selected . '>' . ($current_language == 'ar' ? $value : $key) . '</option>';
               }
               ?>
            </select>
            <p data-ar="بريدك الالكتروني" data-en="your email "></p>
            <input type="email" name="email" placeholder="<?= htmlspecialchars($fetch_profile['email']); ?>" maxlength="20" class="box">
         </div>
         <div class="col">
            <p data-ar="كلمة السر القديمة :" data-en="old password :"></p>
            <input type="password" name="old_pass" data-ar="ادخل كلمة السر القديمة" data-en="enter your old password" maxlength="20" class="box">
            <p data-ar="كلمة السر الجديدة :" data-en="new password :"></p>
            <input type="password" name="new_pass" data-ar="ادخل كلمة السر الجديدة" data-en="enter your new password" maxlength="20" class="box">
            <p data-ar="تأكيد كلمة السر :" data-en="confirm password :"></p>
            <input type="password" name="cpass" data-ar="تأكيد كلمة السر الجديدة" data-en="confirm your new password" maxlength="20" class="box">
         </div>
      </div>
      <p data-ar="الصورة الرمزية :" data-en="update picture :"></p>
      <input type="file" name="image" accept="image/*" class="box">
      <input type="submit" name="submit" value="update now" data-ar="تحديث" data-en="Update" class="btn">
   </form>

</section>

<!-- register section ends -->

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>
