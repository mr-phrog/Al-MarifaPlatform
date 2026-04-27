<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:home.php');
    exit();
}

// Fetch the playlist details
$select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? AND status = ? LIMIT 1");
$select_playlist->execute([$get_id, 'active']);
if ($select_playlist->rowCount() > 0) {
    $fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);

    $playlist_id = $fetch_playlist['id'];

    // Fetch category details based on category_id
    $select_category = $conn->prepare("SELECT name_ar, name_en FROM `categories` WHERE id = ? LIMIT 1");
    $select_category->execute([$fetch_playlist['category_id']]);
    $fetch_category = $select_category->fetch(PDO::FETCH_ASSOC);

    // Fetch the tutor details
    $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
    $select_tutor->execute([$fetch_playlist['tutor_id']]);
    $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

    // Fetch bookmark status
    $select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
    $select_bookmark->execute([$user_id, $playlist_id]);

    // Fetch total number of videos in the playlist
    $select_total_videos = $conn->prepare("SELECT COUNT(*) FROM `content` WHERE playlist_id = ? AND status = ?");
    $select_total_videos->execute([$playlist_id, 'active']);
    $total_videos = $select_total_videos->fetchColumn(); // Fetches the count as a single value

} else {
    echo '<p class="empty" data-en="This playlist was not found!" data-ar="لم يتم العثور على قائمة التشغيل هذه!">لم يتم العثور على قائمة التشغيل هذه!</p>';
    exit(); 
}

// Function to map categories to icons
function getCategoryIcon($category) {
    $icons = [
        'development' => 'fas fa-code',
        'photography' => 'fas fa-camera',
        'design' => 'fas fa-pen',
        'business' => 'fas fa-chart-simple',
        'marketing' => 'fas fa-chart-line',
        'music' => 'fas fa-music',
        'software' => 'fas fa-cog',
        'science' => 'fas fa-vial',
    ];
    return $icons[strtolower($category)] ?? 'fas fa-question'; // Default icon if category not found
}

if (isset($_POST['save_list'])) {
    if ($user_id != '') {
        $list_id = filter_var($_POST['list_id'], FILTER_SANITIZE_STRING);

        $select_list = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
        $select_list->execute([$user_id, $list_id]);

        if ($select_list->rowCount() > 0) {
            $remove_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
            $remove_bookmark->execute([$user_id, $list_id]);
            $message[] = '<span data-ar="تمت إزالة قائمة التشغيل من الإشارات المرجعية الخاصة بك!" data-en="Playlist removed from your bookmarks!"></span>';
        } else {
            $insert_bookmark = $conn->prepare("INSERT INTO `bookmark`(user_id, playlist_id) VALUES(?,?)");
            $insert_bookmark->execute([$user_id, $list_id]);
            $message[] = '<span data-ar="تم حفظ قائمة التشغيل في الإشارات المرجعية الخاصة بك!" data-en="Playlist is saved in your bookmarks!"></span>';
        }
    } else {
        $message[] = '<span data-ar="الرجاء تسجيل الدخول أولا!" data-en="Please login first!"></span>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="قائمة التشغيل" data-en="Playlist">قائمة التشغيل</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link -->
    <link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- playlist section starts -->
<section class="playlist">
    <h1 class="heading" data-en="Playlist Details" data-ar="تفاصيل قائمة التشغيل">تفاصيل قائمة التشغيل</h1>

    <div class="row">
        <div class="col">
            <form action="" method="post" class="save-list">
                <input type="hidden" name="list_id" value="<?= $playlist_id; ?>">
                <?php if ($select_bookmark->rowCount() > 0) { ?>
                    <button type="submit" name="save_list"><i class="far fa-bookmark"></i><span data-en="Save Playlist" data-ar="حفظ قائمة التشغيل">حفظ قائمة التشغيل</span></button>
                <?php } else { ?>
                    <button type="submit" name="save_list"><i class="fas fa-bookmark"></i><span data-ar="محفوظة" data-en="Saved"></span></button>
                <?php } ?>
            </form>
            <div class="thumb">
                <span data-ar="<?= $total_videos; ?> فيديوهات" data-en="<?= $total_videos; ?> Videos"><?= $total_videos; ?> Videos</span>
                <img src="uploaded_files/<?= htmlspecialchars($fetch_playlist['thumb']); ?>" alt="">
            </div>
        </div>

        <div class="col">
            <div class="tutor">
                <img src="uploaded_files/<?= htmlspecialchars($fetch_tutor['image']); ?>" alt="">
                <div>
                    <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
                    <span data-ar="<?= htmlspecialchars($fetch_tutor['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_tutor['profession']); ?>"><?= htmlspecialchars($fetch_tutor['profession_ar']); ?></span>
                </div>
            </div>

            <!-- Category Details -->
            <div class="details">
                <a href="courses.php?category_id=<?= htmlspecialchars($fetch_playlist['category_id']); ?>" class="category-link" data-category="<?= htmlspecialchars($fetch_playlist['category_id']); ?>">
                    <!-- Using dynamic icon class from category -->
                    <i class="<?= getCategoryIcon($fetch_category['name_en']); ?>"></i>
                    <!-- Dynamic text for both English and Arabic, default to Arabic -->
                    <span data-en="<?= htmlspecialchars($fetch_category['name_en']); ?>" data-ar="<?= htmlspecialchars($fetch_category['name_ar']); ?>"><?= htmlspecialchars($fetch_category['name_ar']); ?></span>
                </a>

                <!-- Playlist title and description -->
                <h3><?= htmlspecialchars($fetch_playlist['title']); ?></h3>
                <p><?= htmlspecialchars($fetch_playlist['description']); ?></p>

                <!-- Playlist date -->
                <div class="date">
                    <i class="fas fa-calendar"></i>
                    <span><?= htmlspecialchars($fetch_playlist['date']); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- playlist section ends -->

<!-- videos container section starts -->
<section class="videos-container">
    <h1 class="heading" data-en="Playlist Videos" data-ar="فيديوهات قائمة التشغيل"></h1>

    <div class="box-container">
        <?php
        $select_content = $conn->prepare("SELECT * FROM `content` WHERE playlist_id = ? AND status = ? ORDER BY date DESC");
        $select_content->execute([$get_id, 'active']);
        if ($select_content->rowCount() > 0) {
            while ($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <a href="watch_video.php?get_id=<?= $fetch_content['id']; ?>" class="box">
            <i class="fas fa-play"></i>
            <img src="uploaded_files/<?= htmlspecialchars($fetch_content['thumb']); ?>" alt="">
            <h3><?= htmlspecialchars($fetch_content['title']); ?></h3>
        </a>
        <?php
            }
        } else {
            echo '<p class="empty" data-en="No videos added yet!" data-ar="لم يتم إضافة فيديوهات بعد!">لم يتم إضافة فيديوهات بعد!</p>';
        }
        ?>
    </div>
</section>
<!-- videos container section ends -->

<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>
