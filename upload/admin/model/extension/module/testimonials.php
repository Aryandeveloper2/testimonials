<?php

class ModelExtensionModuleTestimonials extends Model {
    public function getModuleId() {
        $sql = " SHOW TABLE STATUS LIKE '" . DB_PREFIX . "module'" ;
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function addTestimonial($data , $module_id) {
        $product_id =isset($data['product_id']) ? (int)$data['product_id']  : 0 ;
        $this->db->query("INSERT INTO " . DB_PREFIX . "testimonials 
        SET designation= '" . $this->db->escape($data['designation']) . "', 
        module_id= '" . (int)$module_id . "', 
        product_id= '" . $product_id . "', 
        status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', 
        date_added = NOW(), date_modified = NOW()");

        $testimonial_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "testimonials SET 
            image = '" . $this->db->escape($data['image']) . "' WHERE testimonial_id  = '" . (int)$testimonial_id . "'");
        }

        foreach ($data['testimonial_description'] as $language_id => $value) {

            $this->db->query("INSERT INTO " . DB_PREFIX . "testimonials_description SET testimonial_id = '" . (int) $testimonial_id . "', language_id = '" . (int) $language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        return $testimonial_id;
    }

    public function editTestimonials($testimonial_id, $data) {
        $product_id =isset($data['product_id']) ? (int)$data['product_id']  : 0 ;

        $this->db->query("UPDATE " . DB_PREFIX . "testimonials SET
         product_id = '" . $product_id . "', 
         designation= '" . $this->db->escape($data['designation']) . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "',  date_modified = NOW() WHERE testimonial_id='" . (int)$testimonial_id . "' ");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "testimonials SET image = '" . $this->db->escape($data['image']) . "' WHERE testimonial_id  = '" . (int)$testimonial_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "testimonials_description WHERE testimonial_id='" . (int)$testimonial_id . "'");


        foreach ($data['testimonial_description'] as $language_id => $value) {

            $this->db->query("INSERT INTO " . DB_PREFIX . "testimonials_description SET testimonial_id = '" . (int) $testimonial_id . "', language_id = '" . (int) $language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
        }

        return $testimonial_id;
    }

    public function getTotalTestimonials() {

        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "testimonials_description");

        return $query->row['total'];
    }

    public function getTestimonials($data = array(), $module_id) {
        $where = " WHERE t.module_id = '" . (int)$module_id . "' 
            AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
        
        $join = " LEFT JOIN " . DB_PREFIX . "testimonials_description td ON (t.testimonial_id = td.testimonial_id) ";
    
        // اضافه کردن شرط برای فیلتر محصول
        if (isset($data['filter_product']) && !empty($data['filter_product'])) {
            $join .= " JOIN " . DB_PREFIX . "product_description pd ON (
                t.product_id = pd.product_id 
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'
            ) ";
        }
    
        if (!empty($data['filter_title'])) {
            $where .= " AND td.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        }
    
        $sql = "SELECT * 
            FROM " . DB_PREFIX . "testimonials t 
            " . $join . "
            " . $where . "
        ";
    
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND t.status = '" . (int)$data['filter_status'] . "'";
        }
    
        $sql .= " GROUP BY t.testimonial_id";
    
        $sort_data = array(
            'td.title',
            't.status',
            't.sort_order'
        );
    
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY td.title";
        }
    
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
    
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
    
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
    
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
    
        $query = $this->db->query($sql);
    
        return $query->rows;
    }
    

    public function getTestimonial($testimonial_id) {
        //echo $testimonial_id;die('sss');
        //	echo "SELECT DISTINCT * FROM " . DB_PREFIX . "testimonals WHERE testimonial_id = '" . (int)$testimonial_id . "'";die;
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "testimonials WHERE testimonial_id = '" . (int)$testimonial_id . "'");

        return $query->row;
    }

    public function getTestimonialDescriptions($testimonial_id) {
        $testimonial_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonials_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");

        foreach ($query->rows as $result) {
            $testimonial_description_data[$result['language_id']] = array(
                'title'             => $result['title'],
                'description'      => $result['description'],
            );
        }

        return $testimonial_description_data;
    }

    public function getTestimonialss($testimonial_id) {
        //die('db');
        $testimonial_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonials WHERE testimonial_id = '" . (int)$testimonial_id . "'");

        return $query->rows;
    }



    public function deleteTestimonial($testimonial_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "testimonials` WHERE testimonial_id = '" . (int)$testimonial_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "testimonials_description` WHERE testimonial_id = '" . (int)$testimonial_id . "'");


        $this->cache->delete('testimonials');
    }

    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "testimonials` (
             `testimonial_id` int(11) NOT NULL AUTO_INCREMENT,
             `module_id` int(11) NOT NULL,
             `product_id` int(11) NOT NULL DEFAULT 0,
             `designation` varchar(64) NOT NULL,
             `image` varchar(100) NOT NULL,
             `sort_order` int(11) NOT NULL,
             `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
             `date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
             `status` int(1) NOT NULL, 
             PRIMARY KEY (`testimonial_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "testimonials_description` (
         `testimonial_id` int(11) NOT NULL,
         `language_id` int(11) NOT NULL,
         `title` varchar(255) NOT NULL,
         `description` text NOT NULL,
         PRIMARY KEY (`testimonial_id`,`language_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "testimonials`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "testimonials_description`");
    }

}
