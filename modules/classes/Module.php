<?php

class Module {
    protected $active;
    public $module;
    public $menu;
    public $grid;
    public $module_name;
    public $menu_group;
    public $chain;
    public $config;
    function __construct($owner, $config) {}
    function is_active() {
        return $this->active;
    }
    function config_load_default() {
        $this->module = (isset($this->config['module'])) ? $this->config['module'] : false;
        $this->menu = (isset($this->config['menu'])) ? $this->config['menu'] : false;
        $this->grid = (isset($this->config['grid'])) ? $this->config['grid'] : false;
        $this->module_name = $this->config['module_name'];
        $this->menu_group = ($this->menu) ? $this->config['menu_group'] : false;
        $this->chain = isset($this->config['chain']) ? $this->config['chain'] : false;
    }
    function template() {
        if ($this->module)
        include_once $_SERVER['DOCUMENT_ROOT'] . "/templates/profile/module/" . $this->module_name . ".php";
    }
    function template_grid() {
        if ($this->grid)
        include_once $_SERVER['DOCUMENT_ROOT'] . "/templates/profile/grid/" . $this->module_name . ".php";
    }
    function template_menu() {
        if ($this->menu)
        include_once $_SERVER['DOCUMENT_ROOT'] . "/templates/profile/menu/" . $this->module_name . ".php";
    }
}