<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!class_exists('RedsysAPI17')) {
    require_once dirname(__FILE__) . '/../../apiRedsys/apiRedsysFinal.php';
}


/**
 * @since 1.5.0
 */
class RedsysValidationModuleFrontController extends ModuleFrontController
{
    // public $ssl = true;

    // const LANG_DOMAIN = 'Modules.Redsys.Shop';

    // public function __construct()
    // {
    //     $this->name = 'redsys17';
    //     $this->tab = 'payments_gateways';
    //     $this->version = '1.0.5';
    //     $this->author = 'modulos-prestashop.com';
    //     $this->controllers = array('validation');

    //     $this->currencies = true;
    //     $this->currencies_mode = 'checkbox';

    //     $config
    //         = Configuration::getMultiple(
    //         array(
    //             'REDSYS_URLTPV',
    //             'REDSYS_NOMBRE',
    //             'REDSYS_CODIGO',
    //             'REDSYS_TIPOPAGO',
    //             'REDSYS_TERMINAL',
    //             'REDSYS_CLAVE256',
    //             'REDSYS_TRANS',
    //             'REDSYS_ERROR_PAGO',
    //             'REDSYS_LOG',
    //             'REDSYS_IDIOMAS_ESTADO',
    //             'REDSYS_IMG'
    //         )
    //     );

    //     $this->env = $config['REDSYS_URLTPV'];
    //     switch ($this->env) {
    //         case 1: //Real
    //             $this->urltpv = 'https://sis.redsys.es/sis/realizarPago/utf-8';
    //             break;
    //         case 2: //Pruebas t
    //             $this->urltpv = 'https://sis-t.redsys.es:25443/sis/realizarPago/utf-8';
    //             break;
    //         case 3: // Pruebas i
    //             $this->urltpv = 'https://sis-i.redsys.es:25443/sis/realizarPago/utf-8';
    //             break;
    //         case 4: //Pruebas d
    //             $this->urltpv = 'http://sis-d.redsys.es/sis/realizarPago/utf-8';
    //             break;
    //     }

    //     if (isset($config['REDSYS_NOMBRE'])) {
    //         $this->nombre = $config['REDSYS_NOMBRE'];
    //     }
    //     if (isset($config['REDSYS_CODIGO'])) {
    //         $this->codigo = $config['REDSYS_CODIGO'];
    //     }
    //     if (isset($config['REDSYS_TIPOPAGO'])) {
    //         $this->tipopago = $config['REDSYS_TIPOPAGO'];
    //     }
    //     if (isset($config['REDSYS_TERMINAL'])) {
    //         $this->terminal = $config['REDSYS_TERMINAL'];
    //     }
    //     if (isset($config['REDSYS_CLAVE256'])) {
    //         $this->clave256 = $config['REDSYS_CLAVE256'];
    //     }
    //     if (isset($config['REDSYS_TRANS'])) {
    //         $this->trans = $config['REDSYS_TRANS'];
    //     }
    //     if (isset($config['REDSYS_ERROR_PAGO'])) {
    //         $this->error_pago = $config['REDSYS_ERROR_PAGO'];
    //     }
    //     if (isset($config['REDSYS_LOG'])) {
    //         $this->activar_log = $config['REDSYS_LOG'];
    //     }
    //     if (isset($config['REDSYS_IDIOMAS_ESTADO'])) {
    //         $this->idiomas_estado = $config['REDSYS_IDIOMAS_ESTADO'];
    //     }

    //     if (isset($config['REDSYS_IMG'])) {
    //         $this->image = $config['REDSYS_IMG'];
    //     }

    //     $this->imageFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;

    //     $this->need_instance = 0;
    //     $this->bootstrap = true;
    //     parent::__construct();
    //     $this->displayName = $this->trans('Redsys para Prestashop 1.7', array(), self::LANG_DOMAIN);
    //     $this->description = $this->trans('Pagos con tarjeta para Prestashop 1.7', array(), self::LANG_DOMAIN);
    //     $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7.99.99');

    //     $this->page = basename(__FILE__, '.php');

    //     // Mostrar aviso si faltan datos de config.
    //     if (!isset($this->urltpv)
    //         || !isset($this->nombre)
    //         || !isset($this->codigo)
    //         || !isset($this->tipopago)
    //         || !isset($this->terminal)
    //         || !isset($this->clave256)
    //         || !isset($this->trans)
    //         || !isset($this->error_pago)
    //         || !isset($this->activar_log)
    //         || !isset($this->idiomas_estado)) {
    //         $this->warning
    //             = $this->trans(
    //             'Faltan datos por configurar en el módulo de Redsys.',
    //             array(),
    //             self::LANG_DOMAIN
    //         );
    //     }
    // }
    /**
     * @see FrontController::initContent()
     */
    // public function initContent()
    // {
    //     error_log("ESTAMOS EN INITCONTENT!!! Redsys17ValidationModuleFrontController");
    //     parent::initContent();

    //     $cart = $this->context->cart;
    //     if (!$this->module->checkCurrency($cart)) {
    //         Tools::redirect('index.php?controller=order');
    //     }

    //     $this->context->smarty->assign(array(
    //         'nbProducts' => $cart->nbProducts(),
    //         'cust_currency' => $cart->id_currency,
    //         'currencies' => $this->module->getCurrency((int)$cart->id_currency),
    //         'total' => $cart->getOrderTotal(true, Cart::BOTH),
    //         'isoCode' => $this->context->language->iso_code,
    //         'this_path' => $this->module->getPathUri(),
    //         'this_path_check' => $this->module->getPathUri(),
    //         'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
    //     ));

    //     $this->setTemplate('module:redsys17/views/templates/front/payment.tpl');
    // }

    public function postProcess()
    {
        $cart = $this->context->cart;
        // error_log("VALIDATION NUEVO!!! Redsys17ValidationModuleFrontController");
        // error_log("VARIABLE this->module **********: " . var_export($this->module));

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] === 'redsys') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->trans('This payment method is not available.', array(), 'Modules.Checkpayment.Shop'));
        }

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        // $mailVars =    array(
        //     '{check_name}' => Configuration::get('CHEQUE_NAME'),
        //     '{check_address}' => Configuration::get('CHEQUE_ADDRESS'),
        //     '{check_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')));
        $mailVars = array();


        $this->module->validateOrder((int)$cart->id, Configuration::get('PS_OS_CHEQUE'), $total, $this->module->displayName, null, $mailVars, (int)$currency->id, false, $customer->secure_key);
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
}
