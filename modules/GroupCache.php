<?php
include_once "Connect.php";
global $mysql;
$mysql->set_active(QUERY_GROUP_SELECT, QUERY_GROUP_INSERT, QUERY_GROUP_UPDATE);

const faculty = "http://ruz2.spbstu.ru/api/v1/ruz/faculties";

function cache_groups() {
    global $mysql;
    $faculties = curl_init();
    curl_setopt($faculties, CURLOPT_URL, faculty);
    curl_setopt($faculties, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($faculties, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($faculties, CURLOPT_TIMEOUT, 3);

    $json = curl_exec($faculties);

    if (!$json) return false;

    $f = json_decode($json, true)["faculties"];
    curl_close($faculties);

    $groups_stored = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array());

    foreach ($f as $faculty) {
        usleep(rand(500000, 1000000)); //
        echo "-----jumped to faculty " . $faculty["id"] . "<br/>";
        $groups = curl_init();
        curl_setopt($groups, CURLOPT_URL, faculty . "/" . $faculty["id"] . "/groups");
        curl_setopt($groups, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($groups, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($groups, CURLOPT_TIMEOUT, 3);

        $json_groups = curl_exec($groups);

        if (!$json_groups) continue;

        $data = json_decode($json_groups, true);
        curl_close($groups);

        foreach ($data["groups"] as $group) {
            $key = array_search($group["name"], array_column($groups_stored, "name"));
            if (is_int($key)) {
                if ($group["id"] != $groups_stored[$key]["id"]) {
                    $mysql->exec(QUERY_GROUP_UPDATE, RETURN_IGNORE, array("name" => $group["name"], "id" => $group["id"]));
                }
            } else {
                $mysql->exec(QUERY_GROUP_INSERT, RETURN_IGNORE, array("name" => $group["name"], "id" => $group["id"], "university_id" => 1));
            }
        }

    }
    return true;
}
