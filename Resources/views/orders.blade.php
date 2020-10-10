<div class="conv-sidebar-block">
	<div class="panel-group accordion accordion-empty">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" href=".collapse-wc-orders">
						{{ __("Recent orders") }}
						<b class="caret"></b>
					</a>
				</h4>
			</div>
			<div class="woocommerce-orders loading collapse-wc-orders panel-collapse collapse in">
				<div class="panel-body">
					<div class="sidebar-block-header2"><strong>{{ __("Recent orders") }}</strong> (<a data-toggle="collapse" href=".collapse-wc-orders">close</a>)</div>
					<ul class="woocommerce-order-list sidebar-block-list" data-not-found="{{ __("Orders not found") }}"></ul>
					<a href="#" class="woocommerce-order-list-reload"><i class="glyphicon glyphicon-refresh"></i> Refresh</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/template" id="wc-order-item-template">
    <li>
			<div class="woocommerce-order-list-row"><a target="_blank" href="{link}">{number}</a> <span>{total}</span></div>
			<div class="woocommerce-order-list-row"><span>{date}</span> <span class="status">{status}</span></div>
    </li>
</script>

@section('javascript')
    @parent
    loadWooCommerceOrders('{{ $customer->getMainEmail() }}', '{{ $domain}}', '{{ $locale }}');
@endsection