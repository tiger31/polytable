<div class="lesson" count="<?=$this->count;?>">
    <div class="header">
        <div class="lesson_title"><?=$this->name;?></div>
        <div class="lesson_type <?=$this->get_color();?>"><div class="type_right"></div><div class="type_center">- <?=$this->type_name;?> -</div><div class="type_left"></div></div>
        <div class="lesson_period"><?=$this->time_start;?>-<?=$this->time_end;?></div>
    </div>
    <?php if(count($this->teachers) > 0): ?>
        <div class="lesson_teacher"><? if(count($this->teachers) > 1):?>Ведут:<?php else:?>Ведет:<?php endif;?></div>
        <?php foreach($this->teachers as $teacher): ?>
            <div class="lesson_list_item"><?=$teacher;?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if(count($this->place) > 0): ?>
        <div class="lesson_place">Место: </div>
        <?php foreach($this->place as $place): ?>
            <div class="lesson_list_item"><?=$place;?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($this->homework): ?>
        <div class="lesson_homework">
            <div class="homework_text"><?=$this->homework_text;?></div>
            <?php if(count($this->homework_files) > 0): ?>
                <div class="homework_files_container">
                    <?php foreach ($this->homework_files as $file): ?>
                        <?php if($file['showable']): ?>
                            <img class="homework_file view" alt="<?=$file['original']?>" title="<?=$file['original']?>" src="uploads/thumbnails/<?=$file['date']?>/<?=$file['name']?>"/>
                        <?php else: ?>
                            <a href="uploads/files/<?=$file['date']?>/<?=$file['name']?>"><img class="homework_file" alt="<?=$file['original']?>" title="<?=$file['original']?>" src="assets/images/file.png"/></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div>
        <?php if ($editor): ?>
            <div class="lesson_edit">Редактировать</div>
        <?php endif; ?>
        <?php if ($this->homework): ?>
            <div class="lesson_homework_show">Показать ДЗ</div>
        <?php endif;?>
    </div>
</div>

