<?php
namespace Acme\RedsysBundle\Controller;

use Acme\PaymentBundle\Model\PaymentDetails;
use Crevillo\Payum\Redsys\Api;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Registry\RegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PurchaseExamplesController extends Controller
{
    /**
     * @Extra\Route(
     *   "/prepare",
     *   name="acme_redsys_prepare"
     * )
     *
     * @Extra\Template("AcmeRedsysBundle:PurchaseExamples:prepare.html.twig")
     */
    public function prepareAction(Request $request)
    {
        // 4548 8120 4940 0004
        // 12/20
        // 123
        // 123456

        $paymentName = 'redsys';

        $form = $this->createPurchaseForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            $storage = $this->getPayum()->getStorage('Acme\PaymentBundle\Model\PaymentDetails');

            /** @var PaymentDetails */
            $details = $storage->create();
            $details['Ds_Merchant_Amount'] = $data['amount'];
            $details['Ds_Merchant_Currency'] = $data['currencyCode'];
            $details['Ds_Merchant_Order'] = date('ymdHis');
            $details['Ds_Merchant_TransactionType'] = Api::TRANSACTIONTYPE_AUTHORIZATION;
            $details['Ds_Merchant_ConsumerLanguage'] = Api::CONSUMERLANGUAGE_SPANISH;
            $storage->update($details);

            $notifyToken = $this->getTokenFactory()->createNotifyToken($paymentName, $details);
            $details['Ds_Merchant_MerchantURL'] = $notifyToken->getTargetUrl();

            $captureToken = $this->getTokenFactory()->createCaptureToken(
                $paymentName,
                $details,
                'acme_payment_details_view'
            );

            return $this->redirect($captureToken->getTargetUrl());
        }

        return $this->render('AcmePaymentBundle:SimplePurchaseBe2Bill:prepare.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createPurchaseForm()
    {
        return $this->createFormBuilder()
            ->add('amount', 'text', array('required' => false, 'data' => 123))
            ->add('currencyCode', 'text', array('required' => false, 'data' => '978'))

            ->getForm()
        ;
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }
}
