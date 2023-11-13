<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaCustomMessage extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'prestacustommessage';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Szymon Andrzejewski';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Banner with custom message');
        $this->description = $this->l('Displays custom banner on homepage.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PRESTACUSTOMMESSAGE_HEADING', null);
        Configuration::updateValue('PRESTACUSTOMMESSAGE_DESCRIPTION', null);
        Configuration::updateValue('PRESTACUSTOMMESSAGE_TEXT_COLOR', null);
        Configuration::updateValue('PRESTACUSTOMMESSAGE_IMAGE', null);
        Configuration::updateValue('PRESTACUSTOMMESSAGE_BUTTON_TEXT', null);
        Configuration::updateValue('PRESTACUSTOMMESSAGE_BUTTON_URL', null);

        

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('PRESTACUSTOMMESSAGE_HEADING');
        Configuration::deleteByName('PRESTACUSTOMMESSAGE_DESCRIPTION');
        Configuration::deleteByName('PRESTACUSTOMMESSAGE_TEXT_COLOR');
        Configuration::deleteByName('PRESTACUSTOMMESSAGE_IMAGE');
        Configuration::deleteByName('PRESTACUSTOMMESSAGE_BUTTON_TEXT');
        Configuration::deleteByName('PRESTACUSTOMMESSAGE_BUTTON_URL');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPrestaCustomMessageModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPrestaCustomMessageModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'prefix' => '<i class="fa-solid fa-heading"></i>',
                        'desc' => $this->l('Enter a heading text for the message.'),
                        'name' => 'PRESTACUSTOMMESSAGE_HEADING',
                        'label' => $this->l('Heading'),
                    ),
                    array(
                        'row' => 5,
                        'type' => 'textarea',
                        'prefix' => '<i class="fa-solid fa-pen"></i>',
                        'desc' => $this->l('Enter banner\'s content.'),
                        'name' => 'PRESTACUSTOMMESSAGE_DESCRIPTION',
                        'label' => $this->l('Description'),
                        'autoload_rte' => true,
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'prefix' => '',
                        'desc' => $this->l('Enter color code for text (hex or rgba).'),
                        'name' => 'PRESTACUSTOMMESSAGE_TEXT_COLOR',
                        'label' => $this->l('Font color:'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Upload image'),
                        'name' => 'PRESTACUSTOMMESSAGE_IMAGE',
                        'desc' => $this->l('Upload an image for the custom banner.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Button text'),
                        'name' => 'PRESTACUSTOMMESSAGE_BUTTON_TEXT',
                        'desc' => $this->l('Enter the text for the button.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Button URL'),
                        'name' => 'PRESTACUSTOMMESSAGE_BUTTON_URL',
                        'desc' => $this->l('Enter the URL for the button click action.'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'PRESTACUSTOMMESSAGE_HEADING' => Configuration::get('PRESTACUSTOMMESSAGE_HEADING', null),
            'PRESTACUSTOMMESSAGE_DESCRIPTION' => Configuration::get('PRESTACUSTOMMESSAGE_DESCRIPTION', null),
            'PRESTACUSTOMMESSAGE_TEXT_COLOR' => Configuration::get('PRESTACUSTOMMESSAGE_TEXT_COLOR', null),
            'PRESTACUSTOMMESSAGE_IMAGE' => Configuration::get('PRESTACUSTOMMESSAGE_IMAGE', null),
            'PRESTACUSTOMMESSAGE_BUTTON_TEXT' => Configuration::get('PRESTACUSTOMMESSAGE_BUTTON_TEXT', null),
            'PRESTACUSTOMMESSAGE_BUTTON_URL' => Configuration::get('PRESTACUSTOMMESSAGE_BUTTON_URL', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
    
        foreach (array_keys($form_values) as $key) {
            if ($key === 'PRESTACUSTOMMESSAGE_IMAGE') {
                // Handle image upload and save the file path
                if (!empty($_FILES[$key]['name'])) {
                    $image_path = $this->uploadImage($_FILES[$key]);
                    Configuration::updateValue($key, $image_path);
                }
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
    }
    
    private function uploadImage($file)
    {
        // Define the target directory where images will be uploaded
        $upload_dir = _PS_MODULE_DIR_ . $this->name . '/views/img/';
    
        // Generate a unique filename for the uploaded image
        $file_name = uniqid() . '_' . $file['name'];
    
        // Set the full path to the uploaded image
        $target_file = $upload_dir . $file_name;
    
        // Check if the file was uploaded successfully
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $this->_path . 'views/img/' . $file_name;
        }
    
        return null;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        $render = false;
        $heading = Configuration::get('PRESTACUSTOMMESSAGE_HEADING');
        $desc = Configuration::get('PRESTACUSTOMMESSAGE_DESCRIPTION');
        $text_color = Configuration::get('PRESTACUSTOMMESSAGE_TEXT_COLOR');
        $image = Configuration::get('PRESTACUSTOMMESSAGE_IMAGE');
        $btn_text = Configuration::get('PRESTACUSTOMMESSAGE_BUTTON_TEXT');
        $btn_url = Configuration::get('PRESTACUSTOMMESSAGE_BUTTON_URL');

        if (!empty($heading)) {
            $this->context->smarty->assign('prestacustommessage_heading', $heading);
            $render = true;
        }
        
        if (!empty($desc)) {
            $this->context->smarty->assign('prestacustommessage_desc', $desc);
            $render = true;
        }

        if (!empty($text_color)) {
            $this->context->smarty->assign('prestacustommessage_text_color', $text_color);
        }

        if (!empty($image)) {
            $this->context->smarty->assign('prestacustommessage_img', $image);
        }

        if (!empty($btn_text)) {
            $this->context->smarty->assign('prestacustommessage_btn_txt', $btn_text);
        }

        if (!empty($btn_url)) {
            $this->context->smarty->assign('prestacustommessage_btn_url', $btn_url);
        }

        if ($render) {
            return $this->display(__FILE__, 'views/templates/prestacustommessage.tpl');
        }

        return '';
    }
}
