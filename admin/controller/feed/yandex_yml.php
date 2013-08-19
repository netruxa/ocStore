<?php
/**
 * Yandex.YML data feed for OpenCart (ocStore) 1.5.5.x
 *
 * Controller of admin form
 *
 * @author Alexander Toporkov <toporchillo@gmail.com>
 * @copyright (C) 2013- Alexander Toporkov
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Extended version of this module: http://opencartforum.ru/files/file/670-eksport-v-iandeksmarket/
 */
class ControllerFeedYandexYml extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('feed/yandex_yml');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate($this->request->post))) {
			if (isset($this->request->post['yandex_yml_categories'])) {
				$this->request->post['yandex_yml_categories'] = implode(',', $this->request->post['yandex_yml_categories']);
			}
			if (isset($this->request->post['yandex_yml_attributes'])) {
				$this->request->post['yandex_yml_attributes'] = implode(',', $this->request->post['yandex_yml_attributes']);
			}

			$this->model_setting_setting->editSetting('yandex_yml', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_attributes'] = $this->language->get('tab_attributes');
		$this->data['tab_tailor'] = $this->language->get('tab_tailor');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_select_all'] = $this->language->get('text_select_all');
		$this->data['text_unselect_all'] = $this->language->get('text_unselect_all');

		$this->data['extended_version'] = $this->language->get('extended_version');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_data_feed'] = $this->language->get('entry_data_feed');
		$this->data['entry_shopname'] = $this->language->get('entry_shopname');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_datamodel'] = $this->language->get('entry_datamodel');
		$this->data['datamodels'] = $this->language->get('datamodels');
		$this->data['entry_delivery_cost'] = $this->language->get('entry_delivery_cost');
		
		$this->data['entry_category'] = $this->language->get('entry_category');
		$this->data['entry_currency'] = $this->language->get('entry_currency');
		$this->data['entry_unavailable'] = $this->language->get('entry_unavailable');
		$this->data['entry_in_stock'] = $this->language->get('entry_in_stock');
		$this->data['entry_out_of_stock'] = $this->language->get('entry_out_of_stock');

		$this->data['entry_pickup'] = $this->language->get('entry_pickup');
		$this->data['entry_sales_notes'] = $this->language->get('entry_sales_notes');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_numpictures'] = $this->language->get('entry_numpictures');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		
		$this->data['entry_cron_run'] = $this->language->get('entry_cron_run');
		$this->data['cron_path'] = 'php '.realpath(DIR_CATALOG.'../export/yandex_yml.php');
		$this->data['entry_export_url'] = $this->language->get('entry_export_url');
		$this->data['export_url'] = HTTP_CATALOG.'export/yandex_yml.xml';

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),
			'text'      => $this->language->get('text_feed'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('feed/yandex_yml', 'token=' . $this->session->data['token'], 'SSL'),
			'text'      => $this->language->get('heading_title'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('feed/yandex_yml', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['yandex_yml_status'])) {
			$this->data['yandex_yml_status'] = $this->request->post['yandex_yml_status'];
		} else {
			$this->data['yandex_yml_status'] = $this->config->get('yandex_yml_status');
		}

		$this->data['data_feed'] = HTTP_CATALOG . 'index.php?route=feed/yandex_yml';

		if (isset($this->request->post['yandex_yml_datamodel'])) {
			$this->data['yandex_yml_datamodel'] = $this->request->post['yandex_yml_datamodel'];
		} else {
			$this->data['yandex_yml_datamodel'] = $this->config->get('yandex_yml_datamodel');
		}
		
		if (isset($this->request->post['yandex_yml_delivery_cost'])) {
			$this->data['yandex_yml_delivery_cost'] = $this->request->post['yandex_yml_delivery_cost'];
		} else {
			$this->data['yandex_yml_delivery_cost'] = $this->config->get('yandex_yml_delivery_cost');
		}

		if (isset($this->request->post['yandex_yml_currency'])) {
			$this->data['yandex_yml_currency'] = $this->request->post['yandex_yml_currency'];
		} else {
			$this->data['yandex_yml_currency'] = $this->config->get('yandex_yml_currency');
		}

		if (isset($this->request->post['yandex_yml_unavailable'])) {
			$this->data['yandex_yml_unavailable'] = $this->request->post['yandex_yml_unavailable'];
		} elseif ($this->config->get('yandex_yml_unavailable')) {
			$this->data['yandex_yml_unavailable'] = $this->config->get('yandex_yml_unavailable');
		} else {
			$this->data['yandex_yml_unavailable'] = '';
		}

		if (isset($this->request->post['yandex_yml_in_stock'])) {
			$this->data['yandex_yml_in_stock'] = $this->request->post['yandex_yml_in_stock'];
		} elseif ($this->config->get('yandex_yml_in_stock')) {
			$this->data['yandex_yml_in_stock'] = $this->config->get('yandex_yml_in_stock');
		} else {
			$this->data['yandex_yml_in_stock'] = 7;
		}

		if (isset($this->request->post['yandex_yml_out_of_stock'])) {
			$this->data['yandex_yml_out_of_stock'] = $this->request->post['yandex_yml_out_of_stock'];
		} elseif ($this->config->get('yandex_yml_in_stock')) {
			$this->data['yandex_yml_out_of_stock'] = $this->config->get('yandex_yml_out_of_stock');
		} else {
			$this->data['yandex_yml_out_of_stock'] = 5;
		}

		if (isset($this->request->post['yandex_yml_pickup'])) {
			$this->data['yandex_yml_pickup'] = $this->request->post['yandex_yml_pickup'];
		} else {
			$this->data['yandex_yml_pickup'] = $this->config->get('yandex_yml_pickup');
		}

		if (isset($this->request->post['yandex_yml_sales_notes'])) {
			$this->data['yandex_yml_sales_notes'] = $this->request->post['yandex_yml_sales_notes'];
		} else {
			$this->data['yandex_yml_sales_notes'] = $this->config->get('yandex_yml_sales_notes');
		}

		if (isset($this->request->post['yandex_yml_store'])) {
			$this->data['yandex_yml_store'] = $this->request->post['yandex_yml_store'];
		} else {
			$this->data['yandex_yml_store'] = $this->config->get('yandex_yml_store');
		}
		
		if (isset($this->request->post['yandex_yml_numpictures'])) {
			$this->data['yandex_yml_numpictures'] = $this->request->post['yandex_yml_numpictures'];
		} else {
			$this->data['yandex_yml_numpictures'] = $this->config->get('yandex_yml_numpictures');
		}

		//++++ Для вкладки аттрибутов ++++
		$this->data['tab_attributes_description'] = str_replace('%attr_url%', $this->url->link('catalog/attribute', 'token=' . $this->session->data['token'], 'SSL'), $this->language->get('tab_attributes_description'));
		$this->data['entry_attributes'] = $this->language->get('entry_attributes');
		$this->data['entry_adult'] = $this->language->get('entry_adult');
		$this->data['entry_manufacturer_warranty'] = $this->language->get('entry_manufacturer_warranty');
		$this->data['entry_country_of_origin'] = $this->language->get('entry_country_of_origin');
		if (isset($this->request->post['yandex_yml_attributes'])) {
			$this->data['yandex_yml_attributes'] = $this->request->post['yandex_yml_attributes'];
		} elseif ($this->config->get('yandex_yml_attributes') != '') {
			$this->data['yandex_yml_attributes'] = explode(',', $this->config->get('yandex_yml_attributes'));
		} else {
			$this->data['yandex_yml_attributes'] = array();
		}
		if (isset($this->request->post['yandex_yml_adult'])) {
			$this->data['yandex_yml_adult'] = $this->request->post['yandex_yml_adult'];
		} else {
			$this->data['yandex_yml_adult'] = $this->config->get('yandex_yml_adult');
		}
		if (isset($this->request->post['yandex_yml_manufacturer_warranty'])) {
			$this->data['yandex_yml_manufacturer_warranty'] = $this->request->post['yandex_yml_manufacturer_warranty'];
		} else {
			$this->data['yandex_yml_manufacturer_warranty'] = $this->config->get('yandex_yml_manufacturer_warranty');
		}
		if (isset($this->request->post['yandex_yml_country_of_origin'])) {
			$this->data['yandex_yml_country_of_origin'] = $this->request->post['yandex_yml_country_of_origin'];
		} else {
			$this->data['yandex_yml_country_of_origin'] = $this->config->get('yandex_yml_country_of_origin');
		}
		
		$this->load->model('catalog/attribute');
		$results = $this->model_catalog_attribute->getAttributes(array('sort'=>'attribute_group'));
		$this->data['attributes'] = $results;
		//---- Для вкладки аттрибутов ----

		$this->load->model('localisation/stock_status');

		$this->data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		$this->load->model('catalog/category');

		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

		if (isset($this->request->post['yandex_yml_categories'])) {
			$this->data['yandex_yml_categories'] = $this->request->post['yandex_yml_categories'];
		} elseif ($this->config->get('yandex_yml_categories') != '') {
			$this->data['yandex_yml_categories'] = explode(',', $this->config->get('yandex_yml_categories'));
		} else {
			$this->data['yandex_yml_categories'] = array();
		}


		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();
		$allowed_currencies = array_flip(array('RUR', 'RUB', 'BYR', 'KZT', 'UAH'));
		$this->data['currencies'] = array_intersect_key($currencies, $allowed_currencies);

		$this->template = 'feed/yandex_yml.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate($data) {
		if (!$this->user->hasPermission('modify', 'feed/yandex_yml')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
