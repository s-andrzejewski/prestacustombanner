<?php
/**
 * <ModuleClassName> => PrestaCustomMessage
 * <FileName> => ajax.php
 * Format expected: <ModuleClassName><FileName>ModuleFrontController
 */

class PrestaCustomMessageAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $ajax_content = Configuration::get('PRESTACUSTOMMESSAGE_AJAX_CONTENT', null);

        if (Tools::isSubmit('action')) {

            $response = array('status' => false, "message" => $this->module->l('Nothing here.'));

            switch (Tools::getValue('action')) {

                case 'get_message':
                    
                    $response = array(
                        'status' => true,
                        'message' => $ajax_content,
                    );

                    break;

                default:
                    break;

            }
        }

        header('Content-Type: application/json');
        $json = Tools::jsonEncode($response);
        echo $json;
        die;
    }
}
