<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Fallen Mage</title>
    <link rel="icon" href="images/f1.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="application/x-javascript"> addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        } </script>
    <!-- css -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all"/>
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
    <!--// css -->
    <!-- font -->
    <link href='//fonts.googleapis.com/css?family=Josefin+Sans:400,100,100italic,300,300italic,400italic,600,600italic,700,700italic'
          rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic'
          rel='stylesheet' type='text/css'>
    <!-- //font -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
</head>
<body>
<!-- banner -->
<div class="sub-banner">
    <!-- Navbar -->
    <nav class="navbar navbar-default">
        <div class="container">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="index.html"><h1>Fallen <img src="images/logo.png" alt=" "/> Mage</h1></a>
            </div>

            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hover-effect"><a href="index.html">Home</a></li>
                    <li class="hover-effect"><a href="about.html">About</a></li>
                    <li class="hover-effect"><a href="games.html">Download</a></li>
                    <li class="hover-effect"><a href="chat.php">Chat</a></li>
                    <li class="hover-effect active"><a href="contact.html">Contact</a></li>
                </ul>
            </div>

        </div>
    </nav>
</div>
<!-- banner -->
<?php
include('con.php');

class AddMessage
{
    private $db;
    private $connection;

    function __construct()
    {
        $this->db = new Connection();
        $this->connection = $this->db->get_connection();
    }

    public function add_message($name, $phone, $email, $subject, $message)
    {
        $query = "INSERT INTO `contactUsMessages`(`id`, `name`, `phone`, `email`, `subject`, `message`) VALUES (NULL,'$name','$phone','$email','$subject','$message')";
        $is_inserted = mysqli_query($this->connection, $query);
        if ($is_inserted == 1) {
            print("<br/>");
            print("<br/>");
            print("<h1>Thank you for contacting us. Your message has been sent successfully</h1>");
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            print("<h1>Internal Server Error</h1>");
        }
        mysqli_close($this->connection);

    }
}

$addMessage = new AddMessage();
if (isset($_POST['message'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['name'], $_POST['subject'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    if (!empty($phone) && !empty($email) && !empty($message)) {
        $addMessage->add_message($name, $phone, $email, $subject, $message);
    }
}
?>
<!-- footer -->
<div class="footer">
    <div class="container">
        <div class="col-md-3 footer-left-w3">
            <h4>Contact</h4>
            <ul>
                <li><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></li>
                <li><a href="mailto:tjsummour17@gmail.com"><h6>tjsummour17@gmail.com</h6></a></li>
            </ul>
            <ul>
                <li><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></li>
                <li><h6>+962786642919</h6></li>
            </ul>
            <ul>
                <li><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span></li>
                <li><h6>Amman - Jordan</h6></li>
            </ul>
        </div>
        <div class="col-md-4 footer-right-w3">
            <a href="index.html"><h4>Fallen <img src="images/f1.png" alt=" "/> Mage</h4></a>
            <p>Free your kingdom now</p>
        </div>
        <div class="clearfix"></div>

    </div>
</div>
<!-- //footer -->
</body>
</html>
