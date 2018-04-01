<div class="day <?=$this->get_class();?>" date="<?=$this->date;?>">
    <div class="content">
        <!-- <div class="triangle warning"></div> -->
        <span class="number <?php if ($this->week_day == 7) print ("weekend") ?>"><?=$this->day->format("d")?></span>
        <span class="weekDay"><?=$this->get_abbr()?></span>
        <?php if ($this->has_cache): ?>
            <div class="period"><?=$this->time_start;?>-<?=$this->time_end;?></div>
        <?php endif; ?>
    </div>
</div>
