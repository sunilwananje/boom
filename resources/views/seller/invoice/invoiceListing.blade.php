@extends('seller.layouts.default')
@section('sidebar')
	<ul>
		<li><a href="" class="heading"> SELLER INVOICE LIST </a></li>
	</ul>
	<div class="pull-right" style="margin: 0 20px 0 0;">		
		<a href='/seller/invoice/add'><button type="submit" class="btn btn-primary btn-sm">Create Invoice</button></a>
	</div>
@stop
@section('content')
<!-- Row Start -->
<div class="row">
<div class="collapse in" id="collapsible"> 
  <div class="col-lg-4 col-md-6">
    <div class="widget">
      <div class="widget-header">
        <div class="title">
          Payments
        </div>
      </div>
      <div class="widget-body">
        <div id="payment-chart" class="chart-height"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6">
    <div class="widget">
      <div class="widget-header">
        <div class="title">
          Status
        </div>
      </div>
      <div class="widget-body">
        <div id="pie-chart" class="chart-height"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-md-6">
      <div class="widget">
        <div class="widget-header">
          <div class="title">
            Details
          </div>
        </div>
        <div class="widget-body">
          <p style="font-size:22px; color:#999; font-weight:100; font-family: Calibri;">TOTAL OUTSTANDING PAYMENTS <i class="fa fa-question-circle" title="Sum of your outstanding payments across all currencies, converted to your default currency of BDT"></i></a></p>
          <p style="font-size:32px; color:#666; font-weight:100; font-family: Calibri;"> ৳ {{ number_format($outstandingAmount) }}</p> <hr style=" margin:33px 0px; border-bottom:1px solid #cfcfcf; border-top:none;">
          <p style="font-size:22px; color:#999; font-weight:100; font-family: Calibri;">AVERAGE DAYS TO PAYMENT</p>
          <p style="font-size:32px; color:#666; font-weight:100; font-family: Calibri;">24-63</p>
        </div>
      </div>
    </div>
    </div>
    <div class="clearfix"></div>
    <div class="btn-group btn-toggle show_hide_btn col-lg-12 col-md-6 pull-left"> 
      <button class="btn btn-primary active" data-toggle="collapse" data-target="#collapsible">Show Charts</button>
      <button class="btn btn-default" data-toggle="collapse" data-target="#collapsible">Hide Charts</button>
    </div>
</div>


<!-- Row End -->


<!-- Row Start -->
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <div class="widget">
          <div class="widget-header">
            <div class="title">
              Invoice List
            </div>                    
          </div>
          <div class="widget-body">
            <form class="form-horizontal ng-pristine ng-valid" role="form" name="search_poi">
              <div class="form-group">
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">
                  <input type="text" name="search" class="form-control ng-pristine ng-untouched ng-valid" id="serch" ng-model="search" placeholder="Search" value="{{$querystringArray['search']}}">
                </div>
              
    						<div class="col-xs-12 col-sm-6 col-md-3 col-lg-2"> 
    						  <select name="invoiceStatus[]" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required lstFruits" multiple="multiple">
    							  <option value="Created" @if(Input::get('invoiceStatus')!=null) @if(in_array("Created",Input::get('invoiceStatus'))) selected @endif @endif>Created</option>
                    <option value="Submitted" @if(Input::get('invoiceStatus')!=null) @if(in_array("Submitted",Input::get('invoiceStatus'))) selected @endif @endif>Submitted</option>
                    <option value="Internal Reject" @if(Input::get('invoiceStatus')!=null) @if(in_array("Internal Reject",Input::get('invoiceStatus'))) selected @endif @endif>Internal Reject</option>
                    <option value="Approved" @if(Input::get('invoiceStatus')!=null) @if(in_array("Approved",Input::get('invoiceStatus'))) selected @endif @endif>Approved</option>
                    <option value="Rejected" @if(Input::get('invoiceStatus')!=null) @if(in_array("Rejected",Input::get('invoiceStatus'))) selected @endif @endif>Rejected</option>
                    <option value="Partially Paid" @if(Input::get('invoiceStatus')!=null) @if(in_array("Partially Paid",Input::get('invoiceStatus'))) selected @endif @endif>Partially Paid</option>
                    <option value="Paid" @if(Input::get('invoiceStatus')!=null) @if(in_array("Paid",Input::get('invoiceStatus'))) selected @endif @endif>Paid</option>
                  </select>						  
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 demo">	<input type="text" name="invoiceDate" id="config-demo" class="form-control date_filter" value="{{$querystringArray['invoiceDate']}}">
    							<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
    						</div>

                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">  
                   <select name="sorting" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required lstFruits">
                     <option value="">Please Select</option>
                     <option value="purchase_order_number-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'purchase_order_number-ASC')) selected @endif> Ascending PO Number</option>
                     <option value="purchase_order_number-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'purchase_order_number-DESC')) selected @endif> Descending PO Number</option>
                     <option value="invoice_number-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'invoice_number-ASC')) selected @endif> Ascending Invoice Number</option>
                     <option value="invoice_number-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'invoice_number-DESC')) selected @endif> Descending Invoice Number</option>
                     <option value="final_amount-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'final_amount-ASC')) selected @endif> Ascending Invoice Amount</option>
                     <option value="final_amount-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'final_amount-DESC')) selected @endif> Descending Invoice Amount</option>
                     <option value="buyerName-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'buyerName-ASC')) selected @endif> Ascending Seller Name</option>
                     <option value="buyerName -DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'buyerName-DESC')) selected @endif> Descending Seller Name</option>
                     <option value="due_date-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'due_date-ASC')) selected @endif> Ascending Due Date</option>
                     <option value="due_date-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'due_date-DESC')) selected @endif> Descending Due Date</option>
                   </select>          
               </div>

                <div class="input-group-btn">
                  <button type="submit" class="btn btn-sm btn-info mr10" ng-click="searchPoi()"><span class="fa fa-search"></span></button> 
                  <button type="button" class="btn btn-sm btn-info" onclick="window.location.href='/seller/invoice';" style="margin:0 0 0 10px;"><span class="fa fa-refresh"></span></button>
                  <button type="button" class="btn btn-sm btn-info" ng-click="reset()" style="margin:0 0 0 10px;"><i class="fa fa-file-excel-o"></i></button>
                  </div>
                </div>
              </div>
            </form>
            @if(session()->has('nonEditMsg'))
              <div class="alert alert-danger" role="alert">
              {{session('nonEditMsg')}}
              </div>
            @endif

            @if(session()->has('deleteMsg'))
              <div class="alert alert-success" role="alert">
              {{session('deleteMsg')}}
              </div>
            @endif

            <div class="table-responsive">
              <table  id="example01" class="display table table-condensed table-striped table-bordered table-hover no-margin">
                <thead>
                  <tr>
    	              <th>Buyer</th>
                    <th>PO Number</th>
                    <th>Invoice Number</th>
                    <th>Amount</th>
      							<th>Invoice Date</th>
      							<th>Due Date</th>
                    <th>Payment Date</th> 
      							<th>Status</th>
                    <th>Action</th>								
                  </tr>
      			    </thead>
                <tbody>
              
                  @forelse($invoiceData as $invoice)
                  @if($invoice->status == 5 || $invoice->status == 7 || $invoice->status == 8) 
                    <?php $disabled = 'disabled';?>
                  @else
                  <?php $disabled = '';?>
                  @endif

                    <tr>
    	                <td>
                       {{$invoice->buyerName}} 
                      </td>
    	                <td>
                       @if($invoice->purchase_order_number)
                        <a href="javascript:void(0);" data-id="{{$invoice->poUuid}}" data-toggle="modal" class="link_A_blue poView">{{$invoice->purchase_order_number}} <!-- <i class="fa fa-envelope faa-shake animated"></i> --></a>
                       @else
                       -----
                       @endif
                      </td>
                      <td>
                        <a href="javascript:void(0);" id="{{$invoice->uuid}}" class="link_A_blue invoice-modal">{{$invoice->invoice_number}}</a>
                      </td>
                      <td style="text-align: right;">
                         
                        @if(!empty($invoice->currency))
                          <?php $code=$invoice->currency;?>
                        {!! $currencyData[$code]['symbol_native'] !!} 
                        @endif
                        {{ number_format($invoice->final_amount,2) }}
                      </td>
    		
    		              <td>
                       {{ date('d M Y', strtotime($invoice->created_at)) }}
                      </td>
    		
    		              <td>
                       {{ date('d M Y', strtotime($invoice->due_date)) }}       
                      </td>  
    			
    		              <td></td>	
                      <td style="text-align:left;">
                         <i class="{{$statusData['symbols'][$statusData['status'][$invoice->status]] or ''}} wight_ntf"></i>
                         {{ $statusData['status'][$invoice->status] or ''}}
                      </td>
                      <td>
                        
                        <a href="/seller/invoice/edit/{{ $invoice->uuid }}" class="btn btn-warning btn-xs" title="Edit" {!! $disabled !!}>
                          <i class="fa fa-pencil"></i>
                        </a> 
                        

                        <a href="javascript:void(0);" id="/seller/invoice/destroy/{{ $invoice->uuid }}" class="btn btn-danger btn-xs delete-invoice" title="Delete" {!! $disabled !!}>
                          <i class="fa fa fa-trash-o"></i>
                        </a>
                          
                        <a href="#" class="btn btn-success btn-xs" title="Communications">
                          <i class="fa fa-envelope-o"></i>
                        </a>                
                      </td>
    		
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" style="text-align:center"><strong>No More Records</strong></td>
                    </tr>
                @endforelse
                  
                </tbody>
              </table>
            </div>
           {!! $invoiceData->appends($querystringArray)->render() !!}
          </div>
        </div>
      </div>
    </div>
    <!-- Row End -->

    <!--PO Modal Start-->
    <div id="poModalContainer"></div>
    <!--PO Modal End-->

    <!--Invoice Modal Start-->
    <div id="allModals"></div>
    <!-- Invoice Modal End-->

    <!-- Delete Invoice Modal Start  -->
    <div class="modal fade" id="confirmDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Delete Parmanently</h4>
        </div>
        <div class="modal-body">
          <p>Are you sure want to delete this invoice?</p>
        </div>
        <div class="modal-footer">
        <input type="hidden" class="route_url">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a class="btn btn-danger" id="confirm">Delete</a>
        </div>
      </div>
     </div>
    </div>
    
    <?php 
     $pieArray = array();
    ?>
    @foreach($pieChartData as $key=>$value)
       <?php 
         $pieArray[$key]['label'] = "<b>".$value->status_count."</b> ".$statusData['status'][$value->status];
         $pieArray[$key]['data'] = round($value->percentage_of_status,2);
         $pieArray[$key]['amount'] = number_format($value->invoice_amount,2);
       ?>
    @endforeach
    <!-- Delete Invoice Modal end -->
    <script type="text/javascript">
     $(document).ready(function(){
        var $border_color = "#efefef";
        var $grid_color = "#ddd";
        var $default_black = "#666";
        var $green = "#8ecf67";
        var $yellow = "#fac567";
        var $orange = "#F08C56";
        var $blue = "#1e91cf";
        var $red = "#f74e4d";
        var $teal = "#28D8CA";
        var $grey = "#999999";
        var $dark_blue = "#0D4F8B";
        
      $(function () {
         //alert(Math.random());
        var data, chartOptions;
        data = <?php echo json_encode($pieArray);?>;
        /*data = [
          { label: "<b>05</b> Created", data: Math.floor (Math.random() * 100 + 150) }, 
          { label: "<b>12</b> Submitted", data: Math.floor (Math.random() * 100 + 390) }, 
          { label: "<b>0</b> Internal Reject", data: Math.floor (Math.random() * 100 + 530) }, 
          { label: "<b>05</b> Approved", data: Math.floor (Math.random() * 100 + 90) },
          { label: "<b>16</b> Rejected", data: Math.floor (Math.random() * 100 + 320) },
          { label: "<b>20</b> Partially Paid", data: Math.floor (Math.random() * 100 + 90) },
          { label: "<b>40</b> Paid", data: Math.floor (Math.random() * 100 + 320) },
        ];*/
     
        chartOptions = {        
          series: {
            pie: {
              show: true,  
              innerRadius: .5, 
              stroke: {
                width: 1,
              }
            }
          }, 
          shadowSize: 0,
          legend: {
            position: 'se'
          },
          
          tooltip: true,

          tooltipOpts: {
            //content: '%s: %y '
            content: '%s: %y%', // show value to 0 decimals
            
          },
          
          grid:{
            hoverable: true,
            clickable: false,
            borderWidth: 1,
            tickColor: $border_color,
            borderColor: $grid_color,
          },
          shadowSize: 0,
          colors: [$yellow, $yellow, $red, $green, $red, $green],
        };


        var holder = $('#pie-chart');

        if (holder.length) {
          $.plot(holder, data, chartOptions );
        }   
          
      });
     });

    $(function () {

          var data, chartOptions;
          
          data = [
            { label: "৳ <?php echo number_format($receivedAmount);?><br> Received", data: <?php echo $receivedAmount;?> },      
            { label: "৳ <?php echo number_format($outstandingAmount);?><br> Outstanding", data: <?php echo $outstandingAmount;?> }, 
            /*{ label: "Pinaples", data: Math.floor (Math.random() * 100 + 530) }, 
            { label: "Grapes", data: Math.floor (Math.random() * 100 + 90) },
            { label: "Bananas", data: Math.floor (Math.random() * 100 + 320) }*/
          ];

          chartOptions = {        
            series: {
              pie: {
                show: true,  
                innerRadius: .5, 
                stroke: {
                  width: 1,
                }
              }
            }, 
            shadowSize: 0,
            legend: {
              position: 'se'
            },
            
            tooltip: true,

            tooltipOpts: {
              //content: '%s: %y'
              content: "%s", // show value to 0 decimals
              shifts: {
                  x: 20,
                  y: 0
              },
              
            },
            
            grid:{
              hoverable: true,
              clickable: false,
              borderWidth: 1,
            tickColor: $border_color,
              borderColor: $grid_color,
            },
            shadowSize: 0,
            colors: [$green, $grey, $yellow, $teal, $yellow, $green],
          };


          var holder = $('#payment-chart');

          if (holder.length) {
            $.plot(holder, data, chartOptions );
          }   
            
        });
    </script>
@stop

