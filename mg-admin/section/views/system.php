<?php
?>
<div class="wrap">
  <div class="over_bg" >
    <div class="m-panel grid_5">
      <div class="panel-header" >
        <span class="m-system-24">Система</span>
      </div>
      <div class="panel-body" style="background: #f2f2f2;">
        <div class="panel-content txtSpan">
          <label><input type="checkbox" name="downtime" <?php echo $checked ?>> Закрыть сайт для профилактики</label>
          <hr>
          <div id="loader"></div>
          <span>Текущая версия системы</span>  <span style="color:green"><?php echo VER?>. </span>
          <br><?php echo $newVersionMsg ?>
        </div>
      </div>
    </div>
  </div>
</div>