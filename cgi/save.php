<?php

require_once("settings.php");

if (isset($_POST["g-recaptcha-response"]) && check_recaptcha($recaptcha_secret, $_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"])) {
        if (isset($_POST["stuff"])) {
                $stuff = $_POST["stuff"];
                if ($stuff == "glosor" && isset($_POST["password"]) && $_POST["password"] == $add_password) {
                        if (isset($_POST["category"]) && isset($_POST["a"]) && isset($_POST["b"])) {
                                save_glosor($_POST["category"], $_POST["a"], $_POST["b"], $servername, $username, $password, $database);
                                echo "0";
                        }
                } else if ($stuff == "stats") {
                        if (isset($_POST["stats"])) {
                                save_stats($_POST["stats"], $servername, $username, $password, $database);
                                echo "0";
                        }
                }
        }
}

function check_recaptcha($recaptcha_secret, $response, $remoteip) {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = array("secret" => $recaptcha_secret, "response" => $response, "remoteip" => $remoteip);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
                die("Captcha check error.");
        }

        $result = json_decode($result);
        return $result->success;
}

function save_glosor($post_category, $post_a, $post_b, $servername, $username, $password, $database) {
        $conn = new mysqli($servername, $username, $password, $database);
        // Check connection
        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8");

        header('Content-type: application/json');

        $sql_create_glosortable = "CREATE TABLE " . TBL_GLOSOR . " (
                                        id INT NOT NULL AUTO_INCREMENT,
                                        a VARCHAR(500),
                                        b VARCHAR(500),
                                        known INT,
                                        PRIMARY KEY(id)
                                        )";
        $sql_create_categoriestable = "CREATE TABLE " . TBL_CATEGORIES . " (
                                        id INT NOT NULL AUTO_INCREMENT,
                                        name VARCHAR(500),
                                        parent INT,
                                        PRIMARY KEY(id)
                                        )";
        $sql_create_categoriesglosormaptable = "CREATE TABLE " . TBL_CATEGORIES_GLOSOR_MAP . " (
                                        id INT NOT NULL AUTO_INCREMENT,
                                        category INT,
                                        glos INT,
                                        PRIMARY KEY(id)
                                        )";

        if (!$conn->query("SELECT 1 FROM " . TBL_GLOSOR . " LIMIT 1;")) {
                if (!$conn->query($sql_create_glosortable)) {
                        die("Table creation failed: (" . $conn->errno . ") " . $conn->error);
                }
        }
        if (!$conn->query("SELECT 1 FROM " . TBL_CATEGORIES . " LIMIT 1;")) {
                if (!$conn->query($sql_create_categoriestable)) {
                        die("Table creation failed: (" . $conn->errno . ") " . $conn->error);
                }
        }
        if (!$conn->query("SELECT 1 FROM " . TBL_CATEGORIES_GLOSOR_MAP . " LIMIT 1;")) {
                if (!$conn->query($sql_create_categoriesglosormaptable)) {
                        die("Table creation failed: (" . $conn->errno . ") " . $conn->error);
                }
        }

        $category_name = "";
        $category_parent = 0;
        $stmt = $conn->prepare("INSERT INTO " . TBL_CATEGORIES . " (name, parent) VALUES(?, ?)");
        $stmt->bind_param("si", $category_name, $category_parent);

        $categories = [];
        for ($i = 0; $i < count($post_category); $i++) {
                $category_name = $conn->real_escape_string($post_category[$i]);
                $rs = $conn->query("SELECT id FROM " . TBL_CATEGORIES . " WHERE name=\"" . $category_name . "\" AND parent=" . $category_parent);
                if (!$rs) {
                        die("Category check failed (" . $conn->errno . ") " . $conn->error);
                } else if ($rs->num_rows > 0) {
                        $row = $rs->fetch_assoc();
                        $categories[] = intval($row["id"]);
                        $category_parent = intval($row["id"]);
                } else {
                        $stmt->execute();
                        $category_parent = $conn->insert_id;
                        $categories[] = $conn->insert_id;
                }
        }
        $stmt->close();

        $a = "";
        $b = "";
        $stmt = $conn->prepare("INSERT INTO " . TBL_GLOSOR . " (a, b, known) VALUES(?, ?, 0)");
        $stmt->bind_param("ss", $a, $b);

        $category = 0;
        $glos = 0;
        $stmt_map = $conn->prepare("INSERT INTO " . TBL_CATEGORIES_GLOSOR_MAP . " (category, glos) VALUES(?, ?)");
        $stmt_map->bind_param("ii", $category, $glos);

        for ($i = 0; $i < count($post_a); $i++) {
                $a = $post_a[$i];
                $b = $post_b[$i];
                $stmt->execute();
                $glos = $conn->insert_id;
                foreach ($categories as $category) {
                        $stmt_map->execute();
                }
        }
        $stmt_map->close();
        $stmt->close();

        $conn->close();
}

function save_stats($post_stats, $servername, $username, $password, $database) {
        $conn = new mysqli($servername, $username, $password, $database);
        // Check connection
        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8");

        header('Content-type: application/json');

        $known = 0;
        $id = 0;
        $stmt = $conn->prepare("UPDATE " . TBL_GLOSOR . " SET known = known + ? WHERE id = ?");
        $stmt->bind_param("ii", $known, $id);

        foreach ($post_stats as $stat) {
                $id = intval($stat["id"]);
                $known = intval($stat["known"]);
                $stmt->execute();
        }

        $stmt->close();

        $conn->close();
}
?>
