@if ($disputes->count() > 0)
  <div class="table-responsive border_none-s dispute_table-s">
    <table class="table" id="buyer-order-table">
      <thead>
        {{-- <tr>
          <th colspan="3">@lang('theme.disputes')</th>
        </tr> --}}
      </thead>
      @foreach ($disputes as $dispute)
      <tbody>
          <tr class="order-info-head">
            <td class="py-4 pl-5 mw_30rem-s">
              <h5 class="mb-2 fg_light_blue-s"><span class="">@lang('theme.order_id'): </span>{{ $dispute->order->order_number }}</h5>
              <h5><span>@lang('theme.order_time_date'): </span>{{ $dispute->order->created_at->toDayDateTimeString() }}</h5>
            </td>
            <td width="" class="store-info py-4 mw_14rem-s">
              <h5 class="mb-2 ">
                <span>@lang('theme.store'):</span>
                @if ($dispute->shop->slug)
                  <a class="fg_light_blue-s" href="{{ route('show.store', $dispute->shop->slug) }}"> {{ $dispute->shop->name }}</a>
                @else
                  @lang('theme.seller')
                @endif
              </h5>
              <h5>
                <span>@lang('theme.status')</span>
                {!! $dispute->order->dispute->statusName() !!}
              </h5>
            </td>
            <td width="" class="order-amount py-4 pr-xl-0 pr-5 text-center mw_16rem-s">
              <h5 class="mb-2"><span>@lang('theme.order_amount'): </span>{{ get_formated_currency($dispute->order->grand_total, true, 2) }}</h5>
              <div class="btn-group" role="group">
                <a class="btn btn-xs btn-default flat br_4px-s px-2  fg_black-s" href="{{ route('order.detail', $dispute->order) }}">@lang('theme.button.order_detail')</a>
                <a class="btn btn-xs btn-default flat ml-2 br_4px-s px-2 fg_black-s" href="{{ route('order.detail', $dispute->order) . '#message-section' }}">@lang('theme.button.contact_seller')</a>
              </div>
            </td>
          </tr> <!-- /.order-info-head -->
          @foreach ($dispute->order->inventories as $item)
            <tr class="order-body">
              <td colspan="2" class="mw_44rem-s pl-5">
                <div class="product-img-wrap">
                  <img src="{{ get_storage_file_url(optional($item->image)->path, 'small') }}" alt="{{ $item->slug }}" title="{{ $item->slug }}" />
                </div>
                <div class="product-info">
                  <a href="{{ route('show.product', $item->slug) }}" class="product-info-title">{{ $item->pivot->item_description }}</a>
  
                  <div class="order-info-amount">
                    <span>{{ get_formated_currency($item->pivot->unit_price, true, 2) }} x {{ $item->pivot->quantity }}</span>
                  </div>
                  {{-- <ul class="order-info-properties">
                              <li>Size: <span>L</span></li>
                              <li>Color: <span>RED</span></li>
                          </ul> --}}
                  @if ($dispute->product_id == $item->product_id)
                    <span class="label label-danger">@lang('theme.disputed')</span>
                  @endif
                </div>
              </td>
              @if ($loop->first)
                <td rowspan="{{ $loop->count }}" class="order-actions text-center px-5 mw_18rem-s">
                  @if ($dispute->order->refunds->count())
                    <a href="{{ route('order.detail', $dispute->order) . '#refund-detail-section' }}" class="btn btn_red-s br_4px-s btn-sm btn-block ">@lang('theme.refund_details')</a>
                  @endif
  
                  <a href="{{ route('dispute.open', $dispute->order) }}" class="btn btn-default btn-sm btn-block br_4px-s">{!! trans('theme.dispute_details') !!}</a>
  
                  @if ($dispute->isOpen())
                    {!! Form::open(['route' => ['dispute.markAsSolved', $dispute]]) !!}
                    {!! Form::button(trans('theme.mark_as_solved'), ['type' => 'submit', 'class' => 'confirm btn btn_red-s br_4px-s btn-block btn-sm ']) !!}
                    {!! Form::close() !!}
                  @endif
                </td>
              @endif
            </tr> <!-- /.order-body -->
          @endforeach
  
          @if ($dispute->order->message_to_customer)
            <tr class="message_from_seller">
              <td colspan="3">
                <p>
                  <strong>@lang('theme.message_from_seller'): </strong> {{ $dispute->order->message_to_customer }}
                </p>
              </td>
            </tr>
          @endif
  
          @if ($dispute->order->buyer_note)
            <tr class="order-info-footer">
              <td colspan="3" class="px-5 py-4">
                <p class="order-detail-buyer-note">
                  <span>@lang('theme.note'): </span> {{ $dispute->order->buyer_note }}
                </p>
              </td>
            </tr>
          @endif
        </tbody>
        <div></div>
        @endforeach
    </table>
  </div>
  {{-- <div class="sep"></div> --}}
@else
  <div class="clearfix space50"></div>
  <p class="lead text-center space50">
    @lang('theme.nothing_found')
  </p>
@endif

<div class="row pagenav-wrapper">
  {{ $disputes->links('theme::layouts.pagination') }}
</div><!-- /.row .pagenav-wrapper -->
<div class="clearfix space20"></div>
