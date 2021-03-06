<?php
// Heading
$_['heading_title']      = 'Klarna Invoice';

// Text
$_['text_payment']        = 'Оплата';
$_['text_success']        = 'Настройки модуля обновлены!';
$_['text_klarna_invoice'] = '<a onclick="window.open(\'http://www.klarna.com\');"><img src="view/image/payment/klarna.png" alt="Klarna" title="Klarna" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_live']           = 'Live';
$_['text_beta']           = 'Beta';
$_['text_sweden']           = 'Sweden';
$_['text_norway']           = 'Norway';
$_['text_finland']          = 'Finland';
$_['text_denmark']          = 'Denmark';
$_['text_germany']          = 'Germany';
$_['text_netherlands']      = 'The Netherlands';

// Entry
$_['entry_merchant']      = 'Klarna Merchant ID:<br /><span class="help">(estore id) для использования сервиса (предоставляется Klarna).</span>';
$_['entry_secret']        = 'Klarna Secret:<br /><span class="help">Секретный ключ для использования сервиса (предоставляется Klarna).</span>';
$_['entry_server']        = 'Сервер:';
$_['entry_total']        = 'Минимальная сумма заказа:<br /><span class="help">Сумма заказа, после достижения которой данный способ станет доступен.</span>';
$_['entry_pending_status']  = 'Pending Status:';
$_['entry_accepted_status'] = 'Accepted Status:';
$_['entry_order_status'] = 'Статус заказа:';
$_['entry_geo_zone']     = 'Географическая зона:';
$_['entry_status']       = 'Статус:';
$_['entry_sort_order']	 = 'Порядок сортировки:';

// Help
$_['help_merchant']         = '(estore id) to use for the Klarna service (provided by Klarna).';
$_['help_secret']           = 'Shared secret to use with the Klarna service (provided by Klarna).';
$_['help_total']            = 'The checkout total the order must reach before this payment method becomes active.';

// Error
$_['error_permission']    = 'У Вас нет прав для управления этим модулем!';
$_['error_xmlrpc']        = 'Для работы требуется PHP расширение XML-RPC!';
$_['error_merchant']      = 'Отсутствует Klarna Merchant ID!';
$_['error_secret']        = 'Отсутствует Klarna Secret!';
?>
