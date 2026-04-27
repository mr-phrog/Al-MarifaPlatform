<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-en="Home" data-ar="الرئيسية">الرئيسية</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- quick select section starts  -->
<section class="quick-select">

   <h1 class="heading" data-en="quick options" data-ar="خيارات سريعة">خيارات سريعة</h1>
   <div class="box-container">

      <?php
         if($user_id != ''){
      ?>
      <div class="box">
         <h3 class="title" data-en="likes and comments" data-ar="الاعجابات والتعليقات"></h3>
         <p data-ar="مجموع الاعجابات" data-en="total likes"> : <span><?= $total_likes; ?></span></p>
         <a href="likes.php" class="inline-btn" data-en="view likes" data-ar="عرض الاعجابات"></a>
         <p data-ar="مجموع التعليقات" data-en="total comments"> : <span><?= $total_comments; ?></span></p>
         <a href="comments.php" class="inline-btn" data-en="view comments" data-ar="عرض التعليقات">عرض التعليقات</a>
         <p data-ar="قوائم التشغيل المحفوظة" data-en="Saved bookmarked"> : <span><?= $total_bookmarked; ?></span></p>
         <a href="bookmark.php" class="inline-btn" data-en="view bookmark" data-ar="عرض الاشارة المرجعية">عرض الاشارة المرجعية</a>
      </div>
      <?php
         }else{ 
      ?>
      <div class="box" style="text-align: center;">
         <h3 class="title" data-ar="من فضلك، قم بالتسجيل الدخول أو التسحيل" data-en="please login or register"></h3>
         <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn" data-ar="دخول" data-en="login"></a>
            <a href="register.php" class="option-btn" data-ar="تسجيل" data-en="register"></a>
         </div>
      </div>
      <?php
      }
      ?>

<!-- بحث و تفية حسب الفئة المختارة -->
<div class="box">
   <h3 class="title" data-en="top categories" data-ar="أهم الفئات">أهم الفئات</h3>
   <div class="flex">
         <a href="home.php" class="category-link" data-category="all"><i class="fas fa-th"></i><span data-en="all" data-ar="الكل">الكل</span></a>
         <a href="home.php?category_id=1" class="category-link" data-category="1"><i class="fas fa-code"></i><span data-en="development" data-ar="التطوير"></span></a>
         <a href="home.php?category_id=2" class="category-link" data-category="2"><i class="fas fa-users-gear"></i><span data-en="Human Development" data-ar="تنمية بشرية"></span></a>
         <a href="home.php?category_id=3" class="category-link" data-category="3"><i class="fas fa-pen"></i><span data-en="design" data-ar="التصميم"></span></a>
         <a href="home.php?category_id=4" class="category-link" data-category="4"><i class="fas fa-chart-simple"></i><span data-en="business" data-ar="الأعمال"></span></a>
         <a href="home.php?category_id=5" class="category-link" data-category="5"><i class="fas fa-chart-line"></i><span data-en="marketing" data-ar="التسويق"></span></a>
         <a href="home.php?category_id=6" class="category-link" data-category="6"><i class="fas fa-language"></i><span data-en="Languages" data-ar="لغات"></span></a>
         <a href="home.php?category_id=7" class="category-link" data-category="7"><i class="fas fa-cog"></i><span data-en="software" data-ar="البرمجيات"></span></a>
         <a href="home.php?category_id=8" class="category-link" data-category="8"><i class="fas fa-vial"></i><span data-en="science" data-ar="العلوم"></span></a>
   </div>
</div>

      <div class="box tutor">
         <h3 class="title" data-en="become a tutor" data-ar="أصبح مدرس"></h3>
         <p data-en="If you have skills in providing high-quality courses, join us as a teacher" data-ar="إذا كان لديك مهارات في تقديم دورات ذو جودة عالية، انضم معانا كمدرس"></p>
         <a href="admin/register.php" class="inline-btn" data-en="get started" data-ar="البدء"></a>
      </div>

   </div>

</section>
<!-- quick select section ends -->

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
         echo '<p class="empty" data-ar= "لم تتم إضافة أي دورات حتى الآن!" data-en= "no courses added yet!" ></p>';
      }
      ?>
   </div>

   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn" data-en="view more" data-ar="عرض المزيد"></a>
   </div>
</section>
<!-- courses section ends -->

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js" defer></script>
<script src="js/switcher.js" defer></script>
   
</body>
</html>