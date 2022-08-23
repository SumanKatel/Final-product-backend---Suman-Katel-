<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\Front\CampaignUsedByCustomer;
use App\Model\Front\Coupon;
use App\Model\Front\ProductCart;
use App\Model\Workstation\Campaign;
use App\Model\Workstation\Customer;
use App\Model\Workstation\Product;
use App\Model\Workstation\ProductOrder;
use App\Model\Workstation\WorkstationUser;
use App\Model\admin\CreditStore;
use App\Model\admin\CustomerSupport;
use App\Model\admin\CustomerSupportFile;
use App\Model\admin\Medium;
use App\Model\admin\SupportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{

    public function promotionCheck(Request $request)
    {
        $customerId = $request->customerId;
        $customer=Customer::find($customerId);
            if ($customer) {
            $validator=Validator::make($request->all(), [
            'offer_code'  => 'required',
            ]);
            if ($validator->passes()) {

            $nowDate = date('Y-m-d H:i:s');
            $offercheckMatch = Campaign::where('offer_code',$request->offer_code)->where('is_coupon',1)->first();
            if ($offercheckMatch) {
            $offercheck = Campaign::where('offer_code',$request->offer_code)->where('date_to','>=',$nowDate)->where('is_coupon',1)->first();
            if ($offercheck) {
                $checkUsed = CampaignUsedByCustomer::where('coupon_code_id',$offercheck->id)
                                ->where('is_used',1)
                                ->where('user_id',$customer->id)
                                ->first();
                            if ($checkUsed) {
                                $result = array(
                                        'status'        =>  true,
                                        'message'       => 'Coupon Already Used !! ',
                                    );
                                return response()->json($result,401);
                            }else{
                                 if ($offercheck->discount_type == 1) {
                                    // fixed amount
                                    $discountPrice = $offercheck->discount_amount;

                                }elseif ($offercheck->discount_type == 2) {

                                    // percentage
                                    $discountPrice = $offercheck->discount_percentage;
                                    
                                }
                                    $value = array(
                                        'discount_type' => $offercheck->discount_type,
                                        'discountPrice' => $discountPrice,
                                        'offer_code'    => $offercheck->offer_code,
                                    );

                                $result = array(
                                        'status'        =>  true,
                                        'message'       => 'Coupon Available !! ',
                                        'data'          => $value,
                                    );
                                return response()->json($result,200);
                            }
                }else{
                    $result = array(
                            'status'        =>  false,
                            'message'       => 'Coupon Expired !! ',
                        );
                    return response()->json($result,401);
                }
                }else{
                    $result = array(
                            'status'        =>  false,
                            'message'       => 'Coupon Not Match !! ',
                        );
                    return response()->json($result,401);
                }


                }else{
                    $result = array(
                    'status'        => false,
                    'message'       => 'Input Field Required',
                    'data'        => $validator->errors()
                    );
                    return response()->json($result,422);
                }
            }else{
                return response()->json([
                    'status'     => false,
                    'message'    => 'User Not Found',
                    'data'       =>null,
                ], 404);
            }
    }


    
}
