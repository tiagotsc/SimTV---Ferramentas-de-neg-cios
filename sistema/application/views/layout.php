<?php
    $menu = (!empty($menu))? $menu: '';
    $menu_lateral = (!empty($menu_lateral))? $menu_lateral: '';
    $corpo = (!empty($corpo))? $corpo: '';
    $rodape = (!empty($rodape))? $rodape: '';
    $html_footer = (!empty($html_footer))? $html_footer: '';
?>
<?php echo $html_header; ?>
<?php echo $menu; ?>
<?php echo $menu_lateral; ?>
<?php echo $corpo; ?>
<?php echo $rodape; ?>
<?php echo $html_footer; ?>