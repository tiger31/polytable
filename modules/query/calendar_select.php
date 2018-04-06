<?php

class calendar_select extends db_query {
    function prepare(PDO $mysql) {
        $this->is_multiple = true;
        $select_all = array(
            "keys" => array("group_id"),
            "query" => $mysql->prepare("SELECT calendar.*, homeworks.text->>\"$.text\" AS `text`, CONCAT(
		\"[\",
		CASE WHEN JSON_LENGTH(homeworks.text->>\"$.files\") > 0 THEN 
			GROUP_CONCAT(
				JSON_OBJECT(
					\"name\", uploads.name, 
					\"original\", uploads.original_name, 
					\"showable\", uploads.showable, 
					\"size\", uploads.size)
				)
		END,	
		\"]\") AS `files`
FROM calendar 
LEFT JOIN homeworks ON calendar.group_id = homeworks.group_id AND calendar.day = homeworks.date AND calendar.lesson = homeworks.lesson
LEFT JOIN uploads
ON JSON_CONTAINS(homeworks.text->\"$.files[*]\", CONCAT('\"', uploads.name, '\"'), \"$\")
WHERE calendar.group_id = :group_id
GROUP BY calendar.group_id, calendar.day, calendar.lesson
ORDER BY calendar.day, calendar.lesson ASC ")
        );
        array_push($this->multiple, $select_all);
        $this->type = db_query::SELECT;
    }
}