<?php
namespace Repository\Eloquent;
use Repository\NotificationInterface;
use App\Models\Notification;
use App\Models\User;
use Session,Uuid,DB,Schema;
class NotificationRepo implements NotificationInterface
{

	public $loggedInUser;
    public $buyerId;
    public $sellerId;
    public $fromId;
	public $toId;
	public $uuid;
	public function getNotifications(){
        $query = Notification::select('notifications.*')->where('to_id',$this->toId);
        if($this->objectType == 'invoice'){
          $query->addSelect('invoices.uuid as invoice_uuid');
          $query->join('invoices','invoices.id','=','object_id');
        }elseif($this->objectType == 'po'){
          $query->addSelect('po.uuid as po_uuid');
          $query->join('purchase_orders as po','po.id','=','object_id');
        }elseif($this->objectType == 'pi'){
          $query->addSelect('pi.uuid as pi_uuid');
          $query->join('payment_instructions as pi','pi.id','=','object_id');
        }
        $query->where('is_read','0');
        $result =  $query->get();
        return $result;
	}

	public function getNotificationById(){

	}

	public function saveNotification($data){
	 
     $notificationColumns = Schema::getColumnListing('notifications');
     /*$userData = new UserRepo();
     $userData->companyId = $data['company_id'];
     $resultArray = $userData->getData();*/
     if(isset($data['company_id'])){
       
         $query = User::join('companies', 'users.company_id', '=', 'companies.id');
         $query->where('companies.id', '=', $data['company_id']);
         $query->select('users.id as user_id','users.name as user_name', 'companies.name as compny_name', 'users.status', 'users.email', 'users.uuid', 'users.user_type', 'users.role_id');
         $resultArray = $query->get();
     
         foreach($resultArray as $k=>$user){
            $notifiication = new Notification();
            $notifiication->uuid = Uuid::generate();
           foreach ($data as $key => $value) {
                    if (in_array($key, $notificationColumns)) {
                        $notifiication->$key = $value;
                    }
                }
           $notifiication->to_id = $user->user_id;
           if($user->user_id!=$data['from_id']){
             $result = $notifiication->save();
           }
           

         }
       }else{
           $notifiication = new Notification();
           $notifiication->uuid = Uuid::generate();
           foreach ($data as $key => $value) {
                    if (in_array($key, $notificationColumns)) {
                        $notifiication->$key = $value;
                    }
                }

           $result = $notifiication->save();
       }
     
     return $result;
	}

    public function readNotification($id){
        $result = Notification::where('uuid', $id)
                  ->where('to_id', $this->toId)
                  ->update(['is_read' => 1]);
        return $result;
    }
	public function getData(){
        
	}

	public function save($data){

	}
}
?>