<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Cartalyst\Stripe\Stripe;
use DB;

class StripPayment extends Controller
{
    public function index()
    {

    }

    public function StripPayment(Request $request)
    {
        
//        echo "hello";die;
        $validator = $this->getValidationFactory()->make($request->all(), [
            'user_id' => 'required',
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
            'amount' => 'required',
            'access_token' => 'required',
        ]);
//        echo "hello";die;
        $input = $request->all();
        if ($validator->passes()) {
           
//            echo "helllo";die;
            $input = array_except($input, array($request->access_token));
            $stripe = Stripe::make('sk_test_CPwFT29ym87ceXsTSEsfzSRV');
//            echo "hi";die;
            
            try {

//                echo "hello";die;
//                $token = $stripe->tokens()->create([
//                    'card' => [
//                        'number' => $request->get('card_no'),
//                        'exp_month' => $request->get('ccExpiryMonth'),
//                        'exp_year' => $request->get('ccExpiryYear'),
//                        'cvc' => $request->get('cvvNumber'),
//                    ],
//                ]);
                $token = $stripe->tokens()->create([
                    'card' => [
                        'number' => '4242424242424242',
                        'exp_month' => 10,
                        'cvc' => 314,
                        'exp_year' => 2020,
                    ],
                ]);

                $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount' => $request->amount,
                    'description' => 'FootyPicks Payment',
                ]);
                if ($charge['status'] == 'succeeded') {


//                    print_r($charge);
//                    die;

                    $date = date('Y-m-d H:i:s');
                    DB::table('payment_transaction')->insert(
                        array(
                            'user_id' => $request->user_id,
                            'teansaction_id' => $charge['id'],
                            'amount' => $request->amount,
                            'payment_date' => $date,
                            'payment_done' => $charge['paid']
                        )
                    );

                    $sql = "update users set ispremium_membership='true' WHERE id = '" . $request->user_id . "'";
                    DB::update($sql);

                    return $this->sendJson([
                        'flag' => 'true',
                        'Data' => 'Payment Successfully']);

                }
            } catch (Exception $e) {
                return $this->sendJson($e->getMessage());
            }
            catch(\Cartalyst\Stripe\Exception\CardErrorException $e)
            {
                return $this->sendJson($e->getMessage());
            }
            catch(\Cartalyst\Stripe\Exception\MissingParameterException $e)
            {
                return $this->sendJson($e->getMessage());
            }
        }
    }
}