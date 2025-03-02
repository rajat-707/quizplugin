<?php
/*
Plugin Name: Konenki Quiz
Description: A customizable quiz plugin for health and wellness recommendations.
Version: 1.0
Author: Your Name
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
function konenki_quiz_menu() {
    add_menu_page(
        'Konenki Quiz', // Page title
        'Konenki Quiz', // Menu title
        'manage_options', // Capability
        'konenki-quiz', // Menu slug
        'konenki_quiz_admin_page', // Function
        'dashicons-clipboard', // Icon
        6 // Position
    );
}
add_action('admin_menu', 'konenki_quiz_menu');

// Admin page content
function konenki_quiz_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save data if form is submitted
    if (isset($_POST['konenki_quiz_nonce'])) {
        if (wp_verify_nonce($_POST['konenki_quiz_nonce'], 'konenki_quiz_save')) {
            // Sanitize and save questions and recommendations
            $questions = sanitize_textarea_field($_POST['questions']);
            $recommendations = sanitize_textarea_field($_POST['recommendations']);
            update_option('konenki_quiz_questions', $questions);
            update_option('konenki_quiz_recommendations', $recommendations);
            echo '<div class="updated"><p>Settings saved!</p></div>';
        }
    }

    // Retrieve saved data
    $questions = get_option('konenki_quiz_questions', '');
    $recommendations = get_option('konenki_quiz_recommendations', '');

    ?>
    <div class="wrap">
        <h1>Konenki Quiz Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('konenki_quiz_save', 'konenki_quiz_nonce'); ?>

            <h2>Quiz Questions</h2>
            <textarea name="questions" rows="10" cols="50" style="width: 100%;"><?php echo esc_textarea($questions); ?></textarea>

            <h2>Recommendations</h2>
            <textarea name="recommendations" rows="10" cols="50" style="width: 100%;"><?php echo esc_textarea($recommendations); ?></textarea>

            <p><input type="submit" class="button-primary" value="Save Changes"></p>
        </form>
    </div>
    <?php
}

// Shortcode to display the quiz on the frontend
function konenki_quiz_shortcode() {
    $questions = get_option('konenki_quiz_questions', '');
    $recommendations = get_option('konenki_quiz_recommendations', '');

    ob_start();
    ?>
    <div id="konenki-quiz">
        <h2>Health and Wellness Quiz</h2>
        <form id="konenki-quiz-form">
            <?php echo wpautop($questions); ?>
            <p><input type="submit" value="Submit"></p>
        </form>
        <div id="konenki-quiz-results" style="display:none;">
            <h2>Recommendations</h2>
            <p><?php echo wpautop($recommendations); ?></p>
        </div>
    </div>
    <script>
        document.getElementById('konenki-quiz-form').onsubmit = function(e) {
            e.preventDefault();
            document.getElementById('konenki-quiz-results').style.display = 'block';
        };
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('konenki_quiz', 'konenki_quiz_shortcode');

// Enqueue styles for the frontend quiz
function konenki_quiz_styles() {
    wp_enqueue_style('konenki-quiz-style', plugins_url('style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'konenki_quiz_styles');