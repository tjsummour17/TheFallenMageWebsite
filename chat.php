<html>
<head>
    <!-- css -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all"/>
    <link href="css/popup-box.css" rel="stylesheet" type="text/css" media="all"/>
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
    <style>
        body {
            font-family: calibri;
        }

        .error {
            color: #FF0000;
        }

        .chat-connection-ack {
            color: #26af26;
        }

        .chat-message {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        #btnSend {
            background: #26af26;
            border: #26af26 1px solid;
            border-radius: 4px;
            color: #FFF;
            display: block;
            margin: 15px 0px;
            padding: 10px 50px;
            cursor: pointer;
        }

        #chat-box {
            background: #fff8f8;
            border: 1px solid #ffdddd;
            border-radius: 4px;
            border-bottom-left-radius: 0px;
            border-bottom-right-radius: 0px;
            min-height: 300px;
            padding: 10px;
            overflow: auto;
        }

        .chat-box-html {
            color: #09F;
            margin: 10px 0px;
            font-size: 0.8em;
        }

        .chat-box-message {
            color: #09F;
            padding: 5px 10px;
            background-color: #fff;
            border: 1px solid #ffdddd;
            border-radius: 4px;
            display: inline-block;
        }

        .chat-input {
            border: 1px solid #ffdddd;
            border-top: 0px;
            width: 100%;
            box-sizing: border-box;
            padding: 10px 8px;
            color: #191919;
        }
    </style>
    <script src="https://code.jquery.com/jquery-1.9.1.js"></script>
    <script>
        function showMessage(messageHTML) {
            $('#chat-box').append(messageHTML);
        }

        $(document).ready(function () {
            var websocket = new WebSocket("ws://localhost:8090/php-socket.php");
            websocket.onopen = function (event) {
                showMessage("<div class='chat-connection-ack'>Connection is established!</div>");
            }
            websocket.onmessage = function (event) {
                var Data = JSON.parse(event.data);
                showMessage("<div class='" + Data.message_type + "'>" + Data.message + "</div>");
                $('#chat-message').val('');
            };

            websocket.onerror = function (event) {
                showMessage("<div class='error'>Problem due to some Error</div>");
            };
            websocket.onclose = function (event) {
                showMessage("<div class='chat-connection-ack'>Connection Closed</div>");
            };

            $('#frmChat').on("submit", function (event) {
                event.preventDefault();
                $('#chat-user').attr("type", "hidden");
                var messageJSON = {
                    chat_user: $('#chat-user').val(),
                    chat_message: $('#chat-message').val()
                };
                websocket.send(JSON.stringify(messageJSON));
            });
        });


    </script>
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
                    <li class="hover-effect active"><a href="about.html">About</a></li>
                    <li class="hover-effect"><a href="games.html">Download</a></li>
                    <li class="hover-effect"><a href="chat.php">Chat</a></li>
                    <li class="hover-effect"><a href="contact.html">Contact</a></li>
                </ul>
            </div>

        </div>
    </nav>
</div>
<!-- banner -->
<form name="frmChat" id="frmChat">
    <div id="chat-box"></div>
    <input type="text" name="chat-user" id="chat-user" placeholder="Name" class="chat-input" required/>
    <input type="text" name="chat-message" id="chat-message" placeholder="Message" class="chat-input chat-message"
           required/>
    <input type="submit" id="btnSend" name="send-chat-message" value="Send">
</form>

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
            <p class="agileinfo">Suspendisse convallis malesuada libero, non rutrum arcu pellentesque lacinia.</p>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- //footer -->
</body>
</html>