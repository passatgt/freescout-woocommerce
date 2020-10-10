<form class="form-horizontal margin-top" method="POST" action="">
    {{ csrf_field() }}

		<div class="descr-block">
				{{ __("Load order and customer information on the sidebar based on the customer's e-mail address. Generate the api keys under WooCommerce / Settings / Advanced / REST API") }}
		</div>

    <div class="form-group{{ $errors->has('settings[wc_domain]') ? ' has-error' : '' }}">
        <label for="wc_domain" class="col-sm-2 control-label">{{ __('Domain') }}</label>

        <div class="col-sm-6">
            <input id="wc_domain" type="text" class="form-control input-sized" name="settings[wc_domain]" value="{{ old('settings[wc_domain]', $settings['wc_domain']) }}" maxlength="60" required autofocus>

            @include('partials/field_error', ['field'=>'settings.wc_domain'])

						<p class="form-help">
								{{ __('Make sure you include a / at the end. For example: https://example.com/') }}
						</p>

        </div>
    </div>

		<div class="form-group{{ $errors->has('settings[wc_public_key]') ? ' has-error' : '' }}">
        <label for="wc_public_key" class="col-sm-2 control-label">{{ __('Public Key') }}</label>

        <div class="col-sm-6">
            <input id="wc_public_key" type="text" class="form-control input-sized" name="settings[wc_public_key]" value="{{ old('settings[wc_public_key]', $settings['wc_public_key']) }}" maxlength="60" required autofocus>

            @include('partials/field_error', ['field'=>'settings.wc_public_key'])
        </div>
    </div>

		<div class="form-group{{ $errors->has('settings[wc_private_key]') ? ' has-error' : '' }}">
        <label for="wc_private_key" class="col-sm-2 control-label">{{ __('Private Key') }}</label>

        <div class="col-sm-6">
            <input id="wc_private_key" type="text" class="form-control input-sized" name="settings[wc_private_key]" value="{{ old('settings[wc_private_key]', $settings['wc_private_key']) }}" maxlength="60" required autofocus>

            @include('partials/field_error', ['field'=>'settings.wc_private_key'])
        </div>
    </div>

    <div class="form-group margin-top">
        <div class="col-sm-6 col-sm-offset-2">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>
