<?php
declare(strict_types=1);
setlocale(LC_TIME,  'Russian_Russia.1251,','ru_ru', 'ru_RU.utf8');
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
class Calendar {
    private $month;
    private $currentDay;
    private $days = array();
    private $shift;

    function __construct($json, $homework) {
        //Dates
        $this->month = new DateTime("first day of this month");
        $tmp = new DateTime("first day of this month");
        $this->currentDay = new DateTime("this day");
        $this->shift = (($this->month->format("w") == 0) ? 6 : ((int)$this->month->format("w") - 1));
        //JSON
        $data = json_decode($json, true);
        $date_link = array();
        //Link data with date
        for ($i = 0; $i < count($data); $i++) {
            array_push($date_link, $data[$i]["date"]);
        }
        //Homework integration
        $homework = homework_restruct((is_assoc($homework) ? array($homework) : $homework));
        for ($i = 0; $i < date("t", $this->month->getTimestamp()); $i++) {
            $date = $tmp->format("Y-m-d");
            $key = array_search($date, $date_link);
            if ($key !== false && isset($homework[$date])) $data[$key]['homework'] = $homework[$date];
            array_push($this->days, new Day($tmp, (int)$tmp->format("w"),($key !== false) ? $data[$key] : null));
            $tmp->modify("next day");
        }
    }

    function days() : array {
        return $this->days;
    }

    function shift() {
        return $this->shift;
    }

}

class Day {
    public $day;
    public $date;
    public $week_day;
    private $json;
    private $homework;
    public $has_cache = false;
    public $lessons_count;
    public $time_start;
    public $time_end;
    public $lessons = array();
    private $today;

    function __construct(DateTime $day, int $weekDay, $json) {

        $this->today = new DateTime("this day");

        $this->day = new DateTime($day->format("Y-m-d"));
        $this->week_day = ($weekDay == "0") ? 7 : $weekDay;
        if ($json !== null) {
            $this->has_cache = true;
            $this->json = $json;
            $this->date = $json["date"];
            $this->lessons_count = count($json["lessons"]);
            $this->homework = (isset($json['homework'])) ? $json['homework'] : null;
            if ($this->lessons_count > 0) {
                $this->time_start = $json["lessons"][0]["time_start"];
                $this->time_end = end($json["lessons"])["time_end"];
            }
        }
        if ($this->has_cache) {
            $count = 0;
            foreach ($json['lessons'] as $lesson) {
                if (isset($this->homework[$count])) $lesson['homework'] = $this->homework[$count];
                array_push($this->lessons, new Lesson($lesson, $this, $count));
                $count++;
            }
        }
    }

    function is_today() {
        return $this->day->format("Ymd") == $this->today->format("Ymd");
    }
    function get_abbr() {
        switch ($this->week_day) {
            case 1:
                return "ПН";
                break;
            case 2:
                return "ВТ";
                break;
            case 3:
                return "СР";
                break;
            case 4:
                return "ЧТ";
                break;
            case 5:
                return "ПТ";
                break;
            case 6:
                return "СБ";
                break;
            case 7:
                return "ВС";
                break;
            default:
                return "";
        }
        //return (strftime("%a", $this->day->getTimestamp()));
    }

    function get_class() {
        $classes = array();
        if ($this->is_today()) array_push($classes, "current");
        if ($this->is_today() && $this->week_day !== 7) array_push($classes, " active");
        if ($this->day->format("d") < $this->today->format("d")) array_push($classes, "past");
        if ($this->week_day == 7) array_push($classes, "weekend");
        if (!$this->has_cache) array_push($classes, "uncached");
        return join(" ", $classes);
    }

    function template() {
        include $_SERVER['DOCUMENT_ROOT'] . "/templates/calendar/calendar_day.php";
    }

    function print_lessons_info() {
        $prefix = $this->get_prefix();

        if (!$this->has_cache) return "На " . $prefix ." данных нет, удачи :)";
        if ($this->lessons_count === 0) return $prefix . " лекций нет";
        return $prefix . " пар: " . $this->lessons_count;
    }
    function get_prefix() {
        if ($this->date == $this->today->format("Y-m-d")) return "Сегодня";
        $tomorrow = new DateTime("tomorrow");
        if ($this->date == $tomorrow->format("Y-m-d")) return "Завтра";
        $tomorrow->modify("next day");
        if ($this->date == $tomorrow->format("Y-m-d")) return "Послезавтра";
        //TODO strftime()
        return $this->day->format("d.m.Y");
    }
}

class Lesson {
    public $day;
    public $count;
    public $name;
    public $time_start;
    public $time_end;
    public $type_name;
    public $teachers = array();
    public $place = array();
    public $homework_text;
    public $homework_files;

    function __construct($data, $day, $count) {
        $this->day = $day;
        $this->count = $count;
        $this->name = $data['subject_short'];
        $this->time_start = $data['time_start'];
        $this->time_end = $data['time_end'];
        $this->type_name = $data['typeObj']['name'];
        $this->homework = (isset($data['homework'])) ? $data['homework'] : null;
        if ($data['teachers'] !== null)
            for ($i = 0; $i < count($data['teachers']); $i++) {
                array_push($this->teachers, $data['teachers'][$i]['full_name']);
            }
        if ($data['auditories'] !== null)
            for ($i = 0; $i < count($data['auditories']); $i++) {
                if (preg_match("/^\d+$/", $data['auditories'][$i]['name']))
                    $address = $data['auditories'][$i]['building']['name'] . ", ауд. " . $data['auditories'][$i]['name'];
                else
                    $address = $data['auditories'][$i]['building']['name'] . ", " . $data['auditories'][$i]['name'];
                array_push($this->place, $address);
            }
        if ($this->homework) {
            $json = json_decode($this->homework['text'], true);
            $this->homework_text = str_replace("\n","<br/>" ,escape_string($json['text']));
            $this->homework_files = $json['files'];
        }
    }

    function get_color() {
        switch ($this->type_name) {
            case "Практика":
                return "blue";
                break;
            case "Лабораторные":
                return "dark_blue";
                break;
            case "Курсовой проект":
                return "purple";
                break;
            case "Консультация":
            case "Консультации":
                return "orange";
                break;
            case "Экзамен":
            case "Доп. экзамен":
                return "red";
                break;
            case "Лекции":
            default:
                return "green";
                break;
        }
    }

    function template($editor = false) {
        include $_SERVER["DOCUMENT_ROOT"] . "/templates/calendar/calendar_lesson.php";
    }
}

function homework_restruct($homework) {
    if (!is_array($homework)) return false;
    $restructed_data = array();
    foreach ($homework as $row) {
        $date = $row['date'];
        $lesson = $row['lesson'];
        unset($row['date'], $row['lesson']);
        if (!isset($restructed_data[$date])) $restructed_data[$date] = array();
        $restructed_data[$date][$lesson] = $row;
    }
    return $restructed_data;
}

