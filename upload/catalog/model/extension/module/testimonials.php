<?php
class ModelExtensionModuleTestimonials extends Model {
    public function getTestimonials($module_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "testimonials t LEFT JOIN " . DB_PREFIX . "testimonials_description td ON (t.testimonial_id = td.testimonial_id) WHERE t.status='1' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' AND t.module_id = '" . $module_id . "'";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getProductTestimonials($product_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "testimonials t 
        LEFT JOIN " . DB_PREFIX . "testimonials_description td ON (t.testimonial_id = td.testimonial_id) 
        WHERE t.status='1' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' 
        AND t.product_id = '" . $product_id . "'
        AND t.module_id = 0";

        $query = $this->db->query($sql);
        return $query->rows;
    }

}