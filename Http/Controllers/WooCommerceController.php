<?php

namespace Modules\WooCommerce\Http\Controllers;

use App\Option;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class WooCommerceController extends Controller
{
		public function ajax(Request $request)
		{
				$response = [
					'status' => 'error',
					'msg'    => '', // this is error message
				];

				$user = auth()->user();

				switch ($request->action) {

						// Test sending emails from mailbox
						case 'wc_check_orders':
							$customer_email = $request->customer_email;
							$public_key = Option::get('wc_public_key', \Config::get('app.wc_public_key'));
							$private_key = Option::get('wc_private_key', \Config::get('app.wc_private_key'));
							$domain = Option::get('wc_domain', \Config::get('app.wc_domain'));

							try {
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET' );
								curl_setopt($ch, CURLOPT_URL, $domain.'wp-json/wc/v2/orders?consumer_key='.$public_key.'&consumer_secret='.$private_key.'&search='.$customer_email);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								$html = curl_exec($ch);
								curl_close($ch);
							} catch (\Exception $e) {
								break;
							}

							$response['status'] = 'success';
							$response['data'] = json_decode($html);
							break;

						default:
							$response['msg'] = 'Unknown action';
							break;
				}

				if ($response['status'] == 'error' && empty($response['msg'])) {
					$response['msg'] = 'Unknown error occuredtest';
				}

				return \Response::json($response);
		}
}
