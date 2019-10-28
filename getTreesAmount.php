<?php

$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "SELECT amount, updated_at from trees";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $now = strtotime(date('Y-m-d H:i:s'));
        $seconds = $now - strtotime($row['updated_at']);
        if ($seconds < 3) {
            echo($row['amount']);
            $conn->close();
            exit();
        }
    }
}

$array = file("https://teamtrees.org/");

foreach($array as $line){
    preg_match('/.*data-count\s*=\s*"([0-9]+)"/', $line, $matches);
    if (count($matches) !== 0) {

        $stmt = $conn->prepare("UPDATE trees SET amount=?, updated_at=now() WHERE 1=1");
        $val = $matches[1];
        $stmt->bind_param("i", $val);
        $stmt->execute();
        $stmt->close();
        echo($matches[1]);

        $conn->close();
        exit();
    }
}

http_response_code(400);
echo('Teamtrees is blocking the requests');

$conn->close();

?>