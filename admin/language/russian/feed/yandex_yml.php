<?php
/**
 * Yandex.YML data feed for OpenCart (ocStore) 1.5.5.x
 *
 * Russian language file for admin form
 *
 * @author Alexander Toporkov <toporchillo@gmail.com>
 * @copyright (C) 2013- Alexander Toporkov
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Extended version of this module: http://opencartforum.ru/files/file/670-eksport-v-iandeksmarket/
 */
// Heading
$_['heading_title']      = 'Яндекс.Маркет';

// Text
$_['text_feed']          = 'Каналы продвижения';
$_['text_success']       = 'Настройки модуля обновлены!';

$_['extended_version']   = 'Расширенная версия модуля доступна <a href="http://opencartforum.ru/files/file/670-eksport-v-iandeksmarket/">здесь</a>';

// Entry
$_['entry_status']       = 'Статус:';
$_['entry_data_feed']    = 'Адрес экспорта:<br/><span class="help">Если вы формируете файл экспорта скриптом по запросу Яндекса.</span>';

$_['entry_shopname']     = 'Название магазина:<br/><span class="help">Короткое название магазина (название, которое выводится в списке найденных на Яндекс.Маркете товаров. Не должно содержать более 20 символов).</span>';
$_['entry_company']      = 'Компания:<br/><span class="help">Полное наименование компании, владеющей магазином. Не публикуется, используется для внутренней идентификации.</span>';

$_['entry_datamodel']    = 'Тип описания товарных предложений:<br/><span class="help">Если выбираете vendor.model, то у товаров обязательно должен быть задан производитель.</span>';
$_['entry_delivery_cost'] = 'local_delivery_cost:<br/><span class="help">Стоимость доставки для своего региона. Только цифры. Если тэг local_delivery_cost в экспорте не требуется, оставьте поле пустым.</span>';

$_['datamodels']    = array(
	'default'=>'упрощенный',
	'vendor.model'=>'vendor.model',
	/*
	'book'=>'book (книги)',
	'audiobook'=>'audiobook (аудиокниги)',
	'artist.title'=>'artist.title (аудио, видео)',
	'tour'=>'tour (туры)',
	'event-ticket'=>'event-ticket (билеты)'
	*/
);

$_['entry_category']     = 'Категории:<br/><span class="help">Отметьте категории из которых надо экспортировать предложения для Яндекс.Маркета</span><br/>Категории Яндекс<br/><span class="help">В правом поле ввода можно указать каким <a href="http://cards2.yandex.net/hlp-get/6213/xls/category_tree-.xls">категориям Яндекс</a> соответствуют ваши категории, например: "Одежда&nbsp;и&nbsp;обувь/Обувь/Мужская/Кеды"';
$_['entry_currency']     = 'Валюта предложений:<br/><span class="help">Яндекс.Маркет принимает предложения в валюте RUR, RUB или UAH. Выберите валюту в которой будут передаваться предложения.</span>';
$_['entry_unavailable']  = 'Весь товар &quot;Под заказ&quot;:<br/><span class="help">Если срок доставки товара больше двух дней, то товар должен экспортироваться как товар &quot;под заказ&quot;.</span>';
$_['entry_in_stock']     = 'Статус &quot;В наличии&quot;:<br/><span class="help">При наличии товара на складе <b>или</b> этом статусе, товар будет считаться &quot;в наличии&quot;, иначе - &quot;под заказ&quot;.</span>';
$_['entry_out_of_stock'] = 'Статус &quot;Нет в наличии&quot;:<br/><span class="help">При остатке на складе 0 <b>и</b> этом статусе, товар экспортироваться не будет.</span>';

$_['entry_pickup'] 		 = 'Самовывоз:<br/><span class="help">Можно ли забрать заказанный товар в пункте выдачи заказов.</span>';
$_['entry_pickup'] 		 = 'Самовывоз:<br/><span class="help">Можно ли забрать заказанный товар в пункте выдачи заказов.</span>';
$_['entry_sales_notes']  = 'sales_notes:<br/><span class="help">Если вы работаете по предоплате, то укажите: "Необходима предоплата".</span>';
$_['entry_store'] 		 = 'Точка продаж:<br/><span class="help">Есть ли точка продаж, где товар есть в наличии и его можно купить БЕЗ предварительного заказа.</span>';
$_['entry_numpictures']  = 'Кол-во картинок товара:<br/><span class="help">Сколько фотографий товара экспортировать. Яндекс принимает не более десяти.</span>';

$_['tab_attributes'] 	 = 'Атрибуты';
$_['tab_attributes_description'] = 'Яндекс рекомендует в экспорте разделять значение атрибута и единицу измерения.
									Для этого <a href="%attr_url%">отредактируйте названия атрибутов</a>, в скобках указывайте единцу измерения
									(если есть единица измерения), а в значении аттрибута единицу измерения не указывайте.
									Например &quot;Вес (кг): 10&quot;, но не &quot;Вес: 10кг&quot;.';
$_['entry_attributes'] 	 = 'Экспортруемые атрибуты:<br/><span class="help">Выбранные атрибуты, если они присутствуют у товара, будут экспортироваться в Яндекс.Маркет (в виде тэгов &lt;param&gt;).</span>';
$_['entry_adult'] 		 = 'Атрибут &quot;товар для взрослых&quot;:<br/><span class="help">При наличии у товара этого атрибута, товар будет экспортироваться как имеющий отношение к удолетворению сексуальных потребностей (с тэгом &lt;adult&gt;).</span>';
$_['entry_manufacturer_warranty'] = 'Атрибут, обозначающий официальную гарантию производителя:<br/><span class="help">При наличии у товара этого атрибута, товар будет экспортироваться с тэгом &lt;manufacturer_warranty&gt;true&lt;/manufacturer_warranty&gt;.</span>';
$_['entry_country_of_origin'] = 'Атрибут, обозначающий страну производства товара:<br/><span class="help">При наличии у товара этого атрибута, товар будет экспортироваться с тэгом &lt;country_of_origin&gt;).</span>';

// Error
$_['error_permission']   = 'У Вас нет прав для управления этим модулем!';
$_['error_no_color']   = 'Вы указали опцию, отвечающую за размер, но не указали опцию, отвечающую за цвет товара.';
$_['error_no_unit']   = 'Вы указали опцию, отвечающую за размер, но не указали единицу измерения размеров.';
?>