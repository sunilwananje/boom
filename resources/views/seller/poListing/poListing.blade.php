@extends('seller.layouts.default')
@section('sidebar')
<ul>
  <li><a href="" class="heading">Seller PO List </a></li>
</ul>

@stop
@section('content')
<!-- Row Start -->
<div class="row">
  <div class="col-lg-12 col-md-12">
    <div class="widget">
      <div class="widget-header">
        <div class="title">
          Buyer PO List
        </div>                    
      </div>
      <div class="widget-body">
        <form class="form-horizontal ng-pristine ng-valid" role="form" name="search_poi">
          <div class="form-group">
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
              <input type="text" name="search" class="form-control ng-pristine ng-untouched ng-valid" id="serch" ng-model="search" placeholder="Search" value="{{Input::get('search')}}">
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">                          
              <select name="buyer[]" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required lstFruits" multiple="multiple">
               <option value="1" @if((Input::get('buyer'))!=null) @if((in_array('1',Input::get('buyer')))) selected @endif @endif> Pending</option>
               <option value="3" @if((Input::get('buyer'))!=null) @if((in_array('3',Input::get('buyer')))) selected @endif @endif> Accepted</option>
               <option value="4" @if((Input::get('buyer'))!=null) @if((in_array('4',Input::get('buyer')))) selected @endif @endif> Declined</option>
               <option value="5" @if((Input::get('buyer'))!=null) @if((in_array('5',Input::get('buyer')))) selected @endif @endif> Completed</option>
             </select>
           </div>
           <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 demo">  <input type="text" id="config-demo" class="form-control date_filter" name="dateRange" value={{ Input::get('dateRange')}}>
             <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
           </div>
           <div class="col-xs-12 col-sm-6 col-md-3 col-lg-2">  
             <select name="sorting" class="form-control ng-pristine ng-untouched ng-invalid ng-invalid-required lstFruits">
               <option value="">Please Select</option>
               <option value="purchase_order_number-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'purchase_order_number-ASC')) selected @endif> Ascending PO Number</option>
               <option value="purchase_order_number-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'purchase_order_number-DESC')) selected @endif> Descending PO Number</option>
               <option value="final_amount-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'final_amount-ASC')) selected @endif> Ascending PO Amount</option>
               <option value="final_amount-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'final_amount-DESC')) selected @endif> Descending PO Amount</option>
               <option value="compName-ASC" @if(Input::has('sorting') && (Input::get('sorting') === 'compName-ASC')) selected @endif> Ascending Buyer Name</option>
               <option value="compName-DESC" @if(Input::has('sorting') && (Input::get('sorting') === 'compName-DESC')) selected @endif> Descending Buyer Name</option>
             </select>          
           </div>
           <div class="input-group-btn">
            <button type="submit" class="btn btn-sm btn-info mr10" ng-click="searchPoi()"><span class="fa fa-search"></span></button> 
            <button type="button" class="btn btn-sm btn-info" onClick="window.location.href='{{route('seller.poListing.view')}}'" style="margin:0 0 0 10px;"><span class="fa fa-refresh"></span></button>
            <button type="button" class="btn btn-sm btn-info" ng-click="reset()" style="margin:0 0 0 10px;"><i class="fa fa-file-excel-o"></i></button>
          </div>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-responsive table-striped table-bordered table-hover no-margin">
          <thead>
            <tr>
             <th>Buyer</th>
             <th>PO Number</th>           
             <th>PO Amount</th>  
             <th>Amount Invoiced</th>
             <th>Amount Remaining</th>
             <th>PO Status</th>
             <th>Action</th>
           </tr>
         </thead>
         <tbody>

          @if(isset($poList))
            @foreach ($poList as $valpolist)
            <?php $cur = $valpolist->currency;?>
              @if(!empty($cur))
                  <?php $symbol = $currencyData[$cur]['symbol_native']; ?>
              @else
                  <?php $symbol = ""; ?>
              @endif
            <tr>
              <td>{{ $valpolist->compName }}</td>
              <td>
                <a href="javascript:void(0)" data-id="{{$valpolist->uuid}}" data-toggle="modal" 
                class="poView link_A_blue" >{{ $valpolist->purchase_order_number }}</a>
              </td>
              <td style="text-align:right;">
                {!!$symbol!!}{{ number_format($valpolist->final_amount,2) }}
              </td>
              <td style="text-align:right;">
                {!!$symbol!!}{{ number_format($valpolist->sum,2) }}
              </td>
              <td style="text-align:right;">
                {!!$symbol!!}{{ number_format($valpolist->minus,2) }} 
              </td>
              <td style="text-align:left;">
                <i class="{{$statusData['symbols'][$statusData['status'][$valpolist->status]]}}"></i>
                {{$statusData['status'][$valpolist->status]}} 
              </td> 
              <td>
                <a href="#" class="btn btn-success btn-xs" title="Communications">
                  <i class="fa fa-envelope-o"></i>
                </a>                
              </td>                           
            </tr>
           @endforeach
     </tbody>
   </table>
   {!! $poList->appends(Request::only('search'))->render() !!}
   @endif

</div>

</div>
</div>
</div>
</div>
<!-- Row End -->
<div id="poModalContainer"></div>
@include('buyer.includes.deleteModal')
@stop

