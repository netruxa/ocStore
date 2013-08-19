<?php
/**
 * Yandex.YML data feed for OpenCart (ocStore) 1.5.5.x
 *
 * Model class to load data for YML
 *
 * @author Alexander Toporkov <toporchillo@gmail.com>
 * @copyright (C) 2013- Alexander Toporkov
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Extended version of this module: http://opencartforum.ru/files/file/670-eksport-v-iandeksmarket/
 */ 
class ModelExportYandexYml extends Model {
	public function getCategory() {
		$query = $this->db->query("SELECT cd.name, c.category_id, c.parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' AND c.sort_order <> '-1'");

		return $query->rows;
	}

	public function getProduct($allowed_categories, $out_of_stock_id, $vendor_required = true) {
		$query = $this->db->query("SELECT
			p.*, pd.name, pd.description, m.name AS manufacturer, p2c.category_id, IFNULL(ps.price, p.price) AS price
			FROM " . DB_PREFIX . "product p
			JOIN " . DB_PREFIX . "product_to_category AS p2c ON (p.product_id = p2c.product_id) " 
			. ($vendor_required ? '' : 'LEFT ') . "JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ps.date_start < NOW() AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())
			WHERE p2c.category_id IN (" . $this->db->escape($allowed_categories) . ")
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND p.date_available <= NOW() 
				AND p.status = '1'
				AND (p.quantity > '0' OR p.stock_status_id != '" . (int)$out_of_stock_id . "')
				GROUP BY p.product_id ORDER BY product_id");

		return $query->rows;
	}

	public function getProductImages($numpictures = 9) {
		$query = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX . "product_image ORDER BY product_id, sort_order");
		$ret = array();
		foreach($query->rows as $row) {
			if (!isset($ret[$row['product_id']])) {
				$ret[$row['product_id']] = array();
			}
			if (count($ret[$row['product_id']]) < $numpictures)
				$ret[$row['product_id']][] = $row['image'];
		}
		return $ret;
	}

	public function getProductOptions($option_ids, $product_id) {
		$ids = array(0);
		foreach ($option_ids as $id)
			$ids[] = intval($id);
		$lang = (int)$this->config->get('config_language_id');
		
		$query = $this->db->query("SELECT pov.option_id, od.name AS option_name, ovd.name, pov.quantity, pov.price, pov.price_prefix
			FROM " . DB_PREFIX . "product_option_value pov 
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id)
			LEFT JOIN " . DB_PREFIX . "option_description od ON (od.option_id = pov.option_id) AND (od.language_id = '$lang')
			WHERE pov.option_id IN (". implode(',', $ids) .") AND pov.product_id = '". (int)$product_id."'
				AND ovd.language_id = '$lang'");
		return $query->rows;
	}
	
	public function getAttributes($attr_ids) {
		if (!$attr_ids) return array();
		$query = $this->db->query("SELECT a.attribute_id, ad.name
			FROM " . DB_PREFIX . "attribute a
			LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id)
			WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND a.attribute_id IN (" . $this->db->escape($attr_ids) . ")
				ORDER BY a.attribute_id, ad.name");
		$ret = array();
		foreach($query->rows as $row) {
			$ret[$row['attribute_id']] = $row['name'];
		}
		return $ret;
	}
	
	public function getProductAttributes($product_id) {
		$query = $this->db->query("SELECT pa.attribute_id, pa.text
			FROM " . DB_PREFIX . "product_attribute pa
			WHERE pa.product_id = '" . (int)$product_id . "'
				AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "'
				ORDER BY pa.attribute_id");
		return $query->rows;
	}
}
?>