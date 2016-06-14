
<div class="modal fade" id="myModalInvoice" role="dialog">
<div class="modal-dialog modal-lg">
<div class="modal-content" id="invoiceData">
      <div class="modal-header">


      @if(isset($invData->created_by) && $invData->created_by != Session::get('userId'))
			  <button type="button" onclick="window.location.href='/buyer/chat/{{App\Models\User::find($invData->created_by)->uuid}}\{{$invData->created_by}}'" class="btn btn-defult"><i class="fa fa-envelope fa-lg"></i>Send Message</button>
      @endif
			  <button type="button" id="view-attach" data-toggle="modal" data-target="#attachModal" class="btn btn-defult"><i class="fa fa-paperclip fa-lg"></i> Attachment</button>
        <button type="button" class="btn btn-defult" id="print-invoice" ><i class="fa fa-print fa-lg"></i> Print</button>
          @permission('buyer.invoice.approve')
            @if($invData->status!=5 && $invData->status!=7 && $invData->status!=8) 
              <button type="button" data-toggle="modal" data-target="#confirmApproveMoadal" class="btn btn-defult"><i class="fa fa-check-circle fa-lg"></i> Approve</button>
              <button type="button" data-toggle="modal" data-target="#confirmRejectMoadal" class="btn btn-defult"><i class="fa fa-times-circle fa-lg"></i> Reject</button>
            @endif
          @endpermission
          <button type="button" class="close" data-dismiss="modal">&times;</button>
  			  @if($invData->status==5 || $invData->status==7) 
              <button type="button" data-toggle="modal" data-target="#changeDateModal" class="btn btn-defult"><i class="fa fa-pencil-square-o fa-lg"></i> Change Due Date</button>
          @endif
			</div>
      
			<div class="modal-body">
			<h1 style="font-size:20px;">
       Details </h1>
  			@if(!empty($invData->currency))
         <?php 
         $code=$invData->currency;
         $symbol=$currencyData[$code]['symbol_native'];
         ?>
        @else
         <?php $symbol="";?>
        @endif
			  <table class="table popup_table">
			  <tbody>
				  <tr>
  					<th>Invoice No.</th>
  					<td>{{$invData->invoice_number}}</td>
  					<td rowspan="3" width="25%" style="vertical-align:top">
               <b>Buyer</b><br> 
               {{$invData->buyerName}}<br>
               {{$invData->buyerAddress}}
            </td>
  					<td rowspan="3" width="30%" style="vertical-align:top">
               <b>Delivery Address</b><br> 
               {{$invData->delivery_address}}
  					</td>
				  </tr>	
				  <tr>
  					<th>PO No.</th>
  					<td>{{$invData->purchase_order_number}}</td>
				  </tr>				  
				  <tr>
  					<th>Invoice Date</th>
  					<td>{{date('d M Y',strtotime($invData->created_at))}}</td>
				  </tr>
				  <tr>
  					<th>Status</th>
  					<td>
            {{$statusData['status'][$invData->status]}}
              
            </td>
  					<td rowspan="3" width="25%" style="vertical-align:top">
              <b>Seller</b><br>
              {{$invData->sellerName}}<br>
              {{$invData->sellerAddress}}
            </td>
				  </tr>				  
				  <tr>
  					<th>Amount</th>
  					<td>
              {{ $symbol }}
              {{number_format($invData->final_amount,2)}}
            </td>
  				</tr> 
				  <tr>
  					<th>Due Date</th>
  					<td>{{date('d M Y',strtotime($invData->due_date))}}</td>
				  </tr>			  
				  
				</tbody>
			  </table>
			   <div class="table-responsive">
            <table class="table table-condensed table-striped table-bordered table-hover no-margin">
              <thead>
                <tr>
                  <th>
                    No.
                  </th>

                  <th>
                    Item Name
                  </th>						  
                  <th>
                    Description
                  </th>
                  
                  <th>
                    Quantity
                  </th>
                  <th>
                    Price Per Quantity
                  </th>
                  <th>
                    Total
                  </th>
                </tr>
              </thead>
              <tbody>
               <?php $i = 1; ?>
               @foreach($invData->invoiceItems as $item)
                <tr>
	                <td>
                    {{ $i++ }}
                  </td>
                  <td>
                    {{$item->name}}
                  </td>

                  <td>
                    {{$item->description}}
                  </td>
                  <td>
                    {{$item->quantity}}
                  </td>
                  <td style="text-align:right;">
                    {{number_format($item->unit_price,2)}}
                  </td>
                  <td style="text-align:right;">
                    {{number_format($item->total,2)}}
                  </td>
                </tr>
               @endforeach 
               <tr>
                  <td class="total" colspan="4">
                    <b class="pull-right">Subtotal</b>
                  </td>
                  <td>&nbsp;</td>
                  <td style="text-align: right;">
                    {{ $symbol." ".number_format($invData->amount,2)}}
                  </td>
                </tr>
                <tr>
                  <td class="total" colspan="4">
                    <b class="pull-right">Discount</b>
                  </td>
                  
                  @if($invData->discount_type==1)
                   <td style="text-align: right;" colspan="2">
                    {{ $symbol." ".number_format($invData->discount,2)}}
                   </td>
                  @else
                  <td style="text-align: right;">
                  {{ $invData->discount }}%
                  </td>
                  <td style="text-align: right;">
                  <?php $discountAmount = ($invData->amount*$invData->discount)/100; ?>
                  {{ $symbol." ".number_format($discountAmount,2) }}
                  </td>
                  @endif
                  
                </tr>
                @if(isset($invData->tax_details) && !empty($invData->tax_details))
                @foreach(json_decode($invData->tax_details) as $tax)
                  <tr>
                    <td class="total" colspan="4">
                      <b class="pull-right">{{$tax->name}}</b>
                    </td>
                    <td style="text-align:right;">
                      {{$tax->percentage}}%
                    </td>

                    <td style="text-align:right;">
                      {{$symbol." ".number_format($tax->value,2)}}
                    </td>
                  </tr>
                  @endforeach
                @else
                   <tr>
                    <td class="total" colspan="4">
                      <b class="pull-right">Tax</b>
                    </td>
                    <td style="text-align:right;">
                      0.00%
                    </td>
                    <td style="text-align:right;">
                      0.00
                    </td>
                  </tr>
                 @endif
                <tr class="success">
                  <td class="total" colspan="4">
                    <b class="pull-right">Total</b>
                  </td>
                  <td>&nbsp;</td>

                  <td style="text-align:right;">
                    <b>{{ $symbol." ".number_format($invData->final_amount,2)}}</b>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
       </div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
  </div>
  </div>
  </div>
  <input type="hidden" name="diffDays" value="{{$diffDays}}">
  <input type="hidden" name="no_of_days" id="no_of_days" value="{{$bankData['basic_configuration']['no_of_days']}}">
  <input type="hidden" name="max_due_days" id="max_due_days" value="{{$bankData['basic_configuration']['maxDaysForInvoiceDueDate']}}">
  <input type="hidden" name="originalDueDate" id="originalDueDate" value="{{date('d M Y',strtotime($invData->original_due_date))}}">
  
<!-- Approve Invoice Modal Start  -->
     <div class="modal fade" id="attachModal" role="dialog" aria-labelledby="confirmAttachLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Attachments</h4>
          </div>
          <form action="/buyer/invoice/upload" method="post" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <input type="file" name="invoice_attach[]" id="invoiceAttach" multiple>
                <input type="hidden" name="invoice_uuid" value="{{$invData->uuid}}">
                  @if(isset($invData->invoiceAttachments) && count($invData->invoiceAttachments)>0)
                    <div class="jFiler-items jFiler-row">
                     <ul class="jFiler-items-list jFiler-items-default">
                       @foreach($invData->invoiceAttachments as $attch)
                       <li class="jFiler-item" data-jfiler-index="0" style="">
                         <div class="jFiler-item-container">
                            <div class="jFiler-item-inner">
                                <div class="jFiler-item-icon pull-left">
                                  <i class="icon-jfi-file-image jfi-file-ext-jpg"></i>
                                </div>
                                <div class="jFiler-item-info pull-left">
                                   <div class="jFiler-item-title" title="{{$attch->name}}">{{$attch->name}}</div>
                                   <div class="jFiler-item-others"></div>
                                   <div class="jFiler-item-assets">
                                     <ul class="list-inline">
                                       <li>
                                         <a href="../uploads/invoices/{!!$attch->name!!}" class="btn btn-default fa fa-download" title="download" download></a>
                                       </li>
                                     </ul>
                                   </div>
                                </div>
                            </div>
                         </div>
                       </li>
                      @endforeach
                     </ul>
                   </div> 
                  @endif
              </div>
            </div>
          </div>
          <div class="modal-footer">
          <input type="hidden" class="route_url">
            <button type="submit" name="submit" class="btn btn-success" id="confirmApprove">Add</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          </form>
        </div>
       </div>
      </div>
     <!-- Approve Invoice Modal end -->
     <!-- Approve Invoice Modal Start  -->

    @if(\Entrust::can('buyer.invoice.checker') || ($configData['maker_checker']['invoice_approval'] == 0))
    <?php 
    if($invData->discount_type==1){
      $subTotal = $invData->amount-$invData->discount;
    } else{
      $subTotal = $invData->amount-($invData->amount*$invData->discount/100);
    }
    ?>
    <div class="modal fade" id="confirmApproveMoadal" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Payment Instruction</h4>
          </div>
          <form class="form-horizontal no-margin" id="tdsIvoice" role="form" method="post" action="/buyer/piListing/store">
          {{ csrf_field() }}
          <div class="modal-body">
               
                <div class="form-group">
                  <label for="subTotal" class="col-sm-4 control-label">Sub Total</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required" id="subTotal" name="sub_total" placeholder="Discount" value="{{ $subTotal}}" readonly>
                  </div>  
                </div>
                <div class="form-group">
                  <label for="tds" class="col-sm-4 control-label">TDS</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required" id="tds" name="tds" placeholder="TDS Percent" value="{{$invData->tax_percentage}}" readonly>
                  </div>  
                </div>

                <div class="form-group">
                  <label for="tds" class="col-sm-4 control-label">Sub Total</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required" id="subTotalDis" name="subTotalDis" readonly>
                  </div>  
                </div>

                
                @if(isset($invData->tax_details) && !empty($invData->tax_details))
                 @foreach(json_decode($invData->tax_details) as $key=>$taxDet)
                  <div class="form-group">
                    <label for="invoice_no" class="col-sm-4 control-label">{{$taxDet->name}} {{$taxDet->percentage}}%</label>
                    <!-- <div class="col-sm-2">
                      <input type="text" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required" id="tax" name="tax" placeholder="Tax Percent" value="" readonly>
                    </div> -->
                    <div class="col-sm-4">
                      <input type="text" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required tax" name="tax" placeholder="Tax Percent" readonly value="{{$taxDet->value}}">
                      <input type="hidden" name="taxt_value" class="taxes" value="@if(!empty($taxDet->percentage)){{ $taxDet->percentage }} @else 0.00 @endif">
                    </div>   
                  </div>
                
                @endforeach
                @endif
                
                <div class="form-group">
                  <label for="invoice_no" class="col-sm-4 control-label">Total</label>
                  <div class="col-sm-4">
                    <input type="text" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required" id="finalAmount" name="final_amount" placeholder="Total" readonly>
                  </div>  
                </div>
                <input type="hidden" name="seller_id" value="{{$invData->sellerUuid}}">
                <input type="hidden" name="invoice_id" value="{{$invData->uuid}}">
          </div>
          <div class="modal-footer">
          <input type="hidden" class="route_url">
            <button type="submit" class="btn btn-success" id="confirmApprove">Approve</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          </form>
        </div>
       </div>
      </div>
     @else
      <div class="modal fade" id="confirmApproveMoadal" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Approve Invoice</h4>
          </div>
          <div class="modal-body">
            <p>Are you sure want to approve this invoice?</p>
          </div>
          <div class="modal-footer">
          <input type="hidden" class="route_url">
            <a href="/buyer/invoice/approve/{{$invData->uuid}}" class="btn btn-success" id="confirmApprove">Approve</a>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            
          </div>
        </div>
       </div>
      </div>
      @endif

     <!-- Approve Invoice Modal end -->
     <!-- Reject Invoice Modal Start  -->
     <div class="modal fade" id="confirmRejectMoadal" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Reject Invoice</h4>
          </div>
          <form class="form-horizontal no-margin ng-valid-parse ng-invalid ng-invalid-required ng-valid-min ng-valid-pattern ng-pristine" role="form" method="post" action="/buyer/invoice/reject">
          {{ csrf_field() }}
          <div class="modal-body">
            <p>Are you sure want to reject this invoice?</p>
            <div class="row">
             <!--  <label for="remarks" class="col-sm-4 control-label">Remark</label> -->
              <div class="col-sm-12">
                <textarea class="form-control" rows="4" id="remarks" name="remarks" placeholder="Remark"></textarea>
                <input type="hidden" name="invoice_uuid" value="{{$invData->uuid}}">
              </div>  
            </div>
          </div>
          <div class="modal-footer">
          <input type="hidden" class="route_url">
            <button type="submit"  class="btn btn-danger">Reject</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          </form>
        </div>
       </div>
      </div>
     <!-- Reject Invoice Modal end -->


      <!-- chat po Modal Start  -->
      <div class="modal fade" id="chatModal" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Chat User</h4>
          </div>
          <form class="form-horizontal no-margin ng-valid-parse ng-invalid ng-invalid-required ng-valid-min ng-valid-pattern ng-pristine" role="form" method="post" action="/buyer/invoice/reject">
          {{ csrf_field() }}
          <div class="modal-body">
            <p>Message to </p>
            <div class="row">
             <!--  <label for="remarks" class="col-sm-4 control-label">Remark</label> -->
              <div class="col-sm-12">
                <textarea class="form-control" rows="4" id="remarks" name="remarks" placeholder="Message"></textarea>
                @if(isset($invData->created_by))
                <input type="hidden" name="user_uuid" value="{{App\Models\User::find($invData->created_by)->uuid}}">
                @endif
              </div>  
            </div>
          </div>
          <div class="modal-footer">
          <input type="hidden" class="route_url">
            <button type="submit"  class="btn btn-danger">Send</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          </form>
        </div>
       </div>
      </div>
     <!-- chat po Modal end -->

     <!-- Change Due Date Modal Start  -->

    <div class="modal fade" id="changeDateModal" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
     <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Change Due Date For Invoice</h4>
        </div>
        <form action="/buyer/invoice/changeDueDate" method="post">
        {{ csrf_field() }}
        <div class="modal-body">
         <?php $flag=0; ?>
          @if($bankData['basic_configuration']['edit_maturity_date']==1)
            @if($diffDays>$bankData['basic_configuration']['no_of_days'] && $diffDays<$bankData['basic_configuration']['maxDaysForInvoiceDueDate'])
              <div class="row">
                <div class="col-sm-3">Due Date</div>
                <div class="col-sm-6">
                    <div class="datepicker"></div>
                    <input type="hidden" name="newDueDate" id="newDueDate" value="{{date('d M Y')}}">
                    <input type="hidden" name="invoiceUuid" id="invoiceUuid" value="{{$invData->uuid}}">
                </div>
            </div>
            <?php $flag=1; ?>
            @else
            <p>This Invoice is discounted by the Seller with the Bank. So, you can't change Invoice due date within the last 15 days of the accepted maturity date.</p>
            @endif

          @else
           <p>This Invoice is discounted by the Seller with the Bank, so you can't change the due date now.</p>
          @endif
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            @if($flag==1)
              <button type="submit" class="btn btn-danger">Submit</button>
            @endif
         </div>
       </form>
      </div>
     </div>
    </div>


     <!-- Add TDS Modal Start 
      <div id="tdsModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
               
        <!-- Modal content
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Tax</h4>
          </div>
          <div class="modal-body">
          <div class="row">
            <div class="col-sm-2">&nbsp;</div>
            <label class="col-sm-4">Name</label>
            <label class="col-sm-4">Percentage</label>
          </div>

          @if(isset($invData->companyConf->tax_configuration))
               @foreach($invData->companyConf->tax_configuration as $name=>$tds)
               <div class="row">
                 <div class="col-sm-2"><input type="radio" class="tax-radio" name="invoice_tax" value="{{$tds}}"></div>
                 <label class="col-sm-4">{{$name}}</label>
                 <label class="col-sm-4">{{$tds}}</label>
               </div>
              @endforeach
           @endif 
         </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
          </div>
        </div>

        </div>
      </div>
      <!-- Add TDS Modal End -->
      
  