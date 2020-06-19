<?php set_time_limit(0);
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
global $mysql;

const URL = "http://ruz2.spbstu.ru/api/v1/ruz/scheduler/";


function cacheData($group_id, $weeks_amount = 3) {
    global $mysql;
    $date = new DateTime("last week");

    $calendar_data = array();
    for ($weeks = 0; $weeks < $weeks_amount; $weeks++) {
        $date->modify("monday");
        $json = curl_init();
        curl_setopt($json, CURLOPT_URL, URL . $group_id . "?date=" . $date->format("Y-m-d"));
        curl_setopt($json, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($json, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($json, CURLOPT_TIMEOUT, 5);
        $data = json_decode(curl_exec($json), true);
        curl_close($json);
        if (!$data)
            continue;

        $days = $data["days"];
        for ($i = 0; $i < count($days); $i++) {
           $day = $days[$i]["date"];
           $weekday = $days[$i]["weekday"];
           $lessons = &$days[$i]["lessons"]; //Не копируем, а работаем по ссылке
           for ($j = 0; $j < count($days[$i]["lessons"]); $j++) {
               $lesson = &$lessons[$j]; //Снова ссылка
               $teachers = [];
               $places = [];
               if (is_array($lesson["teachers"]))
                   foreach ($lesson["teachers"] as $teacher)
                        array_push($teachers, $teacher["full_name"]);
               if (is_array($lesson["auditories"]))
                    foreach ($lesson["auditories"] as $place) {
                        $room = (is_numeric($place["name"])) ? $place["building"]["name"] . ", ауд. " . $place["name"] :
                            $place["building"]["name"] . ", " . $place["name"];
                        array_push($places, $room);
               }
               $lesson_data = array(
                   "group_id" => $group_id,
                   "day" => $day,
                   "weekday" => $weekday,
                   "lesson" => $j,
                   "subject" => $lesson["subject_short"],
                   "type" => $lesson["typeObj"]["name"],
                   "time_start" => $lesson["time_start"],
                   "time_end" => $lesson["time_end"],
                   "teachers" => $teachers,
                   "places" => $places
               );
               array_push($calendar_data, $lesson_data);
           }
        }
        $date->modify("next week");
    }
    $insert_data = [];
    if (count($calendar_data) > 0) {
        $insert_data[] = $calendar_data[0]; // Снова ссылки, чтобы не копировать целый объект
        for ($i = 1; $i < count($calendar_data); $i++) {
            $curr = $calendar_data[$i];
            $prev = end($insert_data);
            if ($curr["day"] == $prev["day"] && $curr["subject"] == $prev["subject"] && $curr['type'] == $prev['type'] && $curr["time_start"] == $prev["time_start"]) {
                $prev["teachers"] = array_unique(array_merge($curr["teachers"], $prev["teachers"]));
                $prev["places"] = array_unique(array_merge($curr["places"], $prev["places"]));
                array_pop($insert_data);
                $insert_data[] = $prev;
            } else {
                $insert_data[] = $curr;
            }
        }
        foreach ($insert_data as &$lesson) {
            $lesson["teachers"] = json_encode($lesson["teachers"], JSON_UNESCAPED_UNICODE);
            $lesson["places"] = json_encode($lesson["places"], JSON_UNESCAPED_UNICODE);
        }
        $mysql->multiple_insert($insert_data);
        echo "Добавлено уникальных пар: " . count($insert_data) . "<br>";
        $until = (count($insert_data) > 0) ? end($insert_data)['day'] : "1970-01-01";
        $mysql->exec(QUERY_GROUP_UPDATE, RETURN_IGNORE, array("until" => $until, "id" => $group_id));
        return true;
    } else {
        return false;
    }

}
function cacheAll() {
    global $mysql;
    $result = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array());
    $i = 1;
    $count = count($result);
    foreach ($result as $key => $group) {
        if ($group['cache'] == 1) {
            echo "Начато кэширование группы " . $group['name'] . " <br>";
            cacheData($group["id"], 6);
            echo "Завершено кэширование группы " . $group['name'] . " ($i/$count) <br>";
            $i++;
        }
    }
}
cacheData(25888, 4);
