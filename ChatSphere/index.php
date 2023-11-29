<?php
session_start();

include './config/connection.php';

if (isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['sender_id'] = uniqid();


    header('Location: index.php'); // Redirect to refresh the page
    exit();
}
$currentUserId = $_SESSION['sender_id'];
$currentUsername = $_SESSION['username'];
// echo $currentUserId = $_SESSION['sender_id'];

// ini_set('display_errors', 0); // Disable error display in production
// error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    $message = trim($_POST['message']);

    // Sanitize and validate the message
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    if (!empty($message)) {
        // Save the sanitized message to the database using prepared statement
        $query = 'INSERT INTO messages (sender_id, message_text, timestamp) VALUES (?, ?, CURRENT_TIMESTAMP)';
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $currentUserId, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

$query = 'SELECT * FROM messages ORDER BY timestamp ASC';
// Retrieve and display the chat messages using prepared statement
// $query = 'SELECT sender_id, message_text FROM messages ORDER BY timestamp ASC';
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Group Chat</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css"
        rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php if (!isset($_SESSION['username'])) { ?>
    <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card p-5">
                    <h3 class="text-center mb-4">Enter Your Unique Username</h3>
                    <form method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" placeholder="Your Username"
                                required>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Start Chat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php } else { ?>
    <div class="container">
        <h3 class="text-center">Group Chat Messaging</h3>
        <div class="messaging" id="message-box">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Recent</h4>
                        </div>
                        <div class="srch_bar">
                            <div class="stylish-input-group">
                                <input type="text" class="search-bar" placeholder="Search">
                                <span class="input-group-addon">
                                    <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Modify the inbox_chat section -->
                    <div class="inbox_chat" id="message-box">
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                            // echo "<pre>";
                            // print_r($row);
                            $senderId = $row['sender_id'];
                            $messageText = $row['message_text'];
                            $messageClass = ($senderId === $currentUserId) ? 'own-message' : 'other-message';

                            if ($senderId === $currentUserId) {
                                // Display the current user's message in the outgoing section
                                ?>
                        <div class="chat_list">
                            <div class="chat_people">
                                <div class="chat_img"> <img src="./images/download.png" alt="sunil"> </div>
                                <div class="chat_ib">
                                    <!-- <h5>Sunil Rajput <span class="chat_date">Dec 25</span></h5> -->
                                    <p>
                                        <?php echo $messageText; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
                            } else {
                                // Display other users' messages in the incoming section
                                ?>
                        <div class="chat_list">
                            <div class="chat_people">
                                <div class="chat_img"> <img src="./images/download.png" alt="sunil"> </div>
                                <div class="chat_ib">
                                    <!-- <h5>Sunil Rajput <span class="chat_date">Dec 25</span></h5> -->
                                    <p>
                                        <?php echo $messageText; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="mesgs">
                    <!-- Modify the msg_history section -->
                    <div class="msg_history" id="message-box">
                        <?php
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            while ($row = mysqli_fetch_assoc($result)) {
                                $senderId = $row['sender_id'];
                                $messageText = $row['message_text'];
                                $timestamp = $row['timestamp'];
                                $messageClass = ($senderId === $currentUserId) ? 'own-message' : 'other-message';

                            if ($senderId === $currentUserId) {
                                // Display the current user's message in the outgoing section
                                ?>
                        <div class="outgoing_msg">
                            <div class="sent_msg">
                                <p>
                                    <?php echo $messageText; ?>
                                </p>
                                <span class="time_date">
                                    <?php
                                    // $timestampFromDB = $timestamp; // Replace this with your actual timestamp from the database
                                    
                                    // Create a DateTime object from the database timestamp
                                    $dateTime = new DateTime($timestamp);
                                    
                                    // Format the DateTime object into the desired format
                                    $formattedDateTime = $dateTime->format('h:i A | F j');
                                    
                                    echo $formattedDateTime; // Output the formatted timestamp
                                    ?>
                                    <!-- 11:01 AM | June 9 -->
                                </span>
                            </div>
                        </div>
                        <?php
                            } else {
                                // Display other users' messages in the incoming section
                                ?>
                        <div class="incoming_msg">
                            <div class="incoming_msg_img"> <img src="./images/download.png" alt="<?php echo $_SESSION['username']; ?>">
                            </div>
                            <div class="received_msg">
                                <div class="received_withd_msg">
                                    <p>
                                        <?php echo $messageText; ?>
                                    </p>
                                    <span class="time_date">
                                        <?php
                                        $timestampFromDB = $timestamp; // Replace this with your actual timestamp from the database
                                        
                                        // Create a DateTime object from the database timestamp
                                        $dateTime = new DateTime($timestampFromDB);
                                        
                                        // Format the DateTime object into the desired format
                                        $formattedDateTime = $dateTime->format('h:i A | F j');
                                        
                                        echo $formattedDateTime; // Output the formatted timestamp
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        }
                        mysqli_stmt_close($stmt);
                        ?>
                    </div>
                    <form id="chat-form" method="POST">
                        <div class="input-group mb-3">
                            <input type="text" name="message" id="message-input" class="form-control"
                                placeholder="Type your message...">
                            <div class="input-group-append">
                                <input type="submit" name="submit" id="send-button" class="btn btn-primary"
                                    value="Submit" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <p class="text-center top_spac">Design by <a target="_blank"
                    href="https://www.linkedin.com/in/priyank-sukhadiya-0a0404214/">Priyank Sukjadiya</a></p>
        </div>
    </div>
    <?php } ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>

<?php
if (isset($_POST['submit'])) {
    // Process form submission

    // Sanitize and validate the message if necessary
    $message = $_POST['message'];

    // Save the message to the database
    $query = 'INSERT INTO messages (sender_id, message_text, timestamp) VALUES (?, ?, CURRENT_TIMESTAMP)';
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $currentUserId, $message);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Close database connection
    mysqli_close($connection);

    // Redirect to a different page to avoid form resubmission
    header("Location: {$_SERVER['REQUEST_URI']}");
    exit();
}
?>

