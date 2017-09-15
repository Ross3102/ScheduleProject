<?php

require_once dirname(__FILE__) . "/../../config.php";
include dirname(__FILE__) . "/../model/userdb.php";

try {
    $db = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Unable to connect to database: " . $e->getMessage(), 0);

    echo ("Unable to connect to database.");
    exit;
}

$user_id = 1;

$user = get_user_by_id($user_id);

function pad($num, $target) {
    $num = array_map('intval', str_split($num));
    $os = "";
    $length = count($num);
    while ($length + strlen($os) < $target) {
        $os .= "0";
    }
    return $os . implode($num);
}

function duration_to_int($item_duration) {
    $item_duration = explode(":", $item_duration);
    return $item_duration[0] * 3600 + $item_duration[1] * 60 + $item_duration[2];
}

function int_to_duration($item_duration) {
    $item_hours = intdiv($item_duration, 3600);
    $item_hours  = pad($item_hours, 1);
    $item_duration %= 3600;
    $item_minutes = intdiv($item_duration, 60);
    $item_minutes = pad($item_minutes, 2);
    $item_duration %= 60;
    $item_duration = pad($item_duration, 2);
    return $item_hours . ":" . $item_minutes . ":" . $item_duration;
}

function writeHeader($head='') {
    global $web_root;
    echo '
    <html>
    <head>
        <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/css/materialize.min.css">
        <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link type="text/css" rel="stylesheet" href="/' . $web_root . '/css/shared.css">
        <script src="/' . $web_root . '/js/jq.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.1/js/materialize.min.js" type="text/javascript"></script>
    </head>
    <body>
    ' . $head . '
    <header>
        <nav class="green">
            <div class="container">
                <div class="nav-wrapper">
                    <a href="/' . $web_root . '/dashboard" class="brand-logo">BCA Work Manager</a>
                    <a data-activates="sidenav" class="button-collapse"><i class="material-icons">menu</i></a>
                    <ul class="right hide-on-med-and-down">
                        <li><a href="/' . $web_root . '/dashboard">Dashboard</a></li>
                    </ul>
                    <ul class="side-nav" id="sidenav">
                        <li><a href="/' . $web_root . '/dashboard">Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
    ';
}

function writeFooter() {
    echo '
    </main>
    </body>
    <script>
        $(document).ready(function() {
            var tool = $(".tooltipped");
            tool.attr({
                "data-delay": 50,
                "data-position": "right"
            });
            $(".button-collapse").sideNav();
        });
    </script>
    </html>';
}

function create_dashboard_item($link, $title, $description, $icon) {
    echo "<a class='collection-item avatar' href='../" . $link . "'>
            <i class='material-icons circle red'>" . $icon . "</i>
            <h6 class='dash-title'>" . $title . "</h6>
            <p class='small-text'>" . $description . "</p>
        </a>";
}

function addTime($time, $duration) {
    $secs = strtotime($duration) - strtotime("00:00:00");
    return date("h:i:s",strtotime($time)+$secs);
}