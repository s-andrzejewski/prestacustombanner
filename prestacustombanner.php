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

        $this->displayName = $this->l('Custom banner');
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
        // Configuration::updateValue('PRESTACUSTOMBANNER_LIVE_MODE', false);
        Configuration::updateValue('PRESTACUSTOMBANNER_HEADING', null);
        Configuration::updateValue('PRESTACUSTOMBANNER_DESCRIPTION', null);
        Configuration::updateValue('PRESTACUSTOMBANNER_BG_COLOR', null);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        // Configuration::deleteByName('PRESTACUSTOMBANNER_LIVE_MODE');
        Configuration::deleteByName('PRESTACUSTOMBANNER_HEADING');
        Configuration::deleteByName('PRESTACUSTOMBANNER_DESCRIPTION');
        Configuration::deleteByName('PRESTACUSTOMBANNER_BG_COLOR');

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
                        // <i class="fa-solid fa-heading"></i>
                        'prefix' => '<i class="fa-solid fa-heading"></i>',
                        'desc' => $this->l('Enter a heading text for the message.'),
                        'name' => 'PRESTACUSTOMBANNER_HEADING',
                        'label' => $this->l('Heading'),
                    ),
                    array(
                        // 'col' => 8,
                        'row' => 5,
                        'type' => 'textarea',
                        // <i class="fa-solid fa-pen"></i>
                        'prefix' => '<i class="fa-solid fa-pen"></i>',
                        'desc' => $this->l('Enter message\'s content.'),
                        'name' => 'PRESTACUSTOMBANNER_DESCRIPTION',
                        'label' => $this->l('Description'),
                        'autoload_rte' => true,
                    ),
                    // Select
                    array(
                        'type' => 'select',
                        'label' => $this->l('Choose background color:'),
                        'name' => 'PRESTACUSTOMBANNER_BG_COLOR',
                        'required' => true,
                        'options' => array(
                        'query' => $id_bg_colors = array(
                            array(
                                'id_bg_colors' => 1,
                                'name' => 'transparent',
                                'value' => null
                            ),
                            array(
                                'id_bg_colors' => 2,
                                'name' => 'red',
                                'value' => '#FF0000'
                            ),
                            array(
                                'id_bg_colors' => 3,
                                'name' => 'yellow',
                                'value' => '#FFFF00'
                            ),  
                            array(
                                'id_bg_colors' => 4,
                                'name' => 'green',
                                'value' => '#0f0'
                            ),                                        
                        ),
                        'id' => 'id_bg_colors',
                        'name' => 'name'
                        )
                    ),
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
            'PRESTACUSTOMBANNER_HEADING' => Configuration::get('PRESTACUSTOMBANNER_HEADING', null),
            'PRESTACUSTOMBANNER_DESCRIPTION' => Configuration::get('PRESTACUSTOMBANNER_DESCRIPTION', null),
            'PRESTACUSTOMBANNER_BG_COLOR' => Configuration::get('PRESTACUSTOMBANNER_BG_COLOR', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
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
        $heading = Configuration::get('PRESTACUSTOMBANNER_HEADING');
        $desc = Configuration::get('PRESTACUSTOMBANNER_DESCRIPTION');
        $background_color = Configuration::get('PRESTACUSTOMBANNER_BG_COLOR');

        if (!empty($heading)) {
            $this->context->smarty->assign('prestacustommessage_heading', $heading);
            $render = true;
        }
        
        if (!empty($desc)) {
            $this->context->smarty->assign('prestacustommessage_desc', $desc);
            $render = true;
        }

        // if (filter_var($background_url, FILTER_VALIDATE_URL)) {
        //     $this->context->smarty->assign('prestacustommessage_bg', $background_url);
        // } elseif (!empty($background_url) && !preg_match("~^(?:f|ht)tps?://~i", $background_url)) {
        //     $background_url = "http://" . $background_url;
        //     $this->context->smarty->assign('prestacustommessage_bg', $background_url);
        // }

        if (!empty($background_color)) {
            $this->context->smarty->assign('PRESTACUSTOMBANNER_BG_COLOR', $background_color);
        }

        if ($render) {
            return $this->display(__FILE__, 'views/templates/prestacustommessage.tpl');
        }

        return 'Error: You should fill one of the inputs in backoffice.';
    }
}
