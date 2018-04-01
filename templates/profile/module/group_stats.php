<div class="module" module="<?=$this->module_name;?>" group="<?=$this->chain;?>">
        <div class="dashboard">
            <div class="blockScheme">
                <div class="block">
                    <div class="title">Группа</div>
                    <div class="icon"><i class="icon circular inverted green calendar outline"></i></div>
                    <div class="content">
                        <p><?=$this->group_data['name'];?></p>
                    </div>
                </div>
                <div class="block">
                    <div class="title">Редакторов</div>
                    <div class="icon"><i class="icon circular inverted teal id card"></i></div>
                    <div class="content">
                        <p><?=$this->editors_count;?></p>
                    </div>
                </div>
                <div class="block">
                    <div class="title">Пользователей</div>
                    <div class="icon"><i class="icon circular inverted green users"></i></div>
                    <div class="content">
                        <p><?=$this->user_count;?></p>
                    </div>
                </div>
                <div class="block">
                    <div class="title">Активных ДЗ</div>
                    <div class="icon"><i class="icon circular inverted teal sticky note"></i></div>
                    <div class="content">
                        <p><?=$this->homework_count;?></p>
                    </div>
                </div>
            </div>
        </div>
</div>
