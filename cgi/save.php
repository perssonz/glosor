<?php

require_once("settings.php");

if (isset($_POST["category"]) && isset($_POST["a"]) && isset($_POST["b"])) {
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
        $query_check = "SELECT id FROM " . TBL_CATEGORIES . " WHERE name=\"";

        $categories = [];
        for ($i = 0; $i < count($_POST["category"]); $i++) {
                $category_name = $conn->real_escape_string($_POST["category"][$i]);
                $rs = $conn->query($query_check . $category_name . "\"");
                if ($rs && $rs->num_rows > 0) {
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
        $stmt = $conn->prepare("INSERT INTO " . TBL_GLOSOR . " (a, b) VALUES(?, ?)");
        $stmt->bind_param("ss", $a, $b);

        $category = 0;
        $glos = 0;
        $stmt_map = $conn->prepare("INSERT INTO " . TBL_CATEGORIES_GLOSOR_MAP . " (category, glos) VALUES(?, ?)");
        $stmt_map->bind_param("ii", $category, $glos);

        for ($i = 0; $i < count($_POST["a"]); $i++) {
                $a = $_POST["a"][$i];
                $b = $_POST["b"][$i];
                $stmt->execute();
                $glos = $conn->insert_id;
                foreach ($categories as $category) {
                        $stmt_map->execute();
                }
        }
        $stmt_map->close();
        $stmt->close();

        $conn->close();
        echo "SUCCESS!";
}

?>
