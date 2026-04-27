<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="الدورات" data-en="courses">الدورات</title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="preload" href="css/style-en.css" as="style">
   <link rel="preload" href="css/style-ar.css" as="style">
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Top categories section (banner) starts -->
<section class="quick-select">
   <h1 class="heading" data-en="top categories" data-ar="أهم الفئات">أهم الفئات</h1>
   <div class="box-container">
      <div class="box">
         <div class="flex">
            <a href="courses.php" class="category-link" data-category="all"><i class="fas fa-th"></i><span data-en="all" data-ar="الكل">الكل</span></a>
            <a href="courses.php?category_id=1" class="category-link" data-category="1"><i class="fas fa-code"></i><span data-en="development" data-ar="التطوير"></span></a>
            <a href="courses.php?category_id=2" class="category-link" data-category="2"><i class="fas fa-users-gear"></i><span data-en="Human Development" data-ar="تنمية بشرية"></span></a>
            <a href="courses.php?category_id=3" class="category-link" data-category="3"><i class="fas fa-pen"></i><span data-en="design" data-ar="التصميم"></span></a>
            <a href="courses.php?category_id=4" class="category-link" data-category="4"><i class="fas fa-chart-simple"></i><span data-en="business" data-ar="الأعمال"></span></a>
            <a href="courses.php?category_id=5" class="category-link" data-category="5"><i class="fas fa-chart-line"></i><span data-en="marketing" data-ar="التسويق"></span></a>
            <a href="courses.php?category_id=6" class="category-link" data-category="6"><i class="fas fa-language"></i><span data-en="Languages" data-ar="لغات"></span></a>
            <a href="courses.php?category_id=7" class="category-link" data-category="7"><i class="fas fa-cog"></i><span data-en="software" data-ar="البرمجيات"></span></a>
            <a href="courses.php?category_id=8" class="category-link" data-category="8"><i class="fas fa-vial"></i><span data-en="science" data-ar="العلوم"></span></a>
         </div>
      </div>
   </div>
</section>
<!-- Top categories section (banner) ends -->

<!-- courses section starts  -->
<section class="courses">

<h1 class="heading">
      <?php
      if(isset($_GET['category_id'])){
         $category_id = $_GET['category_id'];
         
         // هنا يمكن إضافة ترجمة لفئات الفئات حسب احتياجاتك

         $category_names = [
            1 => ['en' => 'development', 'ar' => 'التطوير'],
            2 => ['en' => 'Human Development', 'ar' => 'تنمية بشرية'],
            3 => ['en' => 'design', 'ar' => 'التصميم'],
            4 => ['en' => 'business', 'ar' => 'الأعمال'],
            5 => ['en' => 'marketing', 'ar' => 'التسويق'],
            6 => ['en' => 'Languages', 'ar' => 'لغات'],
            7 => ['en' => 'software', 'ar' => 'البرمجيات'],
            8 => ['en' => 'science', 'ar' => 'العلوم'],
         ];

         $category_name = $category_names[$category_id];
         echo '<span data-en="Courses in ' . htmlspecialchars($category_name['en']) . '" data-ar="دورات في ' . htmlspecialchars($category_name['ar']) . '">دورات في ' . htmlspecialchars($category_name['ar']) . '</span>';
      } else {
         echo '<span data-en="latest courses" data-ar="أحدث الدورات">أحدث الدورات</span>';
      }
      ?>
   </h1>


   <div class="box-container">

  <!-- استخراج الدورات حسب البحث -->

  <?php
         if(isset($category_id)){
            $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE category_id = ? AND status = ? ORDER BY date DESC LIMIT 6");
            $select_courses->execute([$category_id, 'active']);
         } else {
            $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
            $select_courses->execute(['active']);
         }

         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn" data-en="view playlist" data-ar="عرض قائمة التشغيل">عرض قائمة التشغيل</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty" data-ar="لم تتم إضافة أي دورات حتى الآن!" data-en="no courses added yet!"></p>';
      }
      ?>

   </div>

</section>
<!-- courses section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>