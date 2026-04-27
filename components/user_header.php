<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
<div id="loader" class="loader"></div>

<header class="header">
   <section class="flex">
      <div class="logo-container">
         <img src="images/logo-1.png" alt="Logo" class="logo-img">
         <a href="home.php" class="logo" data-en="Al-Maarifa" data-ar="منصة المعرفة"></a>
      </div>

      <form action="search_course.php" method="post" class="search-form">
         <input type="text" name="search_course" data-ar="ابحث عن دورات تعليمية ..." data-en="search courses..." title="بحث الكورسات" required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_course_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
         <div id="lang-switcher" onclick="switchLanguage()" class="fa fa-globe"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span data-en="Student" data-ar="طالب">طالب</span>
         <a href="profile.php" class="btn" data-en="view profile" data-ar="الملف الشخصي"></a>
         <a href="components/user_logout.php" onclick="return confirmAction (event,'logout');" class="delete-btn" data-ar="تسجيل الخروج" data-en="logout"></a>
         <?php
            }else{
         ?>
         <h3 data-ar="من فضلك، قم بتسجيل الدخول أو إنشاء حساب" data-en="please login or register"></h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn" data-ar="تسجيل الدخول" data-en="login"></a>
            <a href="register.php" class="option-btn" data-ar="إنشاء حساب" data-en="register"></a>
         </div>
         <?php
            }
         ?>
      </div>
   </section>
</header>

<!-- header section ends -->

<!-- side bar section starts  -->

<div class="side-bar">

   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <span data-en="Student" data-ar="طالب"></span>
         <a href="profile.php" class="btn" data-en="view profile" data-ar="الملف الشخصي"></a>
         <?php
            }else{
         ?>
         <h3 data-en="please login or register" data-ar="الرجاء تسجيل الدخول أو التسجيل"></h3>
         <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn" data-en="login" data-ar="تسجيل"></a>
            <a href="register.php" class="option-btn" data-en="register" data-ar="انضم"></a>
         </div>
         <?php
            }
         ?>
      </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span data-en="Home" data-ar="الرئيسية"></span></a>
      <a href="courses.php"><i class="fa-solid fa-video"></i><span data-en="Courses" data-ar="الدورات"></span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span data-en="Teachers" data-ar="المعلمين"></span></a>
      <a href="books.php"><i class="fa-solid fa-book"></i><span data-en="Books" data-ar="الكتب"></span></a>
      <a href="assistant.php"><i class="fas fa-robot"></i><span data-en="Smart Assistant" data-ar="مساعدك الذكي"></span></a>
      <a href="about.php"><i class="fas fa-question"></i><span data-en="About us" data-ar="عننا"></span></a>
   </nav>

</div>

<!-- side bar section ends -->

