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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//PBS: Este controlador gestiona la respuesta POST automática que envía redsys justo al confirmar el pago correcto

if (!class_exists('RedsysAPI17')) {
    require_once dirname(__FILE__) . '/../../apiRedsys/apiRedsysFinal.php';
}


/**
 * @since 1.5.0
 */
class RedsysValidationPOSTModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        if ($this->module->active != 1) {
            error_log("INITIATION NOT AUTHORIZED!!! Redsys17ValidationModuleFrontController");
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);



        /** Recoger datos de respuesta **/
        $version            = Tools::getValue('Ds_SignatureVersion');
        $datos              = Tools::getValue('Ds_MerchantParameters');
        $firma_remota       = Tools::getValue('Ds_Signature');

        // Se crea Objeto
        $miObj = new RedsysAPI;
        
        // /** Se decodifican los datos enviados y se carga el array de datos **/
        $decodec = $miObj->decodeMerchantParameters($datos);

        // /** Clave **/
        $kc = Configuration::get('REDSYS_CLAVE256');
        
        /** Se calcula la firma **/
        $firma_local = $miObj->createMerchantSignatureNotif($kc,$datos);
        
        /** Extraer datos de la notificación **/
        $total     = $miObj->getParameter('Ds_Amount');
        $pedido    = $miObj->getParameter('Ds_Order');
        $codigo    = $miObj->getParameter('Ds_MerchantCode');
        $moneda    = $miObj->getParameter('Ds_Currency');
        $respuesta = $miObj->getParameter('Ds_Response');
        $id_trans = $miObj->getParameter('Ds_AuthorisationCode');
        
        /** Código de comercio **/
        $codigoOrig = Configuration::get('REDSYS_CODIGO');
        
        /** Pedidos Cancelados **/
        $error_pago = Configuration::get('REDSYS_ERROR_PAGO');
        
        // /** Log de Errores **/

        $pedidoSecuencial = $pedido;
        $pedido = intval($pedido);
        /** VALIDACIONES DE LIBRERÍA **/
        if ($firma_local === $firma_remota
            && checkImporte($total)
            && checkPedidoNum($pedido)
            && checkFuc($codigo)
            && checkMoneda($moneda)
            && checkRespuesta($respuesta)) {

                /** Creamos los objetos para confirmar el pedido **/
                $context = Context::getContext();
                $cart = new Cart($pedido);
                $redsys = new redsys();

                /** Validamos Objeto carrito **/
                if ($cart->id_customer == 0
                    || $cart->id_address_delivery == 0
                    || $cart->id_address_invoice == 0) 
                {
                    error_log("NOS VAMOS A REDIRECT");
                    Tools::redirect('index.php?controller=order&step=1');
                }
                /** Validamos Objeto cliente **/
                $customer = new Customer((int)$cart->id_customer);
                
                /** Donet **/
                Context::getContext()->customer = $customer;
                $address = new Address((int)$cart->id_address_invoice);
                Context::getContext()->country = new Country((int)$address->id_country);
                Context::getContext()->customer = new Customer((int)$cart->id_customer);
                Context::getContext()->language = new Language((int)$cart->id_lang);
                Context::getContext()->currency = new Currency((int)$cart->id_currency);
                
                /** VALIDACIONES DE DATOS y LIBRERÍA **/
                //Total
                $totalCart = $cart->getOrderTotal(true, Cart::BOTH);
                $totalOrig = number_format($totalCart, 2, '', '');
                
                // ID Moneda interno
                $currencyOrig = new Currency($cart->id_currency);
                // ISO Moneda
                $monedaOrig = $currencyOrig->iso_code_num;
                // DsResponse
                $respuesta = (int)$respuesta;

                if ($monedaOrig == $moneda && $totalOrig == $total && (int)$codigoOrig == (int)$codigo && $respuesta < 101 && checkAutCode($id_trans)) {
                    /** Compra válida **/
                    $mailvars['transaction_id'] = (int)$id_trans;
                    $redsys->validateOrder($pedido, _PS_OS_PAYMENT_, $totalOrig/100, $redsys->displayName, null, $mailvars, (int)$cart->id_currency, false, $customer->secure_key);
                    Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
                } else {
                    if (!($monedaOrig == $moneda)) {
                        error_log(" -- "."La moneda no coincide. ($monedaOrig : $moneda)");
                    }
                    if (!($totalOrig == $total)) {
                        error_log(" -- "."El importe total no coincide. ($totalOrig : $total)");
                    }
                    if (!((int)$codigoOrig == (int)$codigo)) {
                        error_log(" -- "."El código de comercio no coincide. ($codigoOrig : $codigo)");
                    }
                    if (!checkAutCode($id_trans)){
                        error_log(" -- "."Ds_AuthorisationCode inválido. ($id_trans)");
                    }
                    if ($error_pago=="no"){
                        /** se anota el pedido como no pagado **/
                        $redsys->validateOrder($pedido, _PS_OS_ERROR_, 0, $redsys->displayName, 'errores:'.$respuesta);
                    }
                    error_log(" -- "."El pedido con ID de carrito " . $pedido . " es inválido.");
                }
            } else {
                if ($accesoDesde === 'POST') {
                    if (!($firma_local === $firma_remota)) {
                        error_log(" -- "."La firma no coincide.");
                    }
                    if (!checkImporte($total)){
                        error_log(" -- "."Ds_Amount inválido.");
                    }
                    if (!checkPedidoNum($pedido)){
                        error_log(" -- "."Ds_Order inválido.");
                    }
                    if (!checkFuc($codigo)){
                        error_log(" -- "."Ds_MerchantCode inválido.");
                    }
                    if (!checkMoneda($moneda)){
                        error_log(" -- "."Ds_Currency inválido.");
                    }
                    if (!checkRespuesta($respuesta)){
                        error_log(" -- "."Ds_Response inválido.");
                    }
                    if ($error_pago=="no"){
                        /** se anota el pedido como no pagado **/
                        $redsys->validateOrder($pedido, _PS_OS_ERROR_, 0, $redsys->displayName, 'errores:'.$respuesta);
                    }
                    error_log(" -- "."Notificación: El pedido con ID de carrito " . $pedido . " es inválido.");
                } else if ($accesoDesde === 'GET') {
                    Tools::redirect('index.php?controller=order&step=1');
                }
        }
        Tools::redirect($this->context->link->getPageLink('cart', null, null, array('action' => 'show'), true));
    }
}
