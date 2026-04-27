<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
   exit(); // Ensure script stops executing after redirection
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title data-ar="مساعدك الذكي" data-en="Smart Assistant"></title>
        <!-- Linking Google Fonts for Icons -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
        <!-- font awesome cdn link  -->
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->
        <script src="lib/marked.min.js"></script>
        <script src="lib/purify.min.js"></script>
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/all.min.css">
    <link id="style" rel="stylesheet" href="css/style-en.css">

    </head>
    <body class="g-body">
    <?php include 'components/user_header.php'; ?>

        <header class="g-header">
            <!-- Header Greetings -->
            <h2 class="g-title" data-ar="أنا مساعدك الذكي" data-en=" I'm your Smart Assistant"></h2>
            <h4 class="g-subtitle" data-ar="كيف يمكنني مساعدتك اليوم؟" data-en="How I can help you today?" ></h4>
            <!-- Suggestion List -->
            <!-- <ul class="suggestion-list">
                <li class="suggestion">
                    <h4 class="text">Help me plan a game night with my 5 best friends
                        for under $100.</h4>
                    <span class="icon material-symbols-rounded">draw</span>
                </li>
                <li class="suggestion">
                    <h4 class="text">Help me plan a game night with my 5 best friends
                        for under $100.</h4>
                    <span class="icon material-symbols-rounded">lightbulb</span>
                </li>
                <li class="suggestion">
                    <h4 class="text">Help me plan a game night with my 5 best friends
                        for under $100.</h4>
                    <span class="icon material-symbols-rounded">explore</span>
                </li>
                <li class="suggestion">
                    <h4 class="text">Help me plan a game night with my 5 best friends
                        for under $100.</h4>
                    <span class="icon material-symbols-rounded">code</span>
                </li>
            </ul> -->
        </header>

        <!-- Chat List/ Container -->
        <div class="chat-list"></div>

        
        <div class="typing-area">
            <form action="#" class="typing-form">
                <div class="input-wrapper">
                    <input type="text" data-ar="أدخل طلبك هنا" data-en="Enter you prompt here" class="typing-input" required>
                    <button class="icon material-symbols-rounded">send</button>
                </div>
                <div class="action-buttons">
                    <!-- <span class="icon material-symbols-rounded">light_mode</span> -->
                    <span id="delete-chat-button" class="icon material-symbols-rounded">delete</span>
                </div>
            </form>
            <p class="disclaimer-text" data-ar="قد يعرض المساعد الذكي معلومات غير دقيقة، بما في ذلك معلومات عن الأشخاص، لذا تحقق جيدًا من إجاباته." data-en="Smart Assistant may display inaccurate info, including about people, so
                double-check its responses."></p>
        </div>


<!-- Pass user avatar to JS in order to display it with Smart Assistant chat  -->
        <?php
        // Assuming $fetch_profile['image'] contains the path to the user's image
        $imageUrl = 'uploaded_files/' . $fetch_profile['image'];
        ?>
        <script>
        // Pass the PHP variable to JavaScript
        const userImageUrl = "<?php echo $imageUrl; ?>";
        </script>

        <!-- custom js file link  -->
        <script src="js/script.js"></script>
        <script src="js/switcher.js"></script>
    </body>
</html>