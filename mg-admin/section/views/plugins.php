 <div class="wrap">
  <div class="over_bg" >
    <div class="m-panel grid_5">
      <div class="panel-header" >
        <span class="m-order-24">Плагины</span>
      </div>
        <table id="table_plugins" class="product_price" border="0">
          <tr>
            <th>Название</th>
            <th>Описание</th>
            <th>Активность</th>

          </tr>

          <?php
          $i=0;
          foreach($plugins as $pluginInfo){ ?>

          <tr id="<?php echo $pluginInfo['folderName']; ?>"
                 <?php if(!$pluginInfo['Active']):?> class="noactive"
                 <?php else:?>
                 <?php if(++$i %2 == 0):?> class="odd"<?php endif;?>
                 <?php endif;?>
                 >
            <td style="width: 300px;">
              <div style="width: 300px;">

                 <div style="float:left; width: 70px;">
                    <?php /*Вывод иконки для настройки плагина*/ ?>

                    <?php
                      $display = (!$pluginInfo['Active'])?'none':'block';
                    ?>
                    <?php
                    $class = 'plugin-settings-off';
                    $title = "Плагин не имеет страницу настроек";
                    if(PM::isHookInReg($pluginInfo['folderName'])){
                      $class = 'plugin-settings-on';
                      $title = "Настроить плагин";
                    }?>
                    <a href="#" title="<?php echo $title ?>" rel="openPagePlugin" pluginFolder="<?php echo $pluginInfo['folderName']?>" class="<?php echo $class?>" style="display: <?php echo $display?>">

                    </a>

                  </div>

                  <div style="float:left; width: 230px;">
                     <?php /*Информация о плагине*/ ?>

                     <div style="color:black; font-weight: 600; margin: 0px;"><?php echo $pluginInfo['PluginName']; ?></div>

                     Версия <?php echo $pluginInfo['Version']; ?>
                     <br/>
                     Автор: <?php echo $pluginInfo['Author']; ?>

                  </div>

              </div>
            </td>
            <td>
             <?php echo str_replace('[', '&#091;',$pluginInfo['Description']) ?></td>
            <td id="activate">
              <?php
              $class='on-switch';
              $rel='off';
              if (!$pluginInfo['Active']){
                $class='off-switch';
                $rel='on';
              }
              ?>
              <a href="#" rel="<?php echo $rel?>" pluginFolder="<?php echo $pluginInfo['folderName']?>" class="<?php echo $class?>"></a>
            </td>
          </tr>
        <?php } ?>
        </table>

   </div>
  </div>
</div>
