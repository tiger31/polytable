<?php

namespace Configuration\Rights;


class AccessController {

    public function __construct() {
        try {
            $this->register_right(new Right(
                "HOMEWORK_EDIT", 0, RightsGroup::GROUP, [], [],true, "Редактирование ДЗ своей группы"
            ));
            $this->register_right(new Right(
                "HOMEWORK_ANY_EDIT", 1, RightsGroup::ADM, [0], [], false, "Редактирование ДЗ любой группы"
            ));
            $this->register_right(new Right(
                "LESSONS_EDIT", 2 ,RightsGroup::GROUP, [], [], true, "Редактирование лекций своей группы"
            ));
            $this->register_right(new Right(
                "LESSONS_ANY_EDIT", 3, RightsGroup::ADM, [2], [], false, "Редактирование лекций любой группы"
            ));
            $this->register_right(new Right(
                "NOTIFICATIONS_SELF", 4, RightsGroup::SELF, [], [], false, "Добавление личных заметок"
            ));
            $this->register_right(new Right(
                "NOTIFICATIONS_GROUP", 5, RightsGroup::GROUP, [], [], true, "Добавление заметок для группы (группа/день)"
            ));
            $this->register_right(new Right(
                "NOTIFICATIONS_ANY_GROUP", 6, RightsGroup::ADM, [5], [], false, "Добавление заметок для любой группы"
            ));
            $this->register_right(new Right(
                "NOTIFICATIONS_GLOBAL", 7, RightsGroup::MOD, [], [], false, "Добавление глобальных оповещений"
            ));
            $this->register_right(new Right(
                "HEADER_CONFIRM", 8, RightsGroup::MOD, [], [], false, "Подтверждение старост"
            ));
            $this->register_right(new Right(
                "RIGHTS_MANIPULATION", 9 , RightsGroup::GROUP, [], [], false, "Манипулирвоание (контроль) прав в соответсвие с разрешениями аккаунта"
            ));
            $this->register_right(new Right(
                "EDITORS_SET", 10, RightsGroup::MOD, [], [], false, "Назначение редакторов групп"
            ));
            $this->register_right(new Right(
                "CACHE_GROUP", 11, RightsGroup::GROUP, [], [], true, "Ручное обновление кэша группы"
            ));
            $this->register_right(new Right(
                "CACHE_ANY_GROUP", 12, RightsGroup::MOD, [11], [], false, "Ручное обновление кэша любой группы"
            ));
            $this->register_right(new Right(
                "CACHE_GLOBAL", 13, RightsGroup::ADM, [], [], false, "Ручной запуск полного обновления кэша"
            ));
            $this->register_right(new Right(
                "FILES_UPLOAD", 14, RightsGroup::GROUP, [], [], false, "Загрузка файлов на сервер"
            ));
            $this->register_right(new Right(
                "FILES_STATS", 15, RightsGroup::MOD, [], [], false, "Просмотр стастики сервера по хранимым файлам"
            ));
            $this->register_right(new Right(
                "STRUCTURE_GROUPS", 16, RightsGroup::ADM, [], [], false, "Обновление структуры групп в соответсвие с API"
            ));
            $this->register_right(new Right(
                "LOG_READING", 17, RightsGroup::ADM, [], [], false, "Чтение серверных логов"
            ));
        } catch (\Exception $e) {
            //TODO Logger;
        }
    }

    private function register_right($right) {
        if ($right instanceof Right) {
            if (isset(static::$rights_arr[$right->index])) {
                $index = $right->index;
                $first_right = static::$rights_arr[$right->index]->name;
                $second_right = $right->name;
                throw new \RuntimeException("You cannot bind second right ${second_right} for bit index ${index}. Already set right {$first_right}");
            }
            static::$rights_arr[$right->index] = $right;
            define($right->name, $right->index);
        }
    }

    static $rights_arr = array();
}