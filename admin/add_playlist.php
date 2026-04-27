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
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);

    // الحصول على category_id من الاختيار
    $category_id = $_POST['category'];
    $category_id = filter_var($category_id, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id().'.'.$ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/'.$rename;

    // التحقق من وجود الفئة في جدول categories
    $check_category = $conn->prepare("SELECT id FROM `categories` WHERE id = ?");
    $check_category->execute([$category_id]);

    if($check_category->rowCount() > 0){

        // إدراج البيانات إذا كانت الفئة موجودة
        $add_playlist = $conn->prepare("INSERT INTO `playlist`(id, tutor_id, title, description, thumb, status, category_id) VALUES(?,?,?,?,?,?,?)");
        $add_playlist->execute([$id, $tutor_id, $title, $description, $rename, $status, $category_id]);

        move_uploaded_file($image_tmp_name, $image_folder);

        // رسالة نجاح
        $message[] = '<span data-ar="تم إنشاء قائمة تشغيل جديدة!" data-en="New playlist created!"></span>';
        
    } else {

        // رسالة خطأ في حال عدم وجود الفئة
        $message[] = '<span data-ar="الفئة غير موجودة!" data-en="Category does not exist!"></span>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="إضافة قائمة تشغيل" data-en="Add Playlist"></title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlist-form">

    <h1 class="heading" data-ar="إنشاء قائمة تشغيل" data-en="Create Playlist"></h1>

    <form action="" method="post" enctype="multipart/form-data">
        <p data-ar="حالة قائمة التشغيل" data-en="Playlist Status"> <span>*</span></p>
        <select name="status" class="box" required>
            <option value="" selected disabled data-ar="-- اختيار الحالة" data-en="-- Select Status"></option>
            <option value="active" data-ar="نشطة" data-en="Active"></option>
            <option value="deactive" data-ar="غير نشطة" data-en="Deactive"></option>
        </select>

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

        <p data-ar="اسم قائمة التشغيل" data-en="Playlist Title"> <span>*</span></p>
        <input type="text" name="title" maxlength="100" required data-ar="ادخل اسم قائمة التشغيل" data-en="Enter Playlist Title" class="box">
        
        <p data-ar="وصف قائمة التشغيل" data-en="Playlist Description"> <span>*</span></p>
        <textarea name="description" class="box" required data-ar="اكتب الوصف" data-en="Write Description" maxlength="1000" cols="30" rows="10"></textarea>
        
        <p data-ar="الصورة الرمزية لقائمة التشغيل" data-en="Playlist Thumbnail"> <span>*</span></p>
        <input type="file" name="image" accept="image/*" required class="box">
        
        <input type="submit" value="create playlist" name="submit" data-ar="إنشاء" data-en="Create Playlist" class="btn">
    </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>
