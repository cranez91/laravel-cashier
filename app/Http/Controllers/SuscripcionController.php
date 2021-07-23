<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Models\User;

class SuscripcionController extends Controller
{
    public function pago(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $customer = Customer::create(array(
                'email' => $request->stripeEmail,
                'source' => $request->stripeToken
            ));
            $charge = Charge::create(array(
                'customer' => $customer->id,
                'amount' => 1990,
                'currency' => 'usd'
            ));

            return 'Cargo exitoso!';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function processSubscription(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $user = User::find(1);
            //product Id, price Id
            $paymentMethods = $user->paymentMethods();
            $user->newSubscription('prod_Jtz6anBxYigPMu', 'price_1JGB81G5mi1FkQUVlCisP3nV')
                ->add();
            return 'Suscripción exitosa! Acabas de suscribirte al Plan Gold';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function upgradeSubscription(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));    
            $user = User::find(1);    
            //current plan ID and new plan's price ID
            $user->subscription('prod_Jtz6anBxYigPMu')->swap('price_1JGBA0G5mi1FkQUVVujXmFmJ');    
            return 'El Plan ha sido cambiado en forma exitosa!';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function cancelSubscription(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));    
            $user = User::find(1);    
            $user->subscription('prod_Jtz6anBxYigPMu')->cancel();    
            return 'El Plan ha sido cancelado en forma exitosa!';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function invoices()
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $user = User::find(1);        
            $invoices = $user->invoices();
            return view('invoices', compact('invoices'));
        } catch (\Exception $ex) {
                return $ex->getMessage();
        }
    }
    
    public function invoice($invoice_id)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $user = User::find(1);
                
            return $user->downloadInvoice($invoice_id, [
                'vendor'  => 'Zizicom',
                'product' => 'Test Plan',
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
