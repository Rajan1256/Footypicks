<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\Models\User;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Cartalyst\Stripe\Stripe;
use DB;
use App\Models\Team;

class StripPayment extends Controller
{
 const TEAMS_FOLDER = 'teams';
    public function index()
    {

    }

 	     public function uploded_img(Request $request)
    {
        $this->validate($request, [
            'cover' => 'required|image|mimes:png|max:2048',
            'id'=>'required',
        ]);



        if ($request->hasFile('cover')) {
            $image = $request->file('cover');
            $name = rand().'.'.$image->getClientOriginalExtension();

           $destinationPath =  Storage::putFileAs(self::TEAMS_FOLDER,$image, $name);

            $image->move($destinationPath, $name);

            $url = env('APP_URL').'/storage/teams/'.$name;
                Team::where('id',$request->id)->update([
                   'id'=>$request->id,
                   'cover'=>$url
                ]);
           // echo env('APP_data').'/storage/teams/'.$name;
           // $this->save();

            return back()->with('success','Image Upload successfully');
        }
    }

    public function StripPayment(Request $request)
    {
//        $validator = $this->getValidationFactory()->make($request->all(), [
//            'user_id' => 'required',
//            'card_no' => 'required',
//            'ccExpiryMonth' => 'required',
//            'ccExpiryYear' => 'required',
//            'cvvNumber' => 'required',
//            'amount' => 'required',
//        ]);
//        $input = $request->all();
        
//        if ($validator->passes()) {
//
//            /*$input = array_except($input, array('tok_1CpaJRLGoyX5kxbkLG6Lvtfm'));*/
//		$stripe = Stripe::make('sk_test_kMogVrMf5L8iJ3h7sZp4BQaU');
//		/*$stripe = Stripe::make('sk_test_CPwFT29ym87ceXsTSEsfzSRV'); */
//
//            try {
//
////                $token = $stripe->tokens()->create([
////                    'card' => [
////                        'number' => $request->get('card_no'),
////                        'exp_month' => $request->get('ccExpiryMonth'),
////                        'exp_year' => $request->get('ccExpiryYear'),
////                        'cvc' => $request->get('cvvNumber'),
////                    ],
////                ]);
//                $token = $stripe->tokens()->create([
//                    'card' => [
//                        'number' => $input['card_no'],
//                        'exp_month' => $input['ccExpiryMonth'],
//                        'cvc' => $input['cvvNumber'],
//                        'exp_year' => $input['ccExpiryYear'],
//                    ],
//                ]);
//
//                $charge = $stripe->charges()->create([
//                    'card' => $token['id'],
//                    'currency' => 'USD',
//                    'amount' => $request->amount,
//                    'description' => 'FootyPicks Payment',
//                ]);
//                if ($charge['status'] == 'succeeded') {


//                    print_r($charge);
//                    die;

                    $date = date('Y-m-d H:i:s');
                    DB::table('payment_transaction')->insert(
                        array(
                            'user_id' => $request->user_id,
                            'product_id' => $request->product_id,
                            'teansaction_id' =>$request->teansaction_id,
                            'payment_recept' => $request->payment_recept,
                            'amount' => $request->amount,
                            'payment_date' => $date,
                            'payment_done' => 1
                        )
                    );

                    if($request->product_id==2)
                    {
                        $sql = "update users set ispremium_membership='true' WHERE id = '" . $request->user_id . "'";
                        DB::update($sql);
                    }


                    return $this->sendJson([
                        'flag' => 'true',
                        'Data' => 'Payment Successfully']);

                }
//            } catch (Exception $e) {
//                return $this->sendJson($e->getMessage());
//            }
//            catch(\Cartalyst\Stripe\Exception\CardErrorException $e)
//            {
//                return $this->sendJson($e->getMessage());
//            }
//            catch(\Cartalyst\Stripe\Exception\MissingParameterException $e)
//            {
//                return $this->sendJson($e->getMessage());
//            }
       // }
   // }
}