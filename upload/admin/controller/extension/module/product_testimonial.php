<?php
class ControllerExtensionModuleProductTestimonial extends Controller {

    private $error = array();

    public function index() {
        // load
        $this->load->language('extension/module/product_testimonial');
        $this->load->model('extension/module/testimonials');
        $this->load->model('setting/module');
        $this->load->model('tool/image');
        $this->load->model('catalog/product');

        
        $this->document->setTitle($this->language->get('heading_title'));


        if (isset($this->session->data['module_success'])) {
            $data['module_success'] = $this->session->data['module_success'];

            unset($this->session->data['module_success']);
        } else {
            $data['module_success'] = '';
        }

        if (isset($this->session->data['add_success'])) {
            $data['add_success'] = $this->session->data['add_success'];

            unset($this->session->data['add_success']);
        } else {
            $data['add_success'] = '';
        }

        if (isset($this->session->data['edit_success'])) {
            $data['edit_success'] = $this->session->data['edit_success'];

            unset($this->session->data['edit_success']);
        } else {
            $data['edit_success'] = '';
        }


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['add'] = $this->url->link('extension/module/product_testimonial/form', 'user_token=' . $this->session->data['user_token'], true);
        $data['setting'] = $this->url->link('extension/module/product_testimonial/setting', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['user_token'] = $this->session->data['user_token'];
      

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id.title';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_product'])) {
            $filter_product = $this->request->get['filter_product'];
            $url .= '&filter_product=' . $filter_product;
            $data['filter_product'] = $filter_product;
        } else {
            $filter_product = '';
        }
        
        
        if (isset($this->request->get['filter_title'])) {
            $filter_title = $this->request->get['filter_title'];
            $url .= '&filter_title=' . $filter_title;
            $data['filter_title'] = $filter_title;
        } else {
            $filter_title = '';
        }
     

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['testimonials'] = array();

        $filter_data = array(
            'sort' => $sort,
            'module_id' => 0,
            'filter_product' => $filter_product,
            'filter_title' => $filter_title,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $testimonials_total = $this->model_extension_module_testimonials->getTotalTestimonials($filter_data);
    
        $results = $this->model_extension_module_testimonials->getTestimonials($filter_data , 0);

        foreach ($results as $result) {
            $show = false;
            if(isset($result['product_id']) && $result['product_id'] > 0) {
                $result['product_name'] = $this->model_catalog_product->getProductDescriptions($result['product_id']);
                $result['product_name'] = isset($result['product_name'][(int)$this->config->get('config_language_id')]['name']) ? 
                $result['product_name'][(int)$this->config->get('config_language_id')]['name']
                : '';
                $show =  HTTP_CATALOG .'index.php?route=product/product&product_id='.$result['product_id'];
            }
    
            $data['testimonials'][] = array(
                'testimonial_id' => $result['testimonial_id'],
                'product_name' => $result['product_name'],
                'title' => $result['title'],
                'sort_order' => $result['sort_order'],
                'show' => $show,
                'edit' => $this->url->link('extension/module/product_testimonial/form', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $result['testimonial_id']  . $url, true)
            );
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $testimonials_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($testimonials_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($testimonials_total - $this->config->get('config_limit_admin'))) ? $testimonials_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $testimonials_total, ceil($testimonials_total / $this->config->get('config_limit_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['sort_title'] = $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
        $data['sort_sort_order'] = $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);
      

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/product_testimonial_list', $data));
    }

    public function delete() {
        $this->load->model('extension/module/testimonials');
        if(isset($this->request->get['testimonials_id'])){
            $ids = explode(',',$this->request->get['testimonials_id']);
            foreach($ids as $id) {
                $this->model_extension_module_testimonials->deleteTestimonial($id);
            }
        }
    }

    public function form() {
        $this->load->language('extension/module/product_testimonial');
        $this->load->model('extension/module/testimonials');
        $this->load->model('setting/module');
        $this->load->model('tool/image');
        $this->load->model('catalog/product');


        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            if(isset($this->request->get['testimonial_id'])) {
                $this->model_extension_module_testimonials->editTestimonials($this->request->get['testimonial_id'], $this->request->post);
                $testimonial_id = $this->request->get['testimonial_id'];
            } else {
                
                $testimonial_id = $this->model_extension_module_testimonials->addTestimonial($this->request->post , 0);
            }

            $this->session->data['edit_success'] = $this->language->get('edit_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->session->data['success'] = $this->language->get('text_success');
			$this->load->controller('extension/component/save_section/redirect', [
				'action' =>  $this->request->post['action'],
				'save' => $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token']. '&type=module', 'SSL'),
				'save_edit' => $this->url->link('extension/module/product_testimonial/form', 'testimonial_id=' . $testimonial_id .'&user_token=' . $this->session->data['user_token'], 'SSL'),
				'save_new' => $this->url->link('extension/module/product_testimonial/form', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			]);

            $this->response->redirect($this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $data['text_form'] = !isset($this->request->get['testimonial_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = array();
        }

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = array();
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );


        if (!isset($this->request->get['testimonial_id'])) {
            $data['action'] = $this->url->link('extension/module/product_testimonial/form', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/product_testimonial/form', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $this->request->get['testimonial_id'] . $url, true);
        }


        if (isset($this->request->get['testimonial_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $testimonial_info = $this->model_extension_module_testimonials->getTestimonial($this->request->get['testimonial_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['testimonial_description'])) {
            $data['testimonial_description'] = $this->request->post['testimonial_description'];
        } elseif (isset($this->request->get['testimonial_id'])) {
            $data['testimonial_description'] = $this->model_extension_module_testimonials->getTestimonialDescriptions($this->request->get['testimonial_id']);
        } else {
            $data['testimonial_description'] = array();
        }

        if (isset($this->request->post['designation'])) {
            $data['designation'] = $this->request->post['designation'];
        } elseif (!empty($testimonial_info)) {
            $data['designation'] = $testimonial_info['designation'];
        } else {
            $data['designation'] = '';
        }

        // Image

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($testimonial_info)) {
            $data['image'] = $testimonial_info['image'];
        } else {
            $data['image'] = '';
        }


        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($testimonial_info) && is_file(DIR_IMAGE . $testimonial_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($testimonial_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($testimonial_info)) {
            $data['status'] = $testimonial_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($testimonial_info)) {
            $data['sort_order'] = $testimonial_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

      

        if (isset($this->request->post['product_id'])) {
            $data['product_id'] = $this->request->post['product_id'];
        } elseif (!empty($testimonial_info)) {
            $data['product_id'] = $testimonial_info['product_id'];
        } else {
            $data['product_id'] = '';
        }
        if ($data['product_id'] != '') {
            $data['product'] = $this->model_catalog_product->getProductDescriptions($data['product_id']);
            
            $data['product'] = isset($data['product'][(int)$this->config->get('config_language_id')]['name']) ? 
            $data['product'][(int)$this->config->get('config_language_id')]['name']
            : '';
            
        }

        $data['save_section'] = $this->load->controller('extension/component/save_section', [
			'form_id' 		=> "form-module",
			'save_new' 		=> true,
			'save_edit' 	=> true,
			'save' 			=> true,
			'cancel' 		=> $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		]);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/product_testimonial_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/testimonials')) {
            $this->error['warning'] = $this->language->get('error_permission');
        } else {
            foreach ($this->request->post['testimonial_description'] as $language_id => $value) {
                if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 64)) {
                    $this->error['title'][$language_id] = $this->language->get('error_title');
                }

                if (utf8_strlen($value['description']) < 3) {
                    $this->error['description'][$language_id] = $this->language->get('error_description');
                }
            }

            if ($this->error && !isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get('error_warning');
            }
        }

        return !$this->error;
    }



    public function setting() {
        $this->load->language('extension/module/product_testimonial');
        $this->load->model('extension/module/testimonials');
        $this->load->model('setting/module');
        $this->load->model('tool/image');
        $this->load->model('catalog/product');

        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_setting_setting->editSetting('product_testimonial', [
                'product_testimonial' => $this->request->post
            ]);


            $this->session->data['edit_success'] = $this->language->get('edit_success');


            $this->session->data['success'] = $this->language->get('text_success');
			$this->load->controller('extension/component/save_section/redirect', [
				'action' =>  $this->request->post['action'],
				'save' => $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token']. '&type=module', 'SSL'),
				'save_edit' => $this->url->link('extension/module/product_testimonial/setting', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			]);

            $this->response->redirect($this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['text_form'] = 'تنظیمات';


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'], true)
        );

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $module_info = $this->config->get('product_testimonial');
        } else if ($this->request->server['REQUEST_METHOD'] == 'POST'){
            $module_info = $this->request->post;
        }
        
        $keys = [
            'width' => 100, 'height' => 100
        ];

        foreach ($keys as $key => $value) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } else if (isset($module_info[$key])){
                $data[$key] = $module_info[$key];
            } else {
                $data[$key] = $value;
            }
        }
  
        $module_info['module_folder'] = 'testimonial';

		$module_info['template_categories'] = [
			'testimonial_'
		];

        
    	$data['main_sub_module_form'] = $this->load->controller('extension/component/main_sub_module_form', $module_info);
		$data['slider_setting'] = $this->load->controller('extension/component/slider_setting', $module_info);
		$data['template_setting'] = $this->load->controller('extension/component/template_setting', $module_info);
		// $data['product_setting'] = $this->load->controller('extension/component/product_setting', $module_info);


        $data['save_section'] = $this->load->controller('extension/component/save_section', [
			'form_id' 		=> "form-module",
			'save_new' 		=> false,
			'save_edit' 	=> true,
			'save' 			=> true,
			'cancel' 		=> $this->url->link('extension/module/product_testimonial', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		]);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/product_testimonial_setting', $data));
    }


}
