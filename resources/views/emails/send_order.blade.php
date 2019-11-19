<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
 <head> 
    <meta charset="UTF-8"> 
    <meta content="width=device-width, initial-scale=1" name="viewport"> 
    <meta name="x-apple-disable-message-reformatting"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta content="telephone=no" name="format-detection"> 

    <title>Your Order</title> 
    
    <style type="text/css">

        .tg  {border-collapse:collapse;border-spacing:0;}
        .tg td{font-size:14px;padding:8px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
        .tg th{font-size:14px;font-weight:normal;padding:8px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
        .tg .tg-37a3{font-size:16px;border-color:#ffffff;text-align:left;vertical-align:top}
        .tg .tg-eaj8{font-size:14px;background-color:#eef4ff;border-color:#ffffff;text-align:left;vertical-align:top}
        .tg .tg-2fdn{width:50%;border-color:#9b9b9b;text-align:left;vertical-align:top}
        .tg .tg-zv4m{border-color:#ffffff;text-align:left;vertical-align:top}
        .tg .tg-k8mx{font-weight:bold;font-size:14px;background-color:#017bb8;color:#ffffff;border-color:#017bb8;text-align:left;vertical-align:top}
        .tg .tg-r598{font-weight:bold;font-size:14px;background-color:#017bb8;color:#ffffff;border-color:#017bb8;text-align:right;vertical-align:top}
        .tg .tg-2hug{font-size:14px;background-color:#eef4ff;border-color:#eef4ff;text-align:right}
        .tg .tg-bcb9{font-weight:bold;font-size:14px;background-color:#eef4ff;border-color:#eef4ff;text-align:left;vertical-align:top}
        .tg .tg-g6cr{font-weight:bold;font-size:14px;border-color:#ffffff;text-align:right;vertical-align:top}
        .tg .tg-az98{font-size:14px;border-color:#ffffff;text-align:right;vertical-align:top}
        .tg .tg-ryr3{font-size:14px;background-color:#eef4ff;border-color:#eef4ff;text-align:center}
        .tg .tg-d1w5{font-weight:bold;font-size:14px;background-color:#017bb8;color:#ffffff;border-color:#017bb8;text-align:center;vertical-align:top}
        .tg .tg-tckn{font-size:14px;border-color:#ffffff;text-align:left;vertical-align:top}
        .tg .tg-iwpf{background-color:#017bb8;color:#ffffff;border-color:#017bb8;text-align:left;vertical-align:top}
        @media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;}}

        /*  PADDING */
        .pt-1{
            padding-top: 1em;
        }
        .pr-1{
            padding-right: 1em;
        }
        .pb-1{
            padding-bottom: 1em;
        }
        .pl-1{
            padding-left: 1em;
        }

        .pt-2{
            padding-top: 2em;
        }
        .pr-2{
            padding-right: 2em;
        }
        .pb-2{
            padding-bottom: 2em;
        }
        .pl-2{
            padding-left: 2em;
        }

        /*  MARGIN */
        .mt-1{
            margin-top: 1em;
        }
        .mr-1{
            margin-right: 1em;
        }
        .mb-1{
            margin-bottom: 1em;
        }
        .ml-1{
            margin-left: 1em;
        }

        .mt-2{
            margin-top: 2em;
        }
        .mr-2{
            margin-right: 2em;
        }
        .mb-2{
            margin-bottom: 2em;
        }
        .ml-2{
            margin-left: 2em;
        }

        .btn{
            width: 50%;
            opacity:1;
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 15px;
            background: #1a9c0c;
            color: #ffffff !important;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn:hover{
            opacity:0.8;
        }

        .dashed-divider{
            border-top: 1px dashed #9c9c9c;
        }

    </style>
    
</head> 

<body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;"> 

    @php 
        $store = $order->store;
        $storeLogo = $order->store->logo->url ?? null;
        $storeSettings = $store->settings['details'];
        $orderSettings = $storeSettings['orderTemplate'];
        $primaryColor = $orderSettings['colors'][0];
        $secondaryColor = $orderSettings['colors'][1]; 
        $currencySymbol = $storeSettings['general']['currency_type']['currency']['symbol'] ?? '';
        $items = $order['item_lines'];

        //  Company Details
        $company = $order['customized_company_details'];
        $companyLogo = $company['logo']['url'];
        $companyPhones = isset($company['phones']) ?? [];

        //  User Billing Details
        $billing_info = $order['billing_info'];
        $billing_name = $billing_info['name'] ?? null;
        $billing_first_name = $billing_info['first_name'] ?? null;
        $billing_last_name = $billing_info['last_name'] ?? null;
        $billing_email = $billing_info['email'] ?? null;
        $billing_additional_email = $billing_info['additional_email'] ?? null;
        $billing_phones = $billing_info['phones'] ?? [];
        $billing_country = $billing_info['country'] ?? null;
        $billing_province = $billing_info['province'] ?? null;
        $billing_city = $billing_info['city'] ?? null;
        $billing_address_1 = $billing_info['address_1'] ?? null;
        $billing_address_2 = $billing_info['address_2'] ?? null;
        $billing_postal_or_zipcode = $billing_info['postal_or_zipcode'] ?? null;

        //  Company Shipping Details
        $shipping_info = $order['shipping_info'];
        $shipping_name = $shipping_info['name'] ?? null;
        $shipping_first_name = $shipping_info['first_name'] ?? null;
        $shipping_last_name = $shipping_info['last_name'] ?? null;
        $shipping_country = $shipping_info['country'] ?? null;
        $shipping_province = $shipping_info['province'] ?? null;
        $shipping_city = $shipping_info['city'] ?? null;
        $shipping_address_1 = $shipping_info['address_1'] ?? null;
        $shipping_address_2 = $shipping_info['address_2'] ?? null;
        $shipping_postal_or_zipcode = $shipping_info['postal_or_zipcode'] ?? null;

    @endphp

    <div style="background:#f2f2f2;padding: 30px 0px;"> 
        <div style="width:80%;margin: auto;padding: 30px 20px;background: #fff;border-radius: 10px;">
            
            <div style="width:80%;margin:20px auto;">

                @if($storeLogo)
                  <img src="{{ $storeLogo }}" style="width:auto;max-height:120px;" class="mb-1">
                @endif

                <h1>{{ $orderSettings['intro_note']['title'] }}</h1>
                
                <p>{{ $orderSettings['intro_note']['description'] }}</p>

                <div class="dashed-divider"></div>

                @if( $mailConfig['attach_bank_details_pdf'] )

                    <h3 class="mt-3">{{ $orderSettings['how_to_pay']['title'] }}</h3>

                    <p>{{ $orderSettings['how_to_pay']['description'] }}</p>

                    <a href="#" class="btn">Attach Proof Of Payment</a>

                    <div class="dashed-divider"></div>

                @endif

                <h1 class="mt-1 mb-1">Order #{{ $order->id }}</h1>

            </div>
                
            <div class="tg-wrap" style="width:80%;margin:20px auto;">

              <table class="tg" style="table-layout: fixed;">
                <colgroup>
                  <col style="width: 613px">
                  <col style="width: 115px">
                  <col style="width: 154px">
                  <col style="width: 380px">
                </colgroup>
                <tr style="background-color:{{ $primaryColor }} !important;">
                  <th class="tg-k8mx">Item</th>
                  <th class="tg-d1w5">Qty</th>
                  <th class="tg-d1w5">Price</th>
                  <th class="tg-r598">Amount</th>
                </tr>
                @foreach($items as $key => $item)
                  <tr style="background-color:{{ ( ($key + 1) % 2 ) ? $secondaryColor . ' !important': '' }} ;">
                    <td class="tg-bcb9">{{ $item['name'] }}</td>
                    <td class="tg-ryr3" rowspan="2">{{ $item['quantity'] }}</td>
                    <td class="tg-ryr3" rowspan="2">{{ $item['unit_regular_price'] }}</td>
                    <td class="tg-2hug" rowspan="2">{{ $item['quantity'] * $item['unit_regular_price'] }}</td>
                  </tr>
                  <tr style="background-color:{{ ( ($key + 1) % 2 ) ? $secondaryColor . ' !important': '' }} ;">
                    <td class="tg-eaj8" 
                        style="border-color:{{ ( ($key + 1) % 2 ) ? $secondaryColor . ' !important': '' }} ;">
                        {{ $item['description'] }}
                    </td>
                  </tr>
                @endforeach
                <tr>
                  <td class="tg-g6cr" colspan="3">Total</td>
                  <td class="tg-az98">{{ $currencySymbol . number_format( $order['cart_total'], 2,",",".") }}</td>
                </tr>
                <tr>
                  <td class="tg-az98" colspan="3"><span style="font-weight:bold">Tax</span></td>
                  <td class="tg-az98">{{ $currencySymbol . number_format( $order['tax_total'], 2,",",".") }}</td>
                </tr>
                <tr>
                  <td class="tg-az98" colspan="3"><span style="font-weight:bold">Grand Total</span></td>
                  <td class="tg-g6cr">{{ $currencySymbol . number_format( $order['grand_total'], 2,",",".") }}</td>
                </tr>
                <tr>
                  <td class="tg-tckn" colspan="4"></td>
                </tr>
                <tr>
                  <td class="tg-37a3" colspan="4"><span style="font-weight:bold">{{ $orderSettings['notes']['title'] }}</span></td>
                </tr>
                <tr>
                  <td class="tg-tckn" colspan="4">{{ $orderSettings['notes']['details'] }}</td>
                </tr>
                <tr>
                  <td class="tg-37a3" colspan="4"><span style="font-weight:bold">Bank Account Details</span></td>
                </tr>
                <tr>
                  <td class="tg-tckn" colspan="4">

                    @if($orderSettings['bank_details']['account_name'])
                    <span style="font-weight:bold">Account Name:</span>
                      {{ $orderSettings['bank_details']['account_name'] }}<br>
                    @endif

                    @if($orderSettings['bank_details']['branch_name'])
                    <span style="font-weight:bold">Branch :</span> 
                      {{ $orderSettings['bank_details']['branch_name'] }}<br>
                    @endif

                    @if($orderSettings['bank_details']['branch_code'])
                    <span style="font-weight:bold">Branch Code:</span> 
                      {{ $orderSettings['bank_details']['branch_code'] }}<br>
                    @endif

                    @if($orderSettings['bank_details']['swift_code'])
                    <span style="font-weight:bold">Swift Code:</span> 
                     {{ $orderSettings['bank_details']['swift_code'] }}<br>
                    @endif

                    @if($orderSettings['bank_details']['account_number'])
                    <span style="font-weight:bold">Account Number:</span> 
                     {{ $orderSettings['bank_details']['account_number'] }}
                    @endif

                  </td>
                </tr>
                <tr>
                  <td class="tg-zv4m" colspan="4"></td>
                </tr>
                <tr>
                  <td class="tg-iwpf" colspan="2">Billing Address</td>
                  <td class="tg-iwpf" colspan="2">Delivery Address</td>
                </tr>
                <tr>
                  <td class="tg-2fdn" colspan="2">
                  
                    @if($billing_name)
                      <span style="font-weight:bold">Name: </span>
                      {{ $billing_name }}<br>
                    @endif

                    @if($billing_first_name)
                      <span style="font-weight:bold">First Name: </span>
                      {{ $billing_first_name }}<br>
                    @endif

                    @if($billing_last_name)
                      <span style="font-weight:bold">Last Name: </span>
                      {{ $billing_last_name }}<br>
                    @endif
                    
                    @if($billing_email)
                      <span style="font-weight:bold">Email: </span>
                      {{ $billing_email }}<br>
                    @endif

                    @if($billing_email)
                      <span style="font-weight:bold">Email 2: </span>
                      {{ $billing_additional_email }}<br>
                    @endif

                    @if($billing_phones)
                      <span style="font-weight:bold">Phone: </span>
                        
                        @foreach($billing_phones as $key => $phone)
                          {{ ($key != 0 ? ', ' : '') . '(+' . $phone['calling_code'] . ') ' . $phone['number'] }} 
                        @endforeach

                        <br>
                    @endif

                    @if($billing_country)
                      <span style="font-weight:bold">Country: </span>
                      {{ $billing_country }}<br>
                    @endif

                    @if($billing_province)
                      <span style="font-weight:bold">Province: </span>
                      {{ $billing_province }}<br>
                    @endif

                    @if($billing_city)
                      <span style="font-weight:bold">City/Town: </span>
                      {{ $billing_city }}<br>
                    @endif

                    @if($billing_address_1)
                      <span style="font-weight:bold">Physical Address: </span>
                      {{ $billing_address_1 }}
                    @endif

                    @if($billing_address_2)
                      <span style="font-weight:bold">Physical Address 2: </span>
                      {{ $billing_address_2 }}
                    @endif

                    @if($billing_postal_or_zipcode)
                      <span style="font-weight:bold">Postal/Zipcode: </span>
                      {{ $billing_postal_or_zipcode }}
                    @endif

                  </td>
                  <td class="tg-2fdn" colspan="2">

                    @if($shipping_name)
                      <span style="font-weight:bold">Name: </span>
                      {{ $shipping_name }}<br>
                    @endif

                    @if($shipping_first_name)
                      <span style="font-weight:bold">First Name: </span>
                      {{ $shipping_first_name }}<br>
                    @endif

                    @if($shipping_last_name)
                      <span style="font-weight:bold">Last Name: </span>
                      {{ $shipping_last_name }}<br>
                    @endif

                    @if($shipping_country)
                      <span style="font-weight:bold">Country: </span>
                      {{ $shipping_country }}<br>
                    @endif

                    @if($shipping_province)
                      <span style="font-weight:bold">Province: </span>
                      {{ $shipping_province }}<br>
                    @endif

                    @if($shipping_city)
                      <span style="font-weight:bold">City/Town: </span>
                      {{ $shipping_city }}<br>
                    @endif

                    @if($shipping_address_1)
                      <span style="font-weight:bold">Physical Address: </span>
                      {{ $shipping_address_1 }}
                    @endif

                    @if($shipping_address_2)
                      <span style="font-weight:bold">Physical Address 2: </span>
                      {{ $shipping_address_2 }}
                    @endif

                    @if($shipping_postal_or_zipcode)
                      <span style="font-weight:bold">Postal/Zipcode: </span>
                      {{ $shipping_postal_or_zipcode }}
                    @endif

                  </td>
                </tr>
              </table>

            </div>

            <div style="width:80%;margin:20px auto 40px;">

                <div class="dashed-divider"></div>

                <a href="#" class="btn">View All Orders</a>

            </div>
            
        </div>
    </div>

</body>

</html>