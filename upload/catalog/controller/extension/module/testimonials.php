<?php
class ControllerExtensionModuleTestimonials extends Controller {
    public function index($setting) {
        $this->load->language('extension/module/testimonials');
        static $module = 0;
        $this->load->model('extension/module/testimonials');

        $this->load->model('design/banner');
        $this->load->model('tool/image');

        $result = $this->model_extension_module_testimonials->getTestimonials($setting['module_id']);

        $data = $setting;

        foreach($result as $results) {
            $data['testimonials'][]=array(
                'title'            => $results['title'],
                'image'     	   => $results['image'],
                'thumb'     	   => $this->model_tool_image->resize($results['image'],$data['width'],$data['height']),
                'description'      =>  html_entity_decode($results['description'], ENT_QUOTES, 'UTF-8'),
                'status'       => $results['status'],
                'sort_order' => $results['sort_order'],
                'designation'     => $results['designation']
            );
        }


        $data['module'] = $module++;

        return $this->output($setting['template'], $data);
    }


    public function product() {
        $this->load->language('extension/module/testimonials');
        $this->load->model('extension/module/testimonials');

        $this->load->model('tool/image');
        $setting = $this->config->get('product_testimonial');

        $data = $setting;
        if(isset($this->request->get['product_id'])) {
            $product_id = $this->request->get['product_id'];
            $result = $this->model_extension_module_testimonials->getProductTestimonials($product_id);

            
            foreach($result as $results) {
                
                $data['testimonials'][] = array(
                    'title'            => $results['title'],
                    'image'     	   => $results['image'],
                    'thumb'     	   => $this->model_tool_image->resize($results['image'],$setting['width'],$setting['height']),
                    'description'      =>  html_entity_decode($results['description'], ENT_QUOTES, 'UTF-8'),
                    'status'       => $results['status'],
                    'sort_order' => $results['sort_order'],
                    'designation'     => $results['designation']
                );
            }

        }

      return $this->output($setting['template'], $data);
    }

    public function output($template, $data) {
        try {
            $adaptive_status = $this->config->get('config_adaptive_status');

            if($this->session->data['device']=='mobile' && $adaptive_status == 1){
                $this->document->addStyle('catalog/view/theme/default/stylesheet/testimonial/mobile/' . $template . '.css' ,'stylesheet');
                return $this->load->view('extension/module/testimonial/mobile/' . $template, $data);
            } else {
                $this->document->addStyle('catalog/view/theme/default/stylesheet/testimonial/' . $template . '.css' ,'stylesheet');
                return $this->load->view('extension/module/testimonial/' . $template, $data);
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
}