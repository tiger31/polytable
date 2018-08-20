<?php

namespace Configuration\Rights;


interface Accessor {
    public function have_access($cond);
}