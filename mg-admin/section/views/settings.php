
<div class="wrap">
  <div class="over_bg" >
    <div class="m-panel grid_5">
      <div class="panel-header" >
        <span class="m-setting-24">Настройки магазина</span>
      </div>
      <table id="table_settings">
      <?php foreach($data['options'] as $option):?>
      <tr>
      <td><?php echo $option['name'] ?></td>
      <td id="data">

        <?php if($option['option'] == 'templateName'): ?>
        <select class="option" name="<?php echo $option['option'] ?>" style="width:100%">
           <?php foreach($data['templates'] as $template):?>
             <option value="<?php echo $template ?>" <?php if($template == $option['value']){ echo "selected";} ?> ><?php echo $template ?></option>
           <?php endforeach;?>
        </select>
        <?php else:?>
        <input class="option" type="text" value="<?php echo $option['value'] ?>" name="<?php echo $option['option'] ?>"/>
        <?php endif;?>
      </td>

      <td><?php echo $option['desc'] ?></td>
     </tr>
     <?php endforeach;?>
      <tr class="pagination_box" style="height:60px;">
        <td colspan="3">
          <a href="#" rel="save_settings" class="button">Сохранить настройки</a>
        </td>
      <tr>
    </table>
    </div>
  </div>
</div>
