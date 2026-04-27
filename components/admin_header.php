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

      <a href="dashboard.php" class="logo" data-ar="المشرف" data-en="Admin"></a>

      <form action="search_page.php" method="post" class="search-form">
         <input type="text" name="search" data-ar="ابحث هنا ..." data-en="search here..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_btn"></button>
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
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span data-ar="<?= htmlspecialchars($fetch_profile['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_profile['profession']); ?>">
            <?= htmlspecialchars($fetch_profile['profession_ar']); // default to Arabic ?> 
         </span>   
         <a href="profile.php" class="btn" data-ar="عرض الملف الشخصي" data-en="view profile"></a>
         <!-- <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div> -->
         <a href="../components/admin_logout.php" onclick="return confirmAction (event,'logout');" class="delete-btn" data-ar="تسجيل الخروج" data-en="logout"></a>
         <?php
            }else{
         ?>
         <h3 data-ar="يرجي تسجيل الدخول أو تسجيل حساب جديد" data-en="please login or register"></h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn" data-ar="تسجيل الدخول" data-en="login"></a>
            <a href="register.php" class="option-btn" data-ar="تسجيل حساب جديد" data-en="register"></a>
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
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span data-ar="<?= htmlspecialchars($fetch_profile['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_profile['profession']); ?>">
            <?= htmlspecialchars($fetch_profile['profession_ar']); // default to Arabic ?> 
         </span>   
         <a href="profile.php" class="btn" data-ar="الملف الشخصي" data-en="view profile"></a>
         <?php
            }else{
         ?>
         <h3 data-ar="يرجى تسجيل الدخول أو تسجيل حساب جديد" data-en="please login or register"></h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn" data-ar="تسجيل الدخول" data-en="login"></a>
            <a href="register.php" class="option-btn" data-ar="تسجيل حساب جديد" data-en="register"></a>
         </div>
         <?php
            }
         ?>
      </div>

   <nav class="navbar">
      <a href="dashboard.php"><i class="fas fa-home"></i><span data-ar="الرئيسية" data-en="Home"></span></a>
      <a href="overview.php"><i class="fas fa-chart-pie"></i><span data-ar="نظرة عامة" data-en="Overviews"></span></a>
      <a href="playlists.php"><i class="fa-solid fa-bars-staggered"></i><span data-ar="قوائم التشغيل" data-en="Playlists"></span></a>
      <a href="contents.php"><i class="fas fa-graduation-cap"></i><span data-ar="المحتويات" data-en="Contents"></span></a>
      <a href="comments.php"><i class="fas fa-comment"></i><span data-ar="التعليقات" data-en="Comments"></span></a>
      <a href="books.php"><i class="fas fa-book"></i><span data-ar="الكتب" data-en="Books"></span></a>
      <a href="../components/admin_logout.php" onclick="return confirmAction (event,'logout');"><i class="fas fa-right-from-bracket"></i><span data-ar="تسجيل الخروج" data-en="logout"></span></a>
   </nav>

</div>

<!-- side bar section ends -->

