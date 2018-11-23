<?php

require_once("settings.php");

if (isset($_GET["stuff"])) {
        $conn = new mysqli($servername, $username, $password, $database);
        // Check connection
        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8");

        header('Content-type: application/json');

        if ($_GET["stuff"] == "categories_tree") {
                $categories = [];
                $categories_tree = [];
                $rs = $conn->query("SELECT * FROM " . TBL_CATEGORIES);
                if ($rs && $rs->num_rows > 0) {
                        $row = $rs->fetch_assoc();
                        while ($row) {
                                $categories[] = $row;
                                $row = $rs->fetch_assoc();
                        }
                        foreach ($categories as $category) {
                                $parent = intval($category["parent"]);
                                if ($parent == 0) {
                                        $categories_tree[] = buildtree($categories, $category);
                                }
                        }
                }
                echo json_encode($categories_tree);
        } else if ($_GET["stuff"] == "categories_levels") {
                $categories = [];
                $categories_levels = [];
                $rs = $conn->query("SELECT * FROM " . TBL_CATEGORIES);
                if ($rs && $rs->num_rows > 0) {
                        $row = $rs->fetch_assoc();
                        while ($row) {
                                $row["id"] = intval($row["id"]);
                                $categories[] = $row;
                                $row = $rs->fetch_assoc();
                        }
                        foreach ($categories as $category) {
                                $parent = intval($category["parent"]);
                                if ($parent == 0) {
                                        $level = 0;
                                        buildlevels($categories, $category, $categories_levels, $level);
                                }
                        }
                }
                echo json_encode($categories_levels);
        } else if ($_GET["stuff"] == "glosor") {
                if (isset($_GET["category"])) {
                        $category = $conn->real_escape_string($_GET["category"]);
                        if (ctype_digit($category)) {
                                $category = intval($category);
                                $pairs = [];
                                $rs = $conn->query("SELECT * FROM " . TBL_GLOSOR . " WHERE id IN (SELECT glos FROM " . TBL_CATEGORIES_GLOSOR_MAP . " WHERE category = $category)");
                                if ($rs && $rs->num_rows > 0) {
                                        $row = $rs->fetch_assoc();
                                        while ($row) {
                                                $pairs[] = $row;
                                                $row = $rs->fetch_assoc();
                                        }
                                }
                                echo json_encode($pairs);
                        }
                }
        } else if ($_GET["stuff"] == "similar") {
                $result = array();
                $result["bExists"] = false;
                $result["aExists"] = false;
                $result["bSimilar"] = "";
                $result["aSimilar"] = "";
                if (isset($_GET["b"]) && $_GET["b"] != "") {
                        $b = $conn->real_escape_string($_GET["b"]);
                        $word = $b;
                        $words = explode(" ", $b);
                        if (count($words) > 1) {
                                if ($words[0] == "der" || $words[0] == "die" || $words[0] == "das") {
                                        $word = $words[1];
                                }
                        }
                        $rs = $conn->query("SELECT * FROM " . TBL_GLOSOR . " WHERE b LIKE \"%$word%\"");
                        if ($rs && $rs->num_rows > 0) {
                                $row = $rs->fetch_assoc();
                                $result["bExists"] = true;
                                $result["bSimilar"] = $row["b"];
                        }
                }
                if (isset($_GET["a"]) && $_GET["a"] != "") {
                        $word = $conn->real_escape_string($_GET["a"]);
                        $words = explode(" ", $word);
                        if (count($words) > 1) {
                                if ($words[0] == "der" || $words[0] == "die" || $words[0] == "das") {
                                        $word = $words[1];
                                }
                        }
                        $rs = $conn->query("SELECT * FROM " . TBL_GLOSOR . " WHERE a LIKE \"%$word%\"");
                        if ($rs && $rs->num_rows > 0) {
                                $row = $rs->fetch_assoc();
                                $result["aExists"] = true;
                                $result["aSimilar"] = $row["a"];
                        }
                }
                echo json_encode($result);
        }

        $conn->close();
}

function buildtree($categories, $stem) {
        $id = intval($stem["id"]);
        $branch["id"] = $id;
        $branch["name"] = $stem["name"];
        foreach ($categories as $category) {
                $parent = intval($category["parent"]);
                if ($parent == $id) {
                        $branch["children"][] = buildtree($categories, $category);
                }
        }
        return $branch;
}
function buildlevels($categories, $stem, &$levels, $level) {
        $id = intval($stem["id"]);
        $category["id"] = $id;
        $category["name"] = $stem["name"];
        $levels[$level][] = $category;
        foreach ($categories as $category) {
                $parent = intval($category["parent"]);
                if ($parent == $id) {
                        buildlevels($categories, $category, $levels, $level + 1);
                }
        }
}
?>
