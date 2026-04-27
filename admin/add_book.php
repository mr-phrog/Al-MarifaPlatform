<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
    $tutor_id = $_COOKIE['tutor_id'];
}else{
    $tutor_id = '';
    header('location:login.php');
    exit();
}

if(isset($_POST['submit'])){

    $id = unique_id();

    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);

    // ** الحصول على category_id من الاختيار **
    $category_id = $_POST['category'];
    $category_id = filter_var($category_id, FILTER_SANITIZE_STRING);

    // تحقق من أن الفئة موجودة في جدول categories
    $check_category = $conn->prepare("SELECT id FROM `categories` WHERE id = ?");
    $check_category->execute([$category_id]);

    $book_image = $_FILES['book_image']['name'];
    $book_image = filter_var($book_image, FILTER_SANITIZE_STRING);
    $book_image_ext = pathinfo($book_image, PATHINFO_EXTENSION);
    $rename_book_image = unique_id().'.'.$book_image_ext;
    $book_image_size = $_FILES['book_image']['size'];
    $book_image_tmp_name = $_FILES['book_image']['tmp_name'];
    $book_image_folder = '../uploaded_files/'.$rename_book_image;

    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    $author = $_POST['author'];
    $author = filter_var($author, FILTER_SANITIZE_STRING);
    
    $num_pages = $_POST['num_pages'];
    $num_pages = filter_var($num_pages, FILTER_SANITIZE_NUMBER_INT);

    $book_file = $_FILES['book_file']['name'];
    $book_file = filter_var($book_file, FILTER_SANITIZE_STRING);
    $book_file_ext = pathinfo($book_file, PATHINFO_EXTENSION);
    $rename_book_file = unique_id().'.'.$book_file_ext;
    $book_file_tmp_name = $_FILES['book_file']['tmp_name'];
    $book_file_folder = '../uploaded_files/'.$rename_book_file;

    
    // Validate file extensions
    $allowed_image_exts = ['jpg', 'jpeg', 'png', 'gif'];
    $allowed_file_exts = ['pdf'];
    
    if(!in_array($book_image_ext, $allowed_image_exts)){
        $message[] = '<span data-ar="نوع ملف الصورة غير صالح!" data-en="Invalid image file type!"></span>';
    } elseif(!in_array($book_file_ext, $allowed_file_exts)){
        $message[] = '<span data-ar="نوع ملف الكتاب غير صالح!" data-en="Invalid book file type!"></span>';
    } elseif($book_image_size > 2000000){
        $message[] = '<span data-ar="حجم الصورة كبير جدًا" data-en="Image size is too large!"></span>';
    } else {
        // ** Highlight: إضافة category_id إلى جملة SQL **
        $add_book = $conn->prepare("INSERT INTO `books`(id, tutor_id, title, book_image, description, author, num_pages, book_file ,category_id) VALUES(?,?,?,?,?,?,?,?,?)");
        $add_book->execute([$id, $tutor_id, $title, $rename_book_image, $description, $author, $num_pages, $rename_book_file, $category_id]);
        move_uploaded_file($book_image_tmp_name, $book_image_folder);
        move_uploaded_file($book_file_tmp_name, $book_file_folder);
        $message[] = '<span data-ar="تم رفع كتاب جديد!" data-en="New book uploaded!"></span>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="إضافة كتاب" data-en="Add book"></title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>

<body>

<?php include '../components/admin_header.php'; ?>

<section class="book-form">
    <h1 class="heading" data-ar="إنشاء كتاب" data-en="upload book"></h1>

    <form action="" method="post" enctype="multipart/form-data">
        <p data-ar="اسم الكتاب" data-en="book name"> <span>*</span></p>
        <input type="text" name="title" maxlength="100" required data-ar="ادخل اسم الكتاب" data-en="enter book name" class="box">
        <p data-ar="اختيار غلاف الكتاب" data-en="select book's cover"> <span>*</span></p>
        <input type="file" name="book_image" accept="image/*" required class="box">
        <p data-ar="وصف الكتاب" data-en="book description"> <span>*</span></p>
        <textarea name="description" class="box" required data-ar="اكتب الوصف" data-en="write a description" maxlength="1000" cols="30" rows="10"></textarea>
                        <!-- اختيار التصنيف -->
        <p data-ar="التصنيف" data-en="Category"> <span>*</span></p>
        <select name="category" class="box" required>
            <option value="" selected disabled data-ar="-- اختيار التصنيف" data-en="-- Select Category"></option>
            <?php
                // جلب الفئات من جدول categories
                $select_categories = $conn->prepare("SELECT * FROM `categories`");
                $select_categories->execute();
                while($row = $select_categories->fetch(PDO::FETCH_ASSOC)){
                    echo '<option value="'.$row['id'].'" data-ar="'.$row['name_ar'].'" data-en="'.$row['name_en'].'">'.$row['name'].'</option>';
                }
            ?>
        </select>
        <p data-ar="اسم الكاتب" data-en="author name"> <span>*</span></p>
        <input type="text" name="author" maxlength="100" required data-ar="ادخل اسم الكاتب" data-en="enter author name" class="box">
        <p data-ar="عدد صفحات الكتاب" data-en="number of pages"> <span>*</span></p>
        <input type="number" name="num_pages" id="" data-ar="ادخل عدد صفحات الكتاب" data-en="enter the number of book pages" class="box">
        <p data-ar="اختيار الكتاب" data-en="select book"> <span>*</span></p>
        <input type="file" name="book_file" accept=".pdf" required class="box">
        <input type="submit" value="upload book" name="submit" data-ar="رفع الكتاب" data-en="upload book" class="btn">

    </form>
</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>
