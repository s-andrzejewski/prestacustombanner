<?php
/**
 * <ModuleClassName> => PrestaCustomMessage
 * <FileName> => ajax.php
 * Format expected: <ModuleClassName><FileName>ModuleFrontController
 */
require_once _PS_MODULE_DIR_.'prestacustommessage/prestacustommessage.php';

class PrestaCustomMessageAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        $module = new PrestaCustomMessage;
        $ajax_content = Configuration::get('PRESTACUSTOMMESSAGE_AJAX_CONTENT', null);

        if (Tools::isSubmit('action')) {

            $response = array('status' => false, "message" => $module->l('Nothing here.'));

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
        exit;
    }
}
