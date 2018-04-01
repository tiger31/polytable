<?
foreach (USER_MODULES as $key => $value) {
    $group = $key;
    $modules = array_filter($this->modules, function($m) use ($key) {
        return $m->menu === true && $m->menu_group === $key;
    });
    if (count($modules) > 0): ?>
    <div class="item toggle accordion">
        <i class="icon <?=$value['menu_icon'];?>"></i>
        <?=$value['menu_title'];?>
    </div>
    <div class="list">
        <?foreach ($modules as $module) { if ($module->is_active()) $module->template_menu();}?>
    </div>
    <? endif;
}
