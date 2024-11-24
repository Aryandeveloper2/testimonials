<?php
class ControllerExtensionModuleTestimonialList extends Controller {

    private $error = array();

    public function index() {
        // load
        $this->load->language('extension/module/testimonials');
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
            'href' => $this->url->link('extension/module/testimonials', 'user_token=' . $this->session->data['user_token'], true)
        );

       
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
        $modules = $this->model_setting_module->getModulesByCode('testimonials');
        foreach ($modules as $key => $module) {
            $data['modules'][] = $module;
            $_module_id = $module['module_id'];
            $data['modules'][$key]['href'] = $this->url->link('extension/module/testimonial_list', "module_id=$_module_id&user_token=" . $this->session->data["user_token"], true);
        }

        if (isset($this->request->get['module_id'])) {
            $module_id = $this->request->get['module_id'];
            $url .= '&module_id=' . $module_id;
            $data['module_id'] = $module_id;
        } else {
            $module_id = '';
            if(isset( $data['modules'][0]['href'])) {
                $this->response->redirect(
                    $data['modules'][0]['href']
                );
            }

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
        
        $data['add'] = $this->url->link('extension/module/testimonial_list/form', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $data['testimonials'] = array();

        $filter_data = array(
            'sort'          => $sort,
            'module_id'  => $module_id,
            'filter_title'  => $filter_title,
            'order'         => $order,
            'start'         => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'         => $this->config->get('config_limit_admin')
        );

        $testimonials_total = $this->model_extension_module_testimonials->getTotalTestimonials($filter_data , $module_id);

        $results = $this->model_extension_module_testimonials->getTestimonials($filter_data , $module_id);

        foreach ($results as $result) {
        
            $data['testimonials'][] = array(
                'testimonial_id'    => $result['testimonial_id'],
                'title'             => $result['title'],
                'sort_order'        => $result['sort_order'],
                'edit'              => $this->url->link('extension/module/testimonial_list/form', 
                'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $result['testimonial_id']  . $url, true)
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

        $pagination         = new Pagination();
        $pagination->total  = $testimonials_total;
        $pagination->page   = $page;
        $pagination->limit  = $this->config->get('config_limit_admin');
        $pagination->url    = $this->url->link('extension/module/testimonial_list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results']    = sprintf($this->language->get('text_pagination'), ($testimonials_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($testimonials_total - $this->config->get('config_limit_admin'))) ? $testimonials_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $testimonials_total, ceil($testimonials_total / $this->config->get('config_limit_admin')));
        $data['sort']       = $sort;
        $data['order']      = $order;

        $data['sort_title']         = $this->url->link('extension/module/testimonial_list', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
        $data['sort_sort_order']    = $this->url->link('extension/module/testimonial_list', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);
      

        $data['placeholder']    = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['header']         = $this->load->controller('common/header');
        $data['column_left']    = $this->load->controller('common/column_left');
        $data['footer']         = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/testimonial_list', $data));
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
        $this->load->language('extension/module/testimonials');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/testimonials');
        $this->load->model('tool/image');
        $module_id = $this->request->get['module_id'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() && isset($this->request->get['module_id'])) {
            if(isset($this->request->get['testimonial_id'])) {
                $this->model_extension_module_testimonials->editTestimonials($this->request->get['testimonial_id'], $this->request->post);
                $testimonial_id = $this->request->get['testimonial_id'];
            } else {
                $testimonial_id = $this->model_extension_module_testimonials->addTestimonial($this->request->post ,  $this->request->get['module_id']);
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
				'save' => $this->url->link('extension/module/testimonial_list', "module_id=$module_id". '&user_token=' . $this->session->data['user_token']. '&type=module', 'SSL'),
				'save_edit' => $this->url->link('extension/module/testimonial_list/form',  "module_id=$module_id". '&testimonial_id=' . $testimonial_id .'&user_token=' . $this->session->data['user_token'], 'SSL'),
				'save_new' => $this->url->link('extension/module/testimonial_list/form', "module_id=$module_id". '&user_token=' . $this->session->data['user_token'], 'SSL'),
			]);

            $this->response->redirect($this->url->link('extension/module/testimonial_list', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
            'href' => $this->url->link('extension/module/testimonials', 'user_token=' . $this->session->data['user_token'] . '&module_id=' .$this->request->get['module_id']. $url, true)
        );


        if (!isset($this->request->get['testimonial_id'])) {
            $data['action'] = $this->url->link('extension/module/testimonial_list/form', 'user_token=' . $this->session->data['user_token'] . '&module_id=' .$this->request->get['module_id']. $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/testimonial_list/form', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $this->request->get['testimonial_id'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
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
        
        $data['save_section'] = $this->load->controller('extension/component/save_section', [
			'form_id' 		=> "form-module",
			'save_new' 		=> true,
			'save_edit' 	=> true,
			'save' 			=> true,
			'cancel' 		=>  $this->url->link('extension/module/testimonial_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' .$this->request->get['module_id'], true),
		]);
		
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/testimonials/testimonials_form', $data));
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

}
