<!-- overview.php -->
<?php
include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}

// Fetch data for the admin (tutor)
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
$select_contents->execute([$tutor_id]);
$total_contents = $select_contents->rowCount();

$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
$select_playlists->execute([$tutor_id]);
$total_playlists = $select_playlists->rowCount();

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
$select_likes->execute([$tutor_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
$select_comments->execute([$tutor_id]);
$total_comments = $select_comments->rowCount();

$select_books = $conn->prepare("SELECT * FROM `books` WHERE tutor_id = ?");
$select_books->execute([$tutor_id]);
$total_books = $select_books->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        @media (max-width: 768px) {
            .overview {
                padding: 15px; /* Adjust padding for mobile devices */
            }
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="نظرة عامة" data-en="Overview"></title>
    <!-- custom css file link -->
    <link rel="stylesheet" href="../css/all.min.css">
    <link id="style" rel="stylesheet" href="../css/admin_style-ar.css">
</head>
<body>
   <?php include '../components/admin_header.php'; ?>
    <section>
        <h1 class="heading" data-ar="نظرة عامة" data-en="Overview"></h1>
    </section>
    <div class="overview">
        <!-- Dropdown for selecting chart type -->
        <label for="chartType" data-ar="اختر نوع الرسم البياني" data-en="Select Chart Type" style="font-size: 1.4rem;"></label>
        <select class="select-chart" id="chartType" onchange="drawChart()">
            <option value="pie" selected data-ar="مخطط دائري" data-en="Pie Chart">Pie</option>
            <option value="doughnut" data-ar="مخطط دونات" data-en="Doughnut Chart">Doughnut</option>
        </select>

        <!-- Canvas for the pie chart -->
        <canvas id="chart_div"></canvas>
    </div>

    <?php include '../components/footer.php'; ?>

    <!-- Load Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Function to get text based on the current language
        function getLocalizedText(arText, enText) {
            const lang = document.documentElement.lang || 'en';
            return lang === 'ar' ? arText : enText;
        }

        Chart.defaults.font.size = 18;
        Chart.defaults.font.family = "'Tajawal','Poppins', sans-serif";
        // Fetch the value of the CSS custom property
        const rootStyles = getComputedStyle(document.documentElement);
        const mainColor = rootStyles.getPropertyValue('--charts-txt').trim();

        // Apply the custom color to Chart.js defaults
        Chart.defaults.color = mainColor;

        // Function to create and draw the chart
        function drawChart() {
            const lang = document.documentElement.lang || 'en';
            const chartDirection = lang === 'ar' ? 'rtl' : 'ltr';
            const isRtl = lang === 'ar';  // Determine if RTL should be true or false
            const chartTitle = getLocalizedText('نظرة عامة على الأنشطة', 'Overview of Activities');

            // Get selected chart type from the dropdown
            const chartType = document.getElementById('chartType').value;

            function getFontSettings() {
                if (window.innerWidth <= 768) { // For small screens (like mobile phones)
                    return { size: 12, weight: 'bold' }; // Smaller font size and normal weight
                } else { // For larger screens
                    return { size: 20, weight: 'bold' }; // Larger font size and bold weight
                }
            }

            // Set up the pie chart data with correct labels
            const data = {
                labels: [
                    getLocalizedText('المحتويات', 'Contents'),
                    getLocalizedText('قوائم التشغيل', 'Playlists'),
                    getLocalizedText('الكتب', 'Books'),
                    getLocalizedText('الإعجابات', 'Likes'),
                    getLocalizedText('التعليقات', 'Comments')
                ],
                datasets: [{
                    data: [<?= $total_contents; ?>, <?= $total_playlists; ?>, <?= $total_books; ?>, <?= $total_likes; ?>, <?= $total_comments; ?>],
                    backgroundColor: ['#4285f4', '#d96570', '#f4b400', '#0f9d58', '#ab47bc'],
                    hoverBackgroundColor: ['#3367d6', '#c93945', '#f3a300', '#0c7d45', '#9c27b0']
                }]
            };

            // Destroy the chart instance if it already exists
            if (window.myPieChart) {
                window.myPieChart.destroy();
            }

            // Create the pie chart using Chart.js
            const ctx = document.getElementById('chart_div').getContext('2d');
            window.myPieChart = new Chart(ctx, {
                type: chartType, // Use the selected chart type
                data: data,
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            rtl: isRtl,
                            labels: {
                                font: getFontSettings()
                            }
                        },
                        title: {
                            display: true,
                            text: chartTitle
                        }
                    },
                    layout: {
                        padding: 10
                    },
                    direction: chartDirection
                }
            });
        }

        // Function to update the chart language
        function updateChartLanguage() {
            drawChart();
        }

        // Draw the chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            drawChart();
        });

        // Listen for language changes and redraw the chart when the language is switched
        document.addEventListener('languageSwitch', function() {
            updateChartLanguage();
        });

        // Observe changes to the html lang attribute
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === "attributes" && mutation.attributeName === "lang") {
                    updateChartLanguage();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['lang']
        });
    </script>

    <script src="../js/admin_script.js"></script>
    <script src="../js/switcher-admin.js"></script>
</body>
</html>
