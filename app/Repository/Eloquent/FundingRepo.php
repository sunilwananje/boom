<?php
namespace Repository\Eloquent;

use Mail, Validator, Crypt, Request, Input;
use Uuid, Session;
use Repository\FundingInterface;
use App\Models\Company;
use DB,URL;

class FundingRepo implements FundingInterface
{
  public $buyerId = array();
  public $sellerId;
  public $message;
  public function getfundinglimitview()
  {
    $fundinglimits = DB::table('companies')
    					            ->join('users','contact_user_id','=','users.id') 	
                          ->join('roles','users.role_id','=','roles.id')
                          ->leftJoin('payment_instructions as pi', function ($join) {
                                $join->on('pi.seller_id','=','companies.id')->orOn('pi.buyer_id','=','companies.id');
                            })
                          ->leftjoin('discountings as d','d.pi_id','=','pi.id')
                          ->selectRaw('IFNULL(sum(d.loan_amount),0) as Total, companies.name , roles.type ,companies.id as compID , companies.buyer_limit,companies.seller_limit,pi.seller_id,pi.buyer_id')
                          ->WhereIn('roles.type',[roleId('Buyer'),roleId('Both'),roleId('Seller')])
                          // ->whereIn('d.status',[0,1])
                          ->groupBy('companies.id')
                          ->orderBy('roles.type');

                          if(Input::has('rolesearch')){
                           $rolesearch=Input::get('rolesearch');
                           $fundinglimits->Where('companies.name','like',"%$rolesearch%");
                          }
                           $fundinglimits = $fundinglimits->get();

   return $fundinglimits;
                          
 }
                          
                        
 

  public function savefundinglimit($request)
   {

     $savefundlimit=company::find($request->companyId);
       if($request->buyer_approved_limit){
          $savefundlimit->buyer_limit = $request->buyer_approved_limit;
        }
       elseif($request->seller_approved_limit){
          $savefundlimit->seller_limit=$request->seller_approved_limit;
        }
     
        $savefundlimit=$savefundlimit->save();
   }     
    

   
      
    public function pipelineRequest()
    {
       $queryBuyer =  DB::table('companies')
                          ->join('users','contact_user_id','=','users.id')  
                          ->join('roles','users.role_id','=','roles.id')
                          ->leftJoin('payment_instructions as pi', 'pi.buyer_id','=','companies.id')
                          ->leftjoin('discountings as d','d.pi_id','=','pi.id')
                          ->selectRaw('IFNULL(sum(d.loan_amount),0) as total, companies.name , roles.type ,companies.id as compID , companies.buyer_limit,companies.seller_limit,pi.seller_id,pi.buyer_id')
                          ->whereIn('d.status',[1,3]);

        if($this->buyerId){ //this is for buyer company 
            $pipeline_request['buyer'] = $queryBuyer->whereIn('companies.id', $this->buyerId)
                                          ->groupBy('companies.id')
                                          ->lists('total','compID');
        }
        else{ //  this is for all companies if login not buyer
            $pipeline_request['buyer'] = $queryBuyer->groupBy('companies.id')
                                        ->lists('total','compID');
        }
                                      
    
      $queryBuyer =  DB::table('companies')
                          ->join('users','contact_user_id','=','users.id')  
                          ->join('roles','users.role_id','=','roles.id')
                          ->leftJoin('payment_instructions as pi', 'pi.seller_id','=','companies.id')                                      
                          ->leftjoin('discountings as d','d.pi_id','=','pi.id')
                          ->selectRaw('IFNULL(sum(d.loan_amount),0) as total, companies.name , roles.type ,companies.id as compID , companies.buyer_limit,companies.seller_limit,pi.seller_id,pi.buyer_id')
                          ->whereIn('d.status',[1,3]);
        if($this->sellerId){ //this is for seller company 
            $pipeline_request['seller']  = $queryBuyer->where('companies.id', $this->sellerId)
                                          ->groupBy('companies.id')
                                          ->lists('total','compID');
        }
        else{ //this is for all companies if login not buyer
            $pipeline_request['seller'] = $queryBuyer->groupBy('companies.id')
                                             ->lists('total','compID');
        }

        //dd($pipeline_request);
                    
      return $pipeline_request ;

    }

    public function currentExposure()
    {
       
       $queryBuyer = DB::table('companies')
                        ->join('users','contact_user_id','=','users.id')  
                        ->join('roles','users.role_id','=','roles.id')
                        ->leftJoin('payment_instructions as pi', 'pi.buyer_id','=','companies.id')
                        ->leftjoin('discountings as d','d.pi_id','=','pi.id')
                        ->selectRaw('IFNULL(sum(d.loan_amount),0) as total, companies.name , roles.type ,companies.id as compID , companies.buyer_limit,companies.seller_limit,pi.seller_id,pi.buyer_id')
                        ->whereIn('d.status',[5,7]);  

        if($this->buyerId){
            $currentExposure['buyer'] = $queryBuyer
                                          ->whereIn('companies.id', $this->buyerId)
                                          ->groupBy('companies.id')
                                          ->lists('total','compID');
        }
        else{
            $currentExposure['buyer'] = $queryBuyer->groupBy('companies.id')
                                        ->lists('total','compID');
        }    
                                     
      $querySeller = DB::table('companies')
                        ->join('users','contact_user_id','=','users.id')  
                        ->join('roles','users.role_id','=','roles.id')
                        ->leftJoin('payment_instructions as pi', 'pi.seller_id','=','companies.id')                                      
                        ->leftjoin('discountings as d','d.pi_id','=','pi.id')
                        ->selectRaw('sum(d.loan_amount) as total, companies.name , roles.type ,companies.id as compID , companies.buyer_limit,companies.seller_limit,pi.seller_id,pi.buyer_id')
                        ->whereIn('d.status',[5,7]);

      if($this->sellerId) {
          $currentExposure['seller'] = $querySeller 
                                        ->where('companies.id', $this->sellerId)
                                        ->groupBy('companies.id')
                                        ->lists('total','compID');
      }
      else {
          $currentExposure['seller'] = $querySeller 
                                        ->groupBy('companies.id')
                                        ->lists('total','compID');
      }
                                      
                             //  dd($current_exposure);
                          
      return $currentExposure;

    }

    public function availableLimit($piId)
    {
      $sellerDiscountedAmount = 0;
      $flag = false;
      $buyerLimitMesssage = array();
      $str = "";
     // dd($piId);
      if(!is_array($piId))
        $piId = array($piId);
      //code for getting all PI records for loan amt with buyer id and seller id
      $piRepo = new PiRepo();
      $piRepo->piUuid = $piId;

      //calculate available limit
      $piData = DB::table('pi_view as pv')
                  ->join('company_band_view as cv', function ($join) {
                      $join->on('pv.seller_id', '=', 'cv.seller_id')->on('pv.buyer_id', '=', 'cv.buyer_id');
                  })
                  ->leftJoin('purchase_orders as po', 'pv.po_id', '=', 'po.id')
                  ->select('pv.*', 'cv.*', DB::raw('DATEDIFF(pv.due_date,CURDATE()) as discounting_days,sum(pv.pi_net_amount)*cv.discounting/100 as discounted_amount'), 'po.payment_terms')
                  ->whereIn('pv.pi_uuid', $piId)
                  ->groupBy('pv.buyer_id')
                  ->get();

      $pipeReq =  $this->pipelineRequest(); //calculate pipeline request
      $currReq =  $this->currentExposure(); //calculate current exposure

      //creating buyer id array for checking limit

      foreach ($piData as $key => $value) {
         $this->buyerId[] = $value->buyer_id;
         $sellerDiscountedAmount += $value->discounted_amount;
         $buyerDiscountedAmount[$value->buyer_id] = $value->discounted_amount;
         $buyerLimitMesssage[$value->buyer_id] = $value->buyer_name;
      }

      $sellerLimit = Company::select('id','seller_limit')->where('id', $this->sellerId)->lists('seller_limit','id');  
      $buyerLimit  = Company::select('id','buyer_limit')->whereIn('id', $this->buyerId)->lists('buyer_limit','id');  
      
      //calculate seller available limit 
      $sellerAvailLimit = $sellerLimit[$this->sellerId] - (array_key_exists($this->sellerId,$pipeReq['seller']) ? $pipeReq['seller'][$this->sellerId] : 0) - (array_key_exists($this->sellerId,$currReq['seller']) ? $currReq['seller'][$this->sellerId] : 0);

      if($sellerDiscountedAmount <= $sellerAvailLimit){
        foreach ($buyerLimit as $key => $value) {
          $buyerAvailLimit[$key] = (isset($value) ? $value : 0) - (array_key_exists($key,$pipeReq['buyer']) ? $pipeReq['buyer'][$key] : 0) - (array_key_exists($key,$currReq['buyer']) ? $currReq['buyer'][$key] : 0); 
          $str .= $buyerLimitMesssage[$key]. " : ". $value."<br/>";
          if($buyerDiscountedAmount[$key] > $buyerAvailLimit[$key])
            $flag = true; //if buyer discounted amount is greater than avialable amt 
        }
        if($flag){//this messahge shown after limit is crossed
          $this->message = "One or more of the selected buyer(s) do not have the requested eligible limit.<br/>Kindly unselect those PIs and try again.<br/>Buyer Eligible Limit:<br/>$str";
        }    
      }
      else{
        $flag = true;
        $this->message = "Your Available Limit is already crossed";
      }
      //dd($piData,$this->buyerId,$sellerLimit,$buyerLimit,$pipeReq,$currReq,$sellerAvailLimit,$buyerAvailLimit,$sellerDiscountedAmount,$buyerDiscountedAmount,$this->message); 
      return $flag;
     
    }
    
}