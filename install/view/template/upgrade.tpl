<?php echo $header; ?>
<h1>Обновление базы данных</h1>
<div id="column-right">
  <ul>
    <li><b>Обновление</b></li>
    <li>Окончание</li>
  </ul>
</div>
<div id="content">
    <?php if ($error_warning) { ?>
  <div class="alert alert-error"><i class="icon-exclamation-sign"></i> <?php echo $error_warning; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
      <fieldset>
        <p><b>Внимательно прочтите и выполните следующие действия!</b></p>
	  <ol>
	    <li>О любых ошибках и проблемах при обновлении сообщите на форуме</li>
		<li>После обновления, удалите все куки в своем браузере, чтобы избежать ошибок с токенами.</li>
		<li>Перейдите в Административную панель и дважды нажмите Ctrl+F5 для обновления кешированных CSS стилей.</li>
		<li>Перейдите в разделе Система->Пользователи->Группы пользователей к редактированию группы Главный администратор и отметьте все чекбоксы.</li>
		<li>Перейдите в разделе Система->Настройки к редактированию настроек магазина. Проверьте все значения настроек и нажмите кнопку Сохранить даже если ничего не меняли.</li>
		<li>Перейдите в Публичную часть и дважды нажмите Ctrl+F5 для обновления кешированных CSS стилей.</li>
	  </ol>
      </fieldset>
      <div class="buttons">
	  <div class="right">
        <input type="submit" value="Продолжить" class="btn" />
          </div>
      </div>
    </form>
</div>
<?php echo $footer; ?>

