<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
    $tutor_id = $_COOKIE['tutor_id'];
}else{
    $tutor_id = '';
    header('location:login.php');
}

$books = [];
$query = $conn->prepare("SELECT * FROM `books`");
$query->execute();
if($query->rowCount() > 0){
    $books = $query->fetchAll(PDO::FETCH_ASSOC);
}

/*the next PHP code is not tested yet! so it may contain bugs!, tested now--> it works! :) */
if(isset($_POST['delete_book_file'])){
    $delete_id = $_POST['book_file_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
    $verify_book_file = $conn->prepare("SELECT * FROM `books` WHERE id = ? LIMIT 1");
    $verify_book_file->execute([$delete_id]);
    if($verify_book_file->rowCount() > 0){
        $delete_book_file_book_image = $conn->prepare("SELECT * FROM `books` WHERE id = ? LIMIT 1");
        $delete_book_file_book_image->execute([$delete_id]);
        $fetch_book_image = $delete_book_file_book_image->fetch(PDO::FETCH_ASSOC);
        unlink('../uploaded_files/'.$fetch_book_image['book_image']);
        $delete_book_file = $conn->prepare("SELECT * FROM `books` WHERE id = ? LIMIT 1");
        $delete_book_file->execute([$delete_id]);
        $fetch_book_file = $delete_book_file->fetch(PDO::FETCH_ASSOC);
        unlink('../uploaded_files/'.$fetch_book_file['book_file']);
        $delete_books = $conn->prepare("DELETE FROM `books` WHERE id = ?");
        $delete_books->execute([$delete_id]);
        $message[] = '<span data-ar="تم حذف الكتاب!" data-en="book deleted!"></span>';

    }else{
        $message[] = '<span data-ar="الكتاب محذوف بالفعل!" data-en="book already deleted!"></span>';
    }

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
    <link rel="stylesheet" href="../css/all.min.css">
    <link id ="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>

<body>
<?php include '../components/admin_header.php'; ?>

    <section class="books">
        <h1 class="heading" data-ar="كتبك" data-en="your books"></h1>
        <div class="box-container">
            <div class="box" style="text-align: center;">
                <h3 class="title" style="margin-bottom: .5rem;" data-ar="إنشاء كتاب جديد" data-en="Create new book"></h3>
                <a href="add_book.php" class="btn" data-ar="إضافة كتاب" data-en="add book"></a>
            </div>
            
                
            <?php
                $select_book__file = $conn->prepare("SELECT * FROM `books` WHERE tutor_id = ? ORDER BY upload_date DESC");
                $select_book__file->execute([$tutor_id]);
                if($select_book__file->rowCount() > 0){
                while($fecth_book__file = $select_book__file->fetch(PDO::FETCH_ASSOC)){ 
                $book_file_id = $fecth_book__file['id'];
            ?>
            <div class="box">
                <div class="flex">
                    <div>
                        <i class="fas fa-calendar"></i>
                        <span>
                            <?php
                            $date = new DateTime($fecth_book__file['upload_date']);
                            echo $date->format('Y-m-d');
                            ?>
                        </span>
                    </div>
                </div>
                <img src="../uploaded_files/<?= $fecth_book__file['book_image']; ?>" class="book-image" alt="">
                <h3 class="title"><?= $fecth_book__file['title']; ?></h3>
                <p class="description-book"><?= htmlspecialchars($fecth_book__file['description']); ?></p>
                <div class="author-container">
                    <p class="author-book" data-ar="الكاتب" data-en="Author"> <span><?= htmlspecialchars($fecth_book__file['author']); ?></span></p>
                    <p class="page-number-book" data-ar="الصفحات" data-en="Pages"> <span><?= htmlspecialchars($fecth_book__file['num_pages']); ?></span></p>
                </div>
                <form action="" method="post" class="flex-btn">
                    <input type="hidden" name="book_file_id" value="<?= $book_file_id; ?>">
                    <a href="update_book.php?get_id=<?= $book_file_id; ?>" class="option-btn" data-ar="تحديث" data-en="update"></a>
                    <input type="submit" value="delete" class="delete-btn" name="delete_book_file" onclick="return confirmAction (event,'delete_book');" data-ar="حذف الكتاب" data-en="delete book">
                </form>
                <?php
                if (file_exists('../uploaded_files/' . $fecth_book__file['book_file'])) {
                    echo '<a href="../uploaded_files/' . htmlspecialchars($fecth_book__file['book_file']) . '" target="_blank" class="btn" data-ar="قراءة" data-en="Read"></a>';
                } else {
                    echo '<p>File not found: ' . htmlspecialchars($fecth_book__file['book_file']) . '</p>';
                }
                ?>                
                </div>
    <?php
            }
        }else{
            echo '<p class="empty" data-ar="لا توجد كتب مضافة حتى الآن!" data-en="no books added yet!"></p>';
        }
    ?>

    </div>

    </section>




    
    <?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>
<script src="../js/switcher-admin.js"></script>
</body>
</html>