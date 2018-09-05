<?php
global $mysql;
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";

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

    $db = $mysql(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array());
    $actual = array();
    foreach ($db as $dbg)
        $actual[$dbg['id']] = $dbg;

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
            if (!isset($actual[$group['id']]))
            $mysql->exec(QUERY_GROUP_INSERT, RETURN_IGNORE, array(
                "name" => $group["name"],
                "id" => $group["id"],
                "university_id" => 1,
                "faculty_id" => $faculty["id"],
                "faculty_name" => $faculty["name"],
                "faculty_abbr" => $faculty["abbr"],
                "year" => 2018
            ));
        }

    }
    return true;
}
cache_groups();
