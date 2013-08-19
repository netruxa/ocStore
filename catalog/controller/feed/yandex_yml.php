<?php
/**
 * Yandex.YML data feed for OpenCart (ocStore) 1.5.5.x
 *
 * Model class to create YML
 *
 * @author Yesvik http://opencartforum.ru/user/6876-yesvik/
 * @author Alexander Toporkov <toporchillo@gmail.com>
 * @copyright (C) 2013- Alexander Toporkov
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Extended version of this module: http://opencartforum.ru/files/file/670-eksport-v-iandeksmarket/
 */
 
/**
 * Класс YML экспорта
 * YML (Yandex Market Language) - стандарт, разработанный "Яндексом"
 * для принятия и публикации информации в базе данных Яндекс.Маркет
 * YML основан на стандарте XML (Extensible Markup Language)
 * описание формата YML http://partner.market.yandex.ru/legal/tt/
 */
class ControllerFeedYandexYml extends Controller {
	private $shop = array();
	private $currencies = array();
	private $categories = array();
	private $offers = array();
	//private $from_charset = 'utf-8';
	private $eol = "\n";
	private $yml = '';
	
	private $color_options;
	private $size_options;
	private $size_units;
	private $optioned_name;

	public function index() {
		$this->generateYml();
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($this->getYml());
	}
	
	public function saveToFile($filename) {
		$this->generateYml();
		$fp = fopen($filename, 'w');
		$this->putYml($fp);
		fclose($fp);
	}
	
	private function generateYml() {
		if ($this->config->get('yandex_yml_status')) {

			$this->load->model('export/yandex_yml');
			$this->load->model('localisation/currency');
			$this->load->model('tool/image');

			// Магазин
			$this->setShop('name', $this->config->get('config_name'));
			//$this->setShop('name', $this->config->get('yandex_yml_shopname'));
			$this->setShop('company', $this->config->get('config_owner'));
			//$this->setShop('company', $this->config->get('yandex_yml_company'));
			$this->setShop('url', HTTP_SERVER);
			$this->setShop('phone', $this->config->get('config_telephone'));
			$this->setShop('platform', 'ocStore');
			$this->setShop('version', VERSION);

			// Валюты
			// TODO: Добавить возможность настраивать проценты в админке.
			$offers_currency = $this->config->get('yandex_yml_currency');
			if (!$this->currency->has($offers_currency)) exit();

			$decimal_place = $this->currency->getDecimalPlace($offers_currency);

			$shop_currency = $this->config->get('config_currency');

			$this->setCurrency($offers_currency, 1);

			$currencies = $this->model_localisation_currency->getCurrencies();

			$supported_currencies = array('RUR', 'RUB', 'USD', 'BYR', 'KZT', 'EUR', 'UAH');

			$currencies = array_intersect_key($currencies, array_flip($supported_currencies));

			foreach ($currencies as $currency) {
				if ($currency['code'] != $offers_currency && $currency['status'] == 1) {
					$this->setCurrency($currency['code'], number_format(1/$this->currency->convert($currency['value'], $offers_currency, $shop_currency), 4, '.', ''));
				}
			}
			//Тип данных vendor.model или default
			$datamodel = $this->config->get('yandex_yml_datamodel');
			
			// Категории
			$categories = $this->model_export_yandex_yml->getCategory();

			foreach ($categories as $category) {
				$this->setCategory($category['name'], $category['category_id'], $category['parent_id']);
			}

			// Товарные предложения
			$in_stock_id = $this->config->get('yandex_yml_in_stock'); // id статуса товара "В наличии"
			$out_of_stock_id = $this->config->get('yandex_yml_out_of_stock'); // id статуса товара "Нет на складе"
			$vendor_required = ($datamodel == 'vendor_model'); // true - только товары у которых задан производитель, необходимо для 'vendor.model' 

			$pickup = ($this->config->get('yandex_yml_pickup') ? 'true' : false);
			
			if ($this->config->get('yandex_yml_delivery_cost') != '') {
				$local_delivery_cost = intval($this->config->get('yandex_yml_delivery_cost'));
				$export_delivery_cost = true;
			}
			else {
				$export_delivery_cost = false;
			}
				
			$store = ($this->config->get('yandex_yml_store') ? 'true' : false);
			$unavailable = $this->config->get('yandex_yml_unavailable');

			$allowed_categories = $this->config->get('yandex_yml_categories');
			$blacklist = $this->config->get('yandex_yml_blacklist');
			$products = $this->model_export_yandex_yml->getProduct($allowed_categories, $blacklist, $out_of_stock_id, $vendor_required);

			$numpictures = $this->config->get('yandex_yml_numpictures');
			if ($numpictures > 1) {
				//++++ Дополнительные изображения товара ++++
				$product_images = $this->model_export_yandex_yml->getProductImages($numpictures - 1);
				//---- Дополнительные изображения товара ----
			}
			$all_attributes = $this->model_export_yandex_yml->getAttributes($this->config->get('yandex_yml_attributes'));
			$this->optioned_name = $this->config->get('yandex_yml_optioned_name');
			
			$yandex_yml_categ_mapping = unserialize($this->config->get('yandex_yml_categ_mapping'));
			
			$this->color_options = explode(',', $this->config->get('yandex_yml_color_options'));
			$this->size_options = explode(',', $this->config->get('yandex_yml_size_options'));
			$this->size_units = $this->config->get('yandex_yml_size_units') ? unserialize($this->config->get('yandex_yml_size_units')) : array();
			
			foreach ($products as $product) {
				$data = array();

				// Атрибуты товарного предложения
				$data['id'] = $product['product_id'];
				$data['type'] = $datamodel; //'vendor.model' или 'default';
				$data['available'] = (!$unavailable && ($product['quantity'] > 0 || $product['stock_status_id'] == $in_stock_id) ? 'true' : false);
//				$data['bid'] = 10;
//				$data['cbid'] = 15;

				// Параметры товарного предложения
				$data['url'] = $this->url->link('product/product', 'path=' . $this->getPath($product['category_id']) . '&product_id=' . $product['product_id']);
				$data['price'] = $product['price'];
				if ($data['price'] == 0) continue;
				$data['currencyId'] = $offers_currency;
				$data['categoryId'] = $product['category_id'];
				if (isset($yandex_yml_categ_mapping[$product['category_id']]) && $yandex_yml_categ_mapping[$product['category_id']]) {
					$data['market_category'] = $yandex_yml_categ_mapping[$product['category_id']];
				}
				$data['delivery'] = 'true';
				if ($export_delivery_cost) {
					$data['local_delivery_cost'] = $local_delivery_cost;
				}
				if ($pickup)
					$data['pickup'] = $pickup;
				if ($store)
					$data['store'] = $store;

				$data['name'] = $product['name'];
				$data['vendor'] = $product['manufacturer'];
				$data['vendorCode'] = $product['model'];
				$data['model'] = $product['name'];
				$data['description'] = $product['description'];
				$sales_notes = $this->config->get('yandex_yml_sales_notes');
				if ($sales_notes) {
					$data['sales_notes'] = $sales_notes;
				}
//				$data['manufacturer_warranty'] = 'true';
//				$data['barcode'] = $product['sku'];
				if ($numpictures > 0) {
					if ($product['image'] && is_file(DIR_IMAGE . $product['image'])) {
						$data['picture'] = array($this->model_tool_image->resize($product['image'], 600, 600));
					}
					//++++ Дополнительные изображения товара ++++
					if (isset($product_images[$product['product_id']])) {
						if (!isset($data['picture']) || !is_array($data['picture'])) {
							$data['picture'] = array();
						}
						foreach ($product_images[$product['product_id']] as $image) {
							if (!is_file(DIR_IMAGE . $image)) continue;
							$data['picture'][] = $this->model_tool_image->resize($image, 600, 600);
						}
					}
					//---- Дополнительные изображения товара ----
				}

				/*++++ Атрибуты товара ++++
				// пример структуры массива для вывода параметров
				$data['param'] = array(
					array(
						'name'=>'Wi-Fi',
						'value'=>'есть'
					), array(
						'name'=>'Размер экрана',
						'unit'=>'дюйм',
						'value'=>'20'
					), array(
						'name'=>'Вес',
						'unit'=>'кг',
						'value'=>'4.6'
					)
				);
				*/
				$data['param'] = array();
				$attributes = $this->model_export_yandex_yml->getProductAttributes($product['product_id']);
				if (count($attributes) > 0) {
					foreach ($attributes as $attr) {
						if ($attr['attribute_id'] == $this->config->get('yandex_yml_adult')) {
							$data['adult'] = 'true';
						}
						elseif ($attr['attribute_id'] == $this->config->get('yandex_yml_manufacturer_warranty')) {
							$data['manufacturer_warranty'] = 'true';
						}
						elseif ($attr['attribute_id'] == $this->config->get('yandex_yml_country_of_origin')) {
							$data['country_of_origin'] = $attr['text'];
						}
						elseif (isset($all_attributes[$attr['attribute_id']])) {
							$data['param'][] = $this->detectUnits(array(
								'name' => $all_attributes[$attr['attribute_id']],
								'value' => $attr['text']));
						}
					}
				}
				//---- Атрибуты товара ----

				if (!$this->setOptionedOffer($data, $product, $shop_currency, $offers_currency, $decimal_place)) {
					$data['price'] = number_format($this->currency->convert($this->tax->calculate($data['price'], $product['tax_class_id']), $shop_currency, $offers_currency), $decimal_place, '.', '');
					if ($data['price'] > 0) {
						$this->setOffer($data);
					}
				}
			}

			$this->categories = array_filter($this->categories, array($this, "filterCategory"));

			return true;
		}
		return false;
	}

	/**
	 * Создает много элементов offer товарных предложений для разных опций цвет и размер товара
	 */
	protected function setOptionedOffer($data, $product, $shop_currency, $offers_currency, $decimal_place) {
		if (!$this->color_options)
			return false;
		$offers_array = array();
		$coptions = $this->model_export_yandex_yml->getProductOptions($this->color_options, $product['product_id']);
		if (!count($coptions))
			return false;
		//++++ Цвета x Размеры для магазинов одежды ++++
		foreach ($coptions as $option) {
			$data_arr = $data;
			if (($this->optioned_name == 'short') || ($this->optioned_name == 'long')) {
				$data_arr['name'].= ', цвет '.$option['name'];
			}
			$data_arr['param'][] = array('name'=>'Цвет', 'value'=>$option['name']);
			$data_arr['group_id'] = $product['product_id'];
			$data_arr['available'] = $data_arr['available'] && ($option['quantity'] > 0);
			if ($option['price_prefix'] == '+')
				$data_arr['price']+= $option['price'];
			elseif ($option['price_prefix'] == '-')
				$data_arr['price']-= $option['price'];
			$offers_array[] = $data_arr;
		}
		// Размеры
		$soptions = array();
		if ($this->size_options)
			$soptions = $this->model_export_yandex_yml->getProductOptions($this->size_options, $product['product_id']);
		$idx = 0;
		foreach ($offers_array as $i=>$data) {
			if (count($soptions)) {
				foreach ($soptions as $option) {
					$size_option_name = $option['option_name'];
					$size_option_unit = $this->size_units[$option['option_id']];
					$data_arr = $data;
					if ($this->optioned_name == 'long') {
						$data_arr['name'].= ', '.$size_option_name.' '.$option['name'];
					}
					$size_param = array('name'=>$size_option_name, 'value'=>$option['name']);
					if ($size_option_unit) {
						$size_param['unit'] = $size_option_unit;
					} 
					$data_arr['param'][] = $size_param;
					$data_arr['available'] = $data_arr['available'] && ($option['quantity'] > 0);
					if ($option['price_prefix'] == '+')
						$data_arr['price'] = $data_arr['price'] + $option['price'];
					elseif ($option['price_prefix'] == '-')
						$data_arr['price']-= $option['price'];
					$offers_array[] = $data_arr;

					$data_arr['id'] = $data['group_id'].str_pad($idx, 4, '0', STR_PAD_LEFT);
					$data_arr['price'] = number_format($this->currency->convert($this->tax->calculate($data_arr['price'], $product['tax_class_id']), $shop_currency, $offers_currency), $decimal_place, '.', '');
					$this->setOffer($data_arr);
					$idx++;
				}
			}
			else {
				$data['id'] = $data['group_id'].str_pad($i, 4, '0', STR_PAD_LEFT);
				$data['price'] = number_format($this->currency->convert($this->tax->calculate($data['price'], $product['tax_class_id']), $shop_currency, $offers_currency), $decimal_place, '.', '');
				if ($data['price'] > 0) {
					$this->setOffer($data);
				}
			}
		}
		return true;
		//---- Цвета x Размеры для магазинов одежды ----
	}

	/**
	 * Методы формирования YML
	 */

	/**
	 * Формирование массива для элемента shop описывающего магазин
	 *
	 * @param string $name - Название элемента
	 * @param string $value - Значение элемента
	 */
	private function setShop($name, $value) {
		$allowed = array('name', 'company', 'url', 'phone', 'platform', 'version', 'agency', 'email');
		if (in_array($name, $allowed)) {
			$this->shop[$name] = $this->prepareField($value);
		}
	}

	/**
	 * Валюты
	 *
	 * @param string $id - код валюты (RUR, RUB, USD, BYR, KZT, EUR, UAH)
	 * @param float|string $rate - курс этой валюты к валюте, взятой за единицу.
	 *	Параметр rate может иметь так же следующие значения:
	 *		CBRF - курс по Центральному банку РФ.
	 *		NBU - курс по Национальному банку Украины.
	 *		NBK - курс по Национальному банку Казахстана.
	 *		СВ - курс по банку той страны, к которой относится интернет-магазин
	 * 		по Своему региону, указанному в Партнерском интерфейсе Яндекс.Маркета.
	 * @param float $plus - используется только в случае rate = CBRF, NBU, NBK или СВ
	 *		и означает на сколько увеличить курс в процентах от курса выбранного банка
	 * @return bool
	 */
	private function setCurrency($id, $rate = 'CBRF', $plus = 0) {
		$allow_id = array('RUR', 'RUB', 'USD', 'BYR', 'KZT', 'EUR', 'UAH');
		if (!in_array($id, $allow_id)) {
			return false;
		}
		$allow_rate = array('CBRF', 'NBU', 'NBK', 'CB');
		if (in_array($rate, $allow_rate)) {
			$plus = str_replace(',', '.', $plus);
			if (is_numeric($plus) && $plus > 0) {
				$this->currencies[] = array(
					'id'=>$this->prepareField(strtoupper($id)),
					'rate'=>$rate,
					'plus'=>(float)$plus
				);
			} else {
				$this->currencies[] = array(
					'id'=>$this->prepareField(strtoupper($id)),
					'rate'=>$rate
				);
			}
		} else {
			$rate = str_replace(',', '.', $rate);
			if (!(is_numeric($rate) && $rate > 0)) {
				return false;
			}
			$this->currencies[] = array(
				'id'=>$this->prepareField(strtoupper($id)),
				'rate'=>(float)$rate
			);
		}

		return true;
	}

	/**
	 * Категории товаров
	 *
	 * @param string $name - название рубрики
	 * @param int $id - id рубрики
	 * @param int $parent_id - id родительской рубрики
	 * @return bool
	 */
	private function setCategory($name, $id, $parent_id = 0) {
		$id = (int)$id;
		if ($id < 1 || trim($name) == '') {
			return false;
		}
		if ((int)$parent_id > 0) {
			$this->categories[$id] = array(
				'id'=>$id,
				'parentId'=>(int)$parent_id,
				'name'=>$this->prepareField($name)
			);
		} else {
			$this->categories[$id] = array(
				'id'=>$id,
				'name'=>$this->prepareField($name)
			);
		}

		return true;
	}

	/**
	 * Товарные предложения
	 *
	 * @param array $data - массив параметров товарного предложения
	 */
	private function setOffer($data) {
		$offer = array();

		$attributes = array('id', 'type', 'available', 'bid', 'cbid', 'param', 'group_id');
		$attributes = array_intersect_key($data, array_flip($attributes));

		foreach ($attributes as $key => $value) {
			switch ($key)
			{
				case 'id':
				case 'bid':
				case 'cbid':
				case 'group_id':
					$value = (int)$value;
					if ($value > 0) {
						$offer[$key] = $value;
					}
					break;

				case 'type':
					if (in_array($value, array('vendor.model', 'book', 'audiobook', 'artist.title', 'tour', 'ticket', 'event-ticket'))) {
						$offer['type'] = $value;
					}
					break;

				case 'available':
					$offer['available'] = ($value ? 'true' : 'false');
					break;

				case 'param':
					if (is_array($value)) {
						$offer['param'] = $value;
					}
					break;

				default:
					break;
			}
		}

		$type = isset($offer['type']) ? $offer['type'] : '';

		$allowed_tags = array('url'=>0, 'buyurl'=>0, 'price'=>1, 'wprice'=>0, 'currencyId'=>1, 'xCategory'=>0, 'categoryId'=>1, 'market_category'=>0, 'picture'=>0, 'store'=>0, 'pickup'=>0, 'delivery'=>0, 'deliveryIncluded'=>0, 'local_delivery_cost'=>0, 'orderingTime'=>0);

		switch ($type) {
			case 'vendor.model':
				$allowed_tags = array_merge($allowed_tags, array('typePrefix'=>0, 'vendor'=>1, 'vendorCode'=>0, 'model'=>1, 'provider'=>0, 'tarifplan'=>0));
				break;

			case 'book':
				$allowed_tags = array_merge($allowed_tags, array('author'=>0, 'name'=>1, 'publisher'=>0, 'series'=>0, 'year'=>0, 'ISBN'=>0, 'volume'=>0, 'part'=>0, 'language'=>0, 'binding'=>0, 'page_extent'=>0, 'table_of_contents'=>0));
				break;

			case 'audiobook':
				$allowed_tags = array_merge($allowed_tags, array('author'=>0, 'name'=>1, 'publisher'=>0, 'series'=>0, 'year'=>0, 'ISBN'=>0, 'volume'=>0, 'part'=>0, 'language'=>0, 'table_of_contents'=>0, 'performed_by'=>0, 'performance_type'=>0, 'storage'=>0, 'format'=>0, 'recording_length'=>0));
				break;

			case 'artist.title':
				$allowed_tags = array_merge($allowed_tags, array('artist'=>0, 'title'=>1, 'year'=>0, 'media'=>0, 'starring'=>0, 'director'=>0, 'originalName'=>0, 'country'=>0));
				break;

			case 'tour':
				$allowed_tags = array_merge($allowed_tags, array('worldRegion'=>0, 'country'=>0, 'region'=>0, 'days'=>1, 'dataTour'=>0, 'name'=>1, 'hotel_stars'=>0, 'room'=>0, 'meal'=>0, 'included'=>1, 'transport'=>1, 'price_min'=>0, 'price_max'=>0, 'options'=>0));
				break;

			case 'event-ticket':
				$allowed_tags = array_merge($allowed_tags, array('name'=>1, 'place'=>1, 'hall'=>0, 'hall_part'=>0, 'date'=>1, 'is_premiere'=>0, 'is_kids'=>0));
				break;

			default:
				$allowed_tags = array_merge($allowed_tags, array('name'=>1, 'vendor'=>0, 'vendorCode'=>0));
				break;
		}

		$allowed_tags = array_merge($allowed_tags, array('aliases'=>0, 'additional'=>0, 'description'=>0, 'sales_notes'=>0, 'promo'=>0, 'manufacturer_warranty'=>0, 'country_of_origin'=>0, 'downloadable'=>0, 'adult'=>0, 'barcode'=>0));

		$required_tags = array_filter($allowed_tags);

		if (sizeof(array_intersect_key($data, $required_tags)) != sizeof($required_tags)) {
			return;
		}

		$data = array_intersect_key($data, $allowed_tags);
//		if (isset($data['tarifplan']) && !isset($data['provider'])) {
//			unset($data['tarifplan']);
//		}

		$allowed_tags = array_intersect_key($allowed_tags, $data);

		// Стандарт XML учитывает порядок следования элементов,
		// поэтому важно соблюдать его в соответствии с порядком описанным в DTD
		$offer['data'] = array();
		foreach ($allowed_tags as $key => $value) {
			if (!isset($data[$key]))
				continue;
			if (is_array($data[$key])) {
				foreach ($data[$key] as $i => $val) {
					$offer['data'][$key][$i] = $this->prepareField($val);
				}
			}
			else {
				$offer['data'][$key] = $this->prepareField($data[$key]);
			}
		}

		$this->offers[] = $offer;
	}

	/**
	 * Формирование YML файла
	 *
	 * @return string
	 */
	private function getYml() {
		$yml  = '<?xml version="1.0" encoding="UTF-8"?>' . $this->eol;
		$yml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . $this->eol;
		$yml .= '<yml_catalog date="' . date('Y-m-d H:i') . '">' . $this->eol;
		$yml .= '<shop>' . $this->eol;

		// информация о магазине
		$yml .= $this->array2Tag($this->shop);

		// валюты
		$yml .= '<currencies>' . $this->eol;
		foreach ($this->currencies as $currency) {
			$yml .= $this->getElement($currency, 'currency');
		}
		$yml .= '</currencies>' . $this->eol;

		// категории
		$yml .= '<categories>' . $this->eol;
		foreach ($this->categories as $category) {
			$category_name = $category['name'];
			unset($category['name'], $category['export']);
			$yml .= $this->getElement($category, 'category', $category_name);
		}
		$yml .= '</categories>' . $this->eol;

		// товарные предложения
		$yml .= '<offers>' . $this->eol;
		foreach ($this->offers as $offer) {
			$tags = $this->array2Tag($offer['data']);
			unset($offer['data']);
			if (isset($offer['param'])) {
				$tags .= $this->array2Param($offer['param']);
				unset($offer['param']);
			}
			$yml .= $this->getElement($offer, 'offer', $tags);
		}
		$yml .= '</offers>' . $this->eol;

		$yml .= '</shop>';
		$yml .= '</yml_catalog>';

		return $yml;
	}

	/**
	 * Вывод YML в файл
	 * @param $fp дескриптор файла
	 */
	private function putYml($fp) {
		fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . $this->eol
			.'<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . $this->eol
			.'<yml_catalog date="' . date('Y-m-d H:i') . '">' . $this->eol
			.'<shop>' . $this->eol);

		// информация о магазине
		fwrite($fp, $this->array2Tag($this->shop));

		// валюты
		fwrite($fp, '<currencies>' . $this->eol);
		foreach ($this->currencies as $currency) {
			fwrite($fp, $this->getElement($currency, 'currency'));
		}
		fwrite($fp, '</currencies>' . $this->eol
		// категории
			.'<categories>' . $this->eol);
		foreach ($this->categories as $category) {
			$category_name = $category['name'];
			unset($category['name'], $category['export']);
			fwrite($fp, $this->getElement($category, 'category', $category_name));
		}
		fwrite($fp, '</categories>' . $this->eol
		// товарные предложения
			.'<offers>' . $this->eol);
		foreach ($this->offers as $offer) {
			$tags = $this->array2Tag($offer['data']);
			unset($offer['data']);
			if (isset($offer['param'])) {
				$tags .= $this->array2Param($offer['param']);
				unset($offer['param']);
			}
			fwrite($fp, $this->getElement($offer, 'offer', $tags));
		}
		fwrite($fp, '</offers>' . $this->eol
			.'</shop>'
			.'</yml_catalog>');
		return true;
	}


	/**
	 * Фрмирование элемента
	 *
	 * @param array $attributes
	 * @param string $element_name
	 * @param string $element_value
	 * @return string
	 */
	private function getElement($attributes, $element_name, $element_value = '') {
		$retval = '<' . $element_name . ' ';
		foreach ($attributes as $key => $value) {
			$retval .= $key . '="' . $value . '" ';
		}
		$retval .= $element_value ? '>' . $this->eol . $element_value . '</' . $element_name . '>' : '/>';
		$retval .= $this->eol;

		return $retval;
	}

	/**
	 * Преобразование массива в теги
	 *
	 * @param array $tags
	 * @return string
	 */
	private function array2Tag($tags) {
		$retval = '';
		foreach ($tags as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $val) {
					$retval .= '<' . $key . '>' . $val . '</' . $key . '>' . $this->eol;
				}
			}
			else {
				$retval .= '<' . $key . '>' . $value . '</' . $key . '>' . $this->eol;
			}
		}

		return $retval;
	}

	/**
	 * Преобразование массива в теги параметров
	 *
	 * @param array $params
	 * @return string
	 */
	private function array2Param($params) {
		$retval = '';
		foreach ($params as $param) {
			$retval .= '<param name="' . $this->prepareField($param['name']);
			if (isset($param['unit'])) {
				$retval .= '" unit="' . $this->prepareField($param['unit']);
			}
			$retval .= '">' . $this->prepareField($param['value']) . '</param>' . $this->eol;
		}

		return $retval;
	}

	/**
	 * Подготовка текстового поля в соответствии с требованиями Яндекса
	 * Запрещаем любые html-тэги, стандарт XML не допускает использования в текстовых данных
	 * непечатаемых символов с ASCII-кодами в диапазоне значений от 0 до 31 (за исключением
	 * символов с кодами 9, 10, 13 - табуляция, перевод строки, возврат каретки). Также этот
	 * стандарт требует обязательной замены некоторых символов на их символьные примитивы.
	 * @param string $text
	 * @return string
	 */
	private function prepareField($field) {
		$field = htmlspecialchars_decode($field);
		$field = strip_tags($field);
		$from = array('"', '&', '>', '<', '\'');
		$to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;');
		$field = str_replace($from, $to, $field);
		/**
		if ($this->from_charset != 'windows-1251') {
			$field = iconv($this->from_charset, 'windows-1251//IGNORE', $field);
		}
		**/
		$field = preg_replace('#[\x00-\x08\x0B-\x0C\x0E-\x1F]+#is', ' ', $field);

		return trim($field);
	}

	protected function getPath($category_id, $current_path = '') {
		if (isset($this->categories[$category_id])) {
			$this->categories[$category_id]['export'] = 1;

			if (!$current_path) {
				$new_path = $this->categories[$category_id]['id'];
			} else {
				$new_path = $this->categories[$category_id]['id'] . '_' . $current_path;
			}	

			if (isset($this->categories[$category_id]['parentId'])) {
				return $this->getPath($this->categories[$category_id]['parentId'], $new_path);
			} else {
				return $new_path;
			}

		}
	}

	function filterCategory($category) {
		return isset($category['export']);
	}
	
	/**
	 * Определение единиц измерения по содержимому
	 *
	 * @param array $attr array('name'=>'Вес', 'value'=>'100кг')
	 * @return array array('name'=>'Вес', 'unit'=>'кг', 'value'=>'100')
	 */
	protected function detectUnits($attr) {
		//$matches = array();
		$attr['name'] = trim(strip_tags($attr['name']));
		$attr['value'] = trim(strip_tags($attr['value']));
		if (preg_match('/\(([^\)]+)\)$/mi', $attr['name'], $matches)) {
			$attr['name'] = trim(str_replace('('.$matches[1].')', '', $attr['name']));
			$attr['unit'] = trim($matches[1]);
		}
		return $attr;
	}
}
?>