<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;

use Auth,Input,URL,Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Repository\SellerInterface;
use Repository\BuyerInterface;
use Repository\InvoiceInterface;
use Repository\NotificationInterface;

class BuyerDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $buyerRepo;
    public $notificationRepo;
    public $invoiceRepo;
    public $variousStates = array();

    public function __construct(BuyerInterface $buyerRepo,InvoiceInterface $invoiceRepo,NotificationInterface $notificationRepo)
    {
        $this->buyerRepo                = $buyerRepo;
        $this->invoiceRepo              = $invoiceRepo;
        $this->notificationRepo         = $notificationRepo;
        $this->currencyData             = loadJSON('Common-Currency');
        $this->buyerRepo->buyerId       = session('company_id');
        $this->invoiceRepo->buyerId     = session('company_id');
        $this->variousStates            = loadJSON('variousStatus');
        $this->buyerRepo->loggedInUser  = session('userId');
    }

    public function index()
    {
        $this->invoiceRepo->statusArray = [1, 3, 4, 5, 6];
        $this->invoiceRepo->invoiceLimit = 5;
        $this->notificationRepo->toId = session('userId');
        $this->notificationRepo->objectType = 'invoice';
        $notificationData = $this->notificationRepo->getNotifications();
        $this->notificationRepo->objectType = 'po';
        $poNotification = $this->notificationRepo->getNotifications();
        $this->notificationRepo->objectType = 'pi';
        $piNotification = $this->notificationRepo->getNotifications();
        $this->notificationRepo->objectType = 'chat';
        $chatNotification = $this->notificationRepo->getNotifications();

        $invoiceData = $this->invoiceRepo->getInvoices();
        
        $currencyData = $this->currencyData;
        $statusData['status'] = $this->variousStates['INVOICE']['Buyer'];
        $statusData['symbols'] = $this->variousStates['SYMBOLS'];

        /*PO RELATED LOGIC*/
        $this->buyerRepo->po = true; //this is for accessing po related functions from BuyerRepo
        $this->buyerRepo->poLimit = 5;
        $poList = $this->buyerRepo->getData();
        
        $poStatusData['status'] = $this->variousStates['PO']['Buyer'];
        $poStatusData['symbols'] = $this->variousStates['SYMBOLS'];
        $poCurrencyData = $this->currencyData;
        return view('buyer/dashboard/dashboard',compact('invoiceData','currencyData','statusData','poList','poCurrencyData','poStatusData','notificationData','poNotification','piNotification','chatNotification'));
    }

    public function showDashData()
    {
        $this->buyerRepo->viewStatus['openPO'] = array('table' =>'purchase_orders', 'column'=>'final_amount', 'status'=>array(0,1,3));
        $this->buyerRepo->viewStatus['openInv'] = array('table' =>'invoices', 'column'=>'final_amount', 'status'=>array(1,3,5,7));
        $this->buyerRepo->viewStatus['AppPI'] = array('table' =>'payment_instructions', 'column'=>'net_pi_amount', 'status'=>array(1,3));
        $result = $this->buyerRepo->dashboardData();
        return Response::json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
