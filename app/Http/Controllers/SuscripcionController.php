<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Models\User;
use Stripe\Checkout\Session;
use Laravel\Cashier\Cashier;
use Stripe\StripeClient;

class SuscripcionController extends Controller
{
    public function payment(Request $request)
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

    public function createCheckoutSession(Request $request)
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        Stripe::setApiKey(config('services.stripe.secret'));
            
        // The price ID passed from the front end.
        //   $priceId = $_POST['priceId'];
        $priceId = $request->priceId;
            
        $session = Session::create([
            'success_url' => 'https://example.com/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://example.com/canceled',
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $priceId,
                // For metered billing, do not pass quantity
                'quantity' => 1,
            ]],
        ]);

        return redirect($session->url);
    }

    /*public function processSubscription(Request $request)
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
    }*/

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
                'vendor'  => 'LUIS ENTERPRISES',
                'product' => 'Test Plan',
            ]);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }



// **********************************************************************
// **********************************************************************
// **********************************************************************

    public function index()
    {
        return view('subscription.create');
    }

    public function orderPost(Request $request)
    {
        $user = User::find(1);
        $input = $request->all();
        $token =  $request->stripeToken;
        $paymentMethod = $request->paymentMethod;

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            
            if (is_null($user->stripe_id)) {
                $stripeCustomer = $user->createAsStripeCustomer();
            }
            
            Customer::createSource(
                $user->stripe_id,
                ['source' => $token]
            );

            $user->newSubscription('test', $input['plane'])
                ->create($paymentMethod, [
                    'email' => $user->email,
                ]);

            return back()->with('success','Subscription is completed.');
        } catch (Exception $e) {
            return back()->with('success',$e->getMessage());
        }
    }

// **********************************************************************
// **********************************************************************
// **********************************************************************


    public function retrievePlans() {
        $key = config('services.stripe.secret');
        $stripe = new StripeClient($key);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;
        
        foreach($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,[]
            );
            $plan->product = $prod;
        }
        return $plans;
    }

    public function showSubscription() {
        $plans = $this->retrievePlans();
        $user = User::find(1);
        
        return view('subscription.subscribe', [
            'user'=> $user,
            'intent' => $user->createSetupIntent(),
            'plans' => $plans
        ]);
    }

    public function processSubscription(Request $request)
    {
        $user = User::find(1);
        $paymentMethod = $request->input('payment_method');
                   
        $user->createOrGetStripeCustomer();
        $user->addPaymentMethod($paymentMethod);
        $plan = $request->input('plan');       
        
        try {
            $user->newSubscription('default', $plan)
                ->create($paymentMethod, [
                    'email' => $user->email
                ]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }
       
        return redirect('dashboard');
    }
}
