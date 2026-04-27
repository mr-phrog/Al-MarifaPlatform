<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    $user_id = '';
    header('location:login.php');
    exit(); // Ensure script stops executing after redirection
}

$books = [];
$query = $conn->prepare("SELECT * FROM `books`");
$query->execute();
if($query->rowCount() > 0){
    $books = $query->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title data-ar="الكتب" data-en="Books"></title>

<!-- font awesome cdn link  -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

<!-- custom css file link  -->
<link rel="stylesheet" href="css/all.min.css">
<link id="style" rel="stylesheet" href="css/style-en.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>


<!-- Top categories section (banner) starts -->
<section class="quick-select">
   <h1 class="heading" data-en="top categories" data-ar="أهم الفئات">أهم الفئات</h1>
   <div class="box-container">
      <div class="box">
         <div class="flex">
            <a href="books.php" class="category-link" data-category="all"><i class="fas fa-th"></i><span data-en="all" data-ar="الكل">الكل</span></a>
            <a href="books.php?category_id=1" class="category-link" data-category="1"><i class="fas fa-code"></i><span data-en="development" data-ar="التطوير"></span></a>
            <a href="books.php?category_id=2" class="category-link" data-category="2"><i class="fas fa-users-gear"></i><span data-en="Human Development" data-ar="تنمية بشرية"></span></a>
            <a href="books.php?category_id=3" class="category-link" data-category="3"><i class="fas fa-pen"></i><span data-en="design" data-ar="التصميم"></span></a>
            <a href="books.php?category_id=4" class="category-link" data-category="4"><i class="fas fa-chart-simple"></i><span data-en="business" data-ar="الأعمال"></span></a>
            <a href="books.php?category_id=5" class="category-link" data-category="5"><i class="fas fa-chart-line"></i><span data-en="marketing" data-ar="التسويق"></span></a>
            <a href="books.php?category_id=6" class="category-link" data-category="6"><i class="fas fa-language"></i><span data-en="Languages" data-ar="لغات"></span></a>
            <a href="books.php?category_id=7" class="category-link" data-category="7"><i class="fas fa-cog"></i><span data-en="software" data-ar="البرمجيات"></span></a>
            <a href="books.php?category_id=8" class="category-link" data-category="8"><i class="fas fa-vial"></i><span data-en="science" data-ar="العلوم"></span></a>
         </div>
      </div>
   </div>
</section>
<!-- Top categories section (banner) ends -->


<!-- books section starts  -->

<section class="books">

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
         echo '<span data-en="Books in ' . htmlspecialchars($category_name['en']) . '" data-ar="كتب في ' . htmlspecialchars($category_name['ar']) . '">دورات في ' . htmlspecialchars($category_name['ar']) . '</span>';
      } else {
         echo '<span data-en="latest bo" data-ar="أحدث الكتب">أحدث الكتب</span>';
      }
      ?>
   </h1>

<form action="search_book.php" method="post" class="search-book">
    <input type="text" name="search_book" maxlength="100" data-ar="ابحث عن كتاب ..." data-en="search book..." required>
    <button type="submit" name="search_book_btn" class="fas fa-search"></button>
</form>
<div class="box-container-book">

    <!-- Fetch books based on a condition, if needed -->

    <?php
    // Example: Fetch books with certain conditions
    if (isset($category_id)) {
        $select_books = $conn->prepare("SELECT * FROM `books` WHERE category_id = ? ");
        $select_books->execute([$category_id]);
    } else {
        $select_books = $conn->prepare("SELECT * FROM `books`");
        $select_books->execute();
    }

    // Check if there are books available
    if ($select_books->rowCount() > 0) {
        while ($book = $select_books->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box-book">
                <img src="uploaded_files/<?= htmlspecialchars($book['book_image']); ?>" class="book-image" alt="<?= htmlspecialchars($book['title']); ?>">
                <h3 class="title-book"><?= htmlspecialchars($book['title']); ?></h3>
                <p class="description-book"><?= htmlspecialchars($book['description']); ?></p>
                <div class="author-container">
                    <p class="author-book" data-ar="الكاتب" data-en="Author"> <span><?= htmlspecialchars($book['author']); ?></span></p>
                    <p class="page-number-book" data-ar="الصفحات" data-en="Pages"> <span><?= htmlspecialchars($book['num_pages']); ?></span></p>
                </div>
                <div class="btn-container-book">
                    <a href="uploaded_files/<?= htmlspecialchars($book['book_file']); ?>" target="_blank" class="inline-btn" data-ar="قراءة" data-en="Read"></a>
                    <a href="uploaded_files/<?= htmlspecialchars($book['book_file']); ?>" download="<?= htmlspecialchars($book['title']); ?>.pdf" class="inline-option-btn" data-ar="تحميل" data-en="Download"></a>
                </div>
            </div>
            <?php
        }
    } else {
        // Display a message if no books are available
        echo '<p class="empty" data-ar="لم يتم إضافة إي كتاب" data-en="No books added yet!"></p>';
    }
    ?>

</div>


</section>

<!-- books section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>