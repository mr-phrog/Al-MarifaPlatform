<?php
include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:dashboard.php');
}

if (isset($_POST['update'])) {

    $book_file_id = $_POST['book_file_id'];
    $book_file_id = filter_var($book_file_id, FILTER_SANITIZE_STRING);
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $author = $_POST['author'];
    $author = filter_var($author, FILTER_SANITIZE_STRING);
    $num_pages = $_POST['num_pages'];
    $num_pages = filter_var($num_pages, FILTER_SANITIZE_NUMBER_INT);
    
    // Capture the new category value
    $category_id = $_POST['category'];
    $category_id = filter_var($category_id, FILTER_SANITIZE_STRING);

    // Update the books table with the new category
    $update_books = $conn->prepare("UPDATE `books` SET title = ?, description = ?, author = ?, num_pages = ?, category_id = ? WHERE id = ?");
    $update_books->execute([$title, $description, $author, $num_pages, $category_id, $book_file_id]);

    $old_book_image = $_POST['old_book_image'];
    $old_book_image = filter_var($old_book_image, FILTER_SANITIZE_STRING);
    $book_image = $_FILES['book_image']['name'];
    $book_image = filter_var($book_image, FILTER_SANITIZE_STRING);
    $book_image_ext = pathinfo($book_image, PATHINFO_EXTENSION);
    $rename_book_image = unique_id() . '.' . $book_image_ext;
    $book_image_size = $_FILES['book_image']['size'];
    $book_image_tmp_name = $_FILES['book_image']['tmp_name'];
    $book_image_folder = '../uploaded_files/' . $rename_book_image;

    if (!empty($book_image)) {
        if ($book_image_size > 2000000) {
            $message[] = '<span data-ar="حجم الصورة كبير جدًا" data-en="Image size is too large!"></span>';
        } else {
            $update_book_image = $conn->prepare("UPDATE `books` SET book_image = ? WHERE id = ?");
            $update_book_image->execute([$rename_book_image, $book_file_id]);
            move_uploaded_file($book_image_tmp_name, $book_image_folder);
            if ($old_book_image != '' && $old_book_image != $rename_book_image) {
                unlink('../uploaded_files/' . $old_book_image);
            }
        }
    }

    $old_book_file = $_POST['old_book_file'];
    $old_book_file = filter_var($old_book_file, FILTER_SANITIZE_STRING);
    $book_file = $_FILES['book_file']['name'];
    $book_file = filter_var($book_file, FILTER_SANITIZE_STRING);
    $book_file_ext = pathinfo($book_file, PATHINFO_EXTENSION);
    $rename_book_file = unique_id() . '.' . $book_file_ext;
    $book_file_tmp_name = $_FILES['book_file']['tmp_name'];
    $book_file_folder = '../uploaded_files/' . $rename_book_file;

    if (!empty($book_file)) {
        $update_book_file = $conn->prepare("UPDATE `books` SET book_file = ? WHERE id = ?");
        $update_book_file->execute([$rename_book_file, $book_file_id]);
        move_uploaded_file($book_file_tmp_name, $book_file_folder);
        if ($old_book_file != '' && $old_book_file != $rename_book_file) {
            unlink('../uploaded_files/' . $old_book_file);
        }
    }

    $message[] = '<span data-ar="تم تحديث الكتاب!" data-en="Book updated!"></span>';
}

if (isset($_POST['delete_book_file_id'])) {

    $delete_id = $_POST['book_file_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $delete_book_file_book_image = $conn->prepare("SELECT book_image FROM `books` WHERE id = ? LIMIT 1");
    $delete_book_file_book_image->execute([$delete_id]);
    $fetch_book_image = $delete_book_file_book_image->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_files/' . $fetch_book_image['book_image']);

    $delete_book_file = $conn->prepare("SELECT book_file FROM `books` WHERE id = ? LIMIT 1");
    $delete_book_file->execute([$delete_id]);
    $fetch_book_file = $delete_book_file->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_files/' . $fetch_book_file['book_file']);

    $delete_books = $conn->prepare("DELETE FROM `books` WHERE id = ?");
    $delete_books->execute([$delete_id]);
    header('location:books.php');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="تحديث الكتاب" data-en="Update book"></title>

    <!-- font awesome cdn link  -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/all.min.css">
    <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>

<body>

<?php include '../components/admin_header.php'; ?>

    <section class="book-form">
        <h1 class="heading" data-ar="تحديث الكتاب" data-en="update book"></h1>
        <?php
        $select_books = $conn->prepare("SELECT * FROM `books` WHERE id = ? AND tutor_id = ?");
        $select_books->execute([$get_id, $tutor_id]);
        if($select_books->rowCount() > 0){
            while($fecth_books = $select_books->fetch(PDO::FETCH_ASSOC)){ 
                $book_file_id = $fecth_books['id'];
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="book_file_id" value="<?= $fecth_books['id']; ?>">
            <input type="hidden" name="old_book_image" value="<?= $fecth_books['book_image']; ?>">
            <input type="hidden" name="old_book_file" value="<?= $fecth_books['book_file']; ?>">
            <p data-ar="تحديث العنوان" data-en="update title"><span>*</span></p>
            <input type="text" name="title" maxlength="100" required data-ar="ادخل عنوان الكتاب" data-en="enter book title" class="box" value="<?= $fecth_books['title']; ?>">
            <div class="img-wrapper">
                <img src="../uploaded_files/<?= $fecth_books['book_image']; ?>" alt="">
            </div>
            <p data-ar="تحديث غلاف الكتاب" data-en="update book's cover"></p>
            <input type="file" name="book_image" accept="image/*" class="box">
            <p data-ar="تحديث الوصف" data-en="update description"><span>*</span></p>
            <textarea name="description" class="box" required data-ar="اكتب الوصف" data-en="write description" maxlength="1000" cols="30" rows="10"><?= $fecth_books['description']; ?></textarea>
            <p data-ar="التصنيف" data-en="Category"> <span>*</span></p>
            <select name="category" class="box" required>
                <?php
                    // Fetch the categories from the categories table
                    $select_categories = $conn->prepare("SELECT * FROM `categories`");
                    $select_categories->execute();
                    
                    // Loop through the categories and set the selected attribute if it matches the current category of the book
                    while ($row = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($row['id'] == $fecth_books['category_id']) ? 'selected' : '';
                        echo '<option value="'.$row['id'].'" '.$selected.' data-ar="'.$row['name_ar'].'" data-en="'.$row['name_en'].'">'.$row['name'].'</option>';
                    }
                ?>
            </select>

            <p data-ar="اسم الكاتب" data-en="author name"> <span>*</span></p>
            <input type="text" name="author" maxlength="100" required data-ar="ادخل اسم الكاتب" data-en="enter author name" class="box" value="<?= $fecth_books['author']; ?>">
            <p data-ar="عدد صفحات الكتاب" data-en="number of pages"> <span>*</span></p>
            <input type="number" name="num_pages" id="" data-ar="ادخل عدد صفحات الكتاب" data-en="enter the number of book pages" class="box" value="<?= $fecth_books['num_pages']; ?>">
            
            <p data-ar="تحديث الكتاب" data-en="update book"></p>
            <input type="file" name="book_file" accept=".pdf" class="box" value="<?= $fecth_books['book_file']; ?>">
            <input type="submit" value="update_books" name="update" data-ar="تحديث" data-en="Update" class="btn">
            <div class="flex-btn">
            <?php
                if (file_exists('../uploaded_files/' . $fecth_books['book_file'])) {
                echo '<a href="../uploaded_files/' . htmlspecialchars($fecth_books['book_file']) . '" target="_blank" class="option-btn" data-ar="قراءة" data-en="Read"></a>';
                } else {
                echo '<p>File not found: ' . htmlspecialchars($fecth_books['book_file']) . '</p>';
                }
            ?>
                <input type="submit" value="delete_books" name="delete_book_file_id" data-ar="حذف" data-en="delete" onclick="return confirmAction (event,'delete_book');" class="delete-btn">
            </div>
        </form>    
        <?php
            }
        }else{
            echo '<p class="empty" data-ar="لكتاب غير موجود!" data-en="book not found!"> <a href="add_book.php" class="btn" style="margin-top: 1.5rem;" data-ar="إضافة كتاب" data-en="add book"> </a></p>';
        }
    ?>
    </section>






    <?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>