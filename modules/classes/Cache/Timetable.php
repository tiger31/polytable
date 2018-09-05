<?php

namespace Cache;
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
set_time_limit(0);
global $mysql;
class Timetable {

    const URL = "http://ruz2.spbstu.ru/api/v1/ruz/scheduler/";

    public static function lesson_sort($a, $b) {
        if ($a["is_odd"] == $b["is_odd"])
            if ($a["weekday"] == $b["weekday"])
                if ($a["lesson"] == $b["lesson"])
                    return 0;
                else return ($a["lesson"] < $b["lesson"]) ? -1 : 1;
            else return ($a["weekday"] < $b["weekday"]) ? -1 : 1;
        else return ($a["is_odd"] < $b["is_odd"]) ? -1 : 1;
    }

    public static function lesson_diff($a, $b) {
        $keys = ["subject", "type", "time_start", "time_end", "teachers", "places"];
        $diff = [];
        if (self::lesson_sort($a, $b) == 0) {
            foreach ($keys as $key) {
                if ($a[$key] != $b[$key]) {
                   $diff[$key] = $b[$key];
                }
            }
        } else return false;
        return $diff;
    }
    public static function is_lesson_diff($a, $b) {
        $keys = ["subject", "type", "time_start", "time_end", "teachers", "places"];
        if (self::lesson_sort($a, $b) == 0) {
            foreach ($keys as $key) {
                if ($a[$key] != $b[$key]) {
                    return true;
                }
            }
            return false;
        } else return true;
    }

    public static function chain($changes, $stored_changes) {
        $new_changes = [];
        $move_to_static = [];
        $grouped = array();
        foreach ($stored_changes as $lesson)
            $grouped[$lesson['day']][] = $lesson;

        foreach ($changes as $lesson) {
            if (isset($grouped[$lesson['day']])) {
                foreach ($grouped[$lesson['day']] as $old) {
                    if (self::lesson_sort($lesson, $old) == 0) {
                        if (self::is_lesson_diff($lesson, $old)) {
                            $new_changes[] = $lesson;
                        } else
                            break;
                    }
                }
            } else {
                $new_changes[] = $lesson;
            }
        }
        $final = [];
        foreach ($new_changes as $lesson) {
            $date = new \DateTime($lesson['day']);
            $date->modify("-2 weeks");
            $past = $date->format("Y-m-d");
            if (isset($grouped[$past])) {
                $found = false;
                foreach ($grouped[$past] as $old) {
                    if (self::lesson_sort($lesson, $old) == 0 && !self::is_lesson_diff($lesson, $old)) {
                        $found = true;
                        $lesson['chain'] = (int)$old['chain'] + 1;
                        if ($lesson['chain'] >= 3)
                            $move_to_static[$lesson['action']][] = $lesson;
                        else
                            $final[] = $lesson;
                    }
                }
                if (!$found) {
                    $lesson['chain'] = 1;
                    $final[] = $lesson;
                }
            } else {
                $lesson['chain'] = 1;
                $final[] = $lesson;
            }
        }
        return array("static" => $move_to_static, "dynamic" => $final);
    }

    public static function timetable_diff($static, $dynamic) {
        $changes = [];
        //Определяем начала четной/нечетной недели, чтобы можно было переносить записи из static в dynamic
        if ($dynamic['week1']['is_odd'] == 1) {
            $even = new \DateTime(str_replace(".", "-", $dynamic['week2']['date_start']));
            $odd = new \DateTime(str_replace(".", "-", $dynamic['week1']['date_start']));
        } else {
            $even = new \DateTime(str_replace(".", "-", $dynamic['week1']['date_start']));
            $odd = new \DateTime(str_replace(".", "-", $dynamic['week2']['date_start']));
        }
        $static_left = [];
        foreach($static as $s_key => $static_lesson) {
            $found = false;
            $key = 0;
            foreach ($dynamic["lessons"] as $n_key => $nonstatic_lesson) {
                if (self::lesson_sort($static_lesson, $nonstatic_lesson) == 0) {
                    if (self::is_lesson_diff($static_lesson, $nonstatic_lesson)) {
                        $nonstatic_lesson["action"] = "CHANGE";
                        $changes[] = $nonstatic_lesson;
                    }
                    $found = true;
                    $key = $n_key;
                }
            }
            if ($found) {
               unset($dynamic[$key]);
            } else {
                $static_lesson["action"] = "ERASE";
                $static_left[] = $static_lesson;
            }
        }
        foreach ($static_left as &$lesson) {
            $date = new \DateTime(($lesson['is_odd'] == 1) ? $odd->format("Y-m-d") : $even->format("Y-m-d"));
            $date->modify("+ " . ($lesson['weekday'] - 1) . " days");
            $lesson['day'] = $date->format("Y-m-d");
        }
        return array_merge($changes, $static_left);
    }


    public static function analyse($weeks, $group_id) {
        $database = self::static_from_database($group_id);
        $data = $weeks;
        $static = [];
        $static[] = self::static_from_cache($data[0], $data[1], $group_id);
        $static[] = self::static_from_cache($data[2], $data[3], $group_id);

        for ($i = 0; $i < 1; $i++) {
            $dynamic_stored = self::dynamic_from_database($group_id);
            $diff = self::chain(self::timetable_diff($database, $static[$i]), $dynamic_stored);

            //self::insert_static($diff["static"]["CHANGE"] ?? []);
            //self::remove_static($diff["static"]["ERASE"] ?? []);
            self::insert_dynamic($diff['dynamic'] ?? []);
        }

    }

    public static function form_static() {
        $data = json_decode(file_get_contents("../../../data/24113"), true);
        $cache = self::static_from_cache($data[2], $data[3], 24113);
        $database = self::static_from_database(24113);
        self::insert_static($database);
        $s = self::chain(self::timetable_diff($database, $cache), 24113);
        var_dump($s);
    }

    public static function cache_groups() {
        global $mysql;
        $groups = ["23336/3"];
        $i = 1;
        foreach ($groups as $group_s) {
            $group = $mysql(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $group_s));
            if ($group['year'] == 2018 && $group['cache'] == 1) {
                echo "Начато кэширование группы " . $group['name'] . " <br>";
                self::cache_group($group);
                echo "Завершено кэширование группы " . $group['name'] . " ($i/1256) <br>";
                $i++;
            }
        }
    }

    public static function tmp() {
        $str = file_get_contents("../../../data/123.txt");
        //$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        $data = json_decode($str, true);
        var_dump($str);
        $cache = self::static_from_cache($data, array(
            "week" => array(
                "is_odd" => false,
                "date_start" => "2018.09.03",
                "date_end" => "2018.09.09"
            ),
            "days" => array()
        ), 28000);
        var_dump($cache['lessons']);
        self::insert_static($cache['lessons']);

    }

    public static function cache_group($group) {
        global $mysql;
        $date = new \DateTime();
        $data = self::read_timetable($group['id']);
        $trust = true;
        if ($group['cache_static'] == 0) {
            if (isset($data[0]['week']['untrusted']) || isset($data[1]['week']['untrusted']))
                $trust = false;
            if ($trust) {
                $static = self::static_from_cache($data[0], $data[1], $group['id']);
                self::insert_static($static['lessons']);
            }
        }
                    //self::analyse($data, $group['id']);
        if (!$trust) {
            echo "Странный ответ от ruz.spbstu.ru для группы " . $group['name'] . " <br>";
        }
        $mysql(QUERY_GROUP_UPDATE, RETURN_IGNORE, array("id" => $group['id'], "cache_last" => $date->format("Y-m-d H:i:s")));
    }

    public static function read_timetable($group_id) {
        $date = new \DateTime("2018-09-17");
        $calendar_data = array();
        for ($i = 0; $i < 2; $i++) {
            $date->modify("monday");
            $json = curl_init();
            curl_setopt($json, CURLOPT_URL, Timetable::URL . $group_id . "?date=" . $date->format("Y-m-d"));
            curl_setopt($json, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($json, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($json, CURLOPT_TIMEOUT, 5);
            $data = json_decode(curl_exec($json), true);
            curl_close($json);
            if (!$data || !isset($data['week'])) {
                $calendar_data[] = array( "week" => array("untrusted" => true));
            } else {
                $calendar_data[] = $data;
            }
            $date->modify("next week");
        }
        return $calendar_data;
    }

    public static function static_from_cache($first, $second, $group_id) {
        $arr = array_merge(self::cache_to_arr($first, $group_id), self::cache_to_arr($second, $group_id));
        usort($arr, array("Cache\Timetable", "lesson_sort"));
        return array("week1" => $first['week'], "week2" => $second['week'], "lessons" => $arr);
    }

    public static function static_from_database($group_id) {
        global $mysql;
        $data = $mysql(QUERY_CALENDAR_STATIC_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $group_id));
        if (!$data) $data = [];
        else if (is_assoc($data)) $data= [$data];
        foreach ($data as &$lesson) {
            $lesson['teachers'] = json_decode($lesson['teachers'], true);
            $lesson['places'] = json_decode($lesson['places'], true);
        }
        return $data;
    }

    public static function dynamic_from_database($group_id) {
        global $mysql;
        $dynamic_stored = $mysql(QUERY_CALENDAR_DYNAMIC_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $group_id));
        if (!$dynamic_stored) $dynamic_stored = [];
        else if (is_assoc($dynamic_stored)) $dynamic_stored = [$dynamic_stored];
        foreach ($dynamic_stored as &$lesson) {
            $lesson['teachers'] = json_decode($lesson['teachers'], true);
            $lesson['places'] = json_decode($lesson['places'], true);
        }
        return $dynamic_stored;
    }

    public static function insert_static($data) {
        global $mysql;
        foreach ($data as &$lesson) {
            $lesson["teachers"] = json_encode($lesson["teachers"], JSON_UNESCAPED_UNICODE);
            $lesson["places"] = json_encode($lesson["places"], JSON_UNESCAPED_UNICODE);
        }
        if (count($data) > 0) {
            $date = new \DateTime();
            $mysql->mli(MLI_QUERY_CALENDAR_STATIC, $data);
            $mysql(QUERY_GROUP_UPDATE, RETURN_IGNORE, array(
                "id" => $data[0]['group_id'],
                "static_changed" => $date->format("Y-m-d H:i:s")
            ));
        }
    }

    public static function remove_static($data) {
        global $mysql;
        foreach ($data as $lesson) {
            $mysql(QUERY_CALENDAR_STATIC_REMOVE, RETURN_IGNORE, array(
                "group_id" => $lesson['group_id'],
                "weekday" => $lesson['weekday'],
                "lesson" => $lesson['lesson'],
                "is_odd" => $lesson['is_odd']
            ));
        }
    }

    public static function insert_dynamic($data) {
        global $mysql;
        foreach ($data as &$lesson) {
            $lesson["teachers"] = json_encode($lesson["teachers"], JSON_UNESCAPED_UNICODE);
            $lesson["places"] = json_encode($lesson["places"], JSON_UNESCAPED_UNICODE);
        }
        if (count($data) > 0) $mysql->mli(MLI_QUERY_CALENDAR_DYNAMIC, $data);
    }

    public static function cache_to_arr($week, $group_id) {
        $calendar_data = array();
        $days = $week["days"];
        for ($i = 0; $i < count($days); $i++) {
            $weekday = $days[$i]["weekday"];
            $lessons = &$days[$i]["lessons"]; //Не копируем, а работаем по ссылке
            for ($j = 0; $j < count($days[$i]["lessons"]); $j++) {
                $lesson = &$lessons[$j]; //Снова ссылка
                $teachers = [];
                $places = [];
                if (is_array($lesson["teachers"]))
                    foreach ($lesson["teachers"] as $teacher)
                        array_push($teachers, array("name" => $teacher["full_name"], "id" => $teacher["id"]));
                if (is_array($lesson["auditories"]))
                    foreach ($lesson["auditories"] as $place) {
                        $room = (is_numeric($place["name"])) ? $place["building"]["name"] . ", ауд. " . $place["name"] :
                            $place["building"]["name"] . ", " . $place["name"];
                        array_push($places, array("name" => $room, "room_id" => $place['id'], "building_id" => $place["building"]["id"]));
                    }
                $lesson_data = array(
                    "id" => (int)($group_id . ($week["week"]["is_odd"] ? 1 : 0) . $weekday . $j),
                    "group_id" => $group_id,
                    "day" => $days[$i]['date'],
                    "weekday" => $weekday,
                    "lesson" => $j,
                    "is_odd" => $week["week"]["is_odd"] ? 1 : 0,
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
        $insert_data = [];
        if (count($calendar_data) > 0) {
            $insert_data[] = $calendar_data[0]; // Снова ссылки, чтобы не копировать целый объект
            for ($i = 1; $i < count($calendar_data); $i++) {
                $curr = $calendar_data[$i];
                $prev = end($insert_data);
                if ($curr["weekday"] == $prev["weekday"] && $curr["is_odd"] == $prev["is_odd"] && $curr["subject"] == $prev["subject"] && $curr['type'] == $prev['type'] && $curr["time_start"] == $prev["time_start"]) {
                    $prev["teachers"] = array_unique(array_merge($curr["teachers"], $prev["teachers"]), SORT_REGULAR);
                    $prev["places"] = array_unique(array_merge($curr["places"], $prev["places"]), SORT_REGULAR);
                    array_pop($insert_data);
                    $insert_data[] = $prev;
                } else {
                    $insert_data[] = $curr;
                }
            }
        }
        return $insert_data;
    }

    public static function fine_format($lessons) {
        $timetable = [];
        foreach ($lessons as $lesson) {
            $timetable[$lesson['is_odd']][$lesson['weekday']][$lesson['lesson']] = $lesson;
        }
        return $timetable;
    }

}
//Timetable::tmp();
//Timetable::cache_groups();
//Timetable::cache_group($mysql(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("id" =>  26708)));

class Week {
    public $time_start;
    public $time_end;
    public $evenness;
    public $days = array();

    public function __construct() {

    }
}
