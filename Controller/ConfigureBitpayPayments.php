<?php
namespace BitpayPayments\Controller;

use BitpayPayments\BitpayPayments;
use BitpayPayments\Model\BitpayPaymentsConfig;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;

/**
 * Class ConfigureBitpayPayments
 * @package BitpayPayments\Controller
 */
class ConfigureBitpayPayments extends BaseAdminController
{
    public function configure()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('BitpayPayments'), AccessManager::UPDATE)) {
            return $response;
        }

        $conf = new BitpayPaymentsConfig();
        $one_is_done=0;
        $tab="";

        $form = new \BitpayPayments\Form\ConfigureBitpayPayments($this->getRequest());
        try {
            $vform = $this->validateForm($form);
            $conf->setApiKey($vform->get('apiKey')->getData())
                ->getPairingKey($vform->get('pairingKey')->getData())
                ->write();
            $one_is_done=1;
            $tab="configure_account";
        } catch (\Exception $e) {}

        $form = new \BitpayPayments\Form\ConfigureSandboxBitpayPayments($this->getRequest());
        try {
            $vform = $this->validateForm($form);
            $tab="configure_sandbox";
            $conf->setApiKeySandbox($vform->get('apiKey')->getData())
                ->setPairingKeySandbox($vform->get('pairingKey')->getData())
                ->setSandbox($vform->get('sandbox')->getData() ?"true":"");

                $conf->write();

        } catch (\Exception $e) {}
        //Redirect to module configuration page
        $this->redirectToRoute("admin.module.configure",array(),
            array ( 'module_code'=>BitpayPayments::getModCode(true),
                'current_tab'=>$tab,
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction'));
    }
}
