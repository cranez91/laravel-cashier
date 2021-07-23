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

    public function procesa_suscripcion(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $user = User::find(1);
            //product Id, price Id
            $user->newSubscription('prod_Jtz6anBxYigPMu', 'price_1JGB81G5mi1FkQUVlCisP3nV');
            return 'Suscripción exitosa! Acabas de suscribirte al Plan Gold';
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
