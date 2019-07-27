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
        .tg .tg-2fdn{border-color:#9b9b9b;text-align:left;vertical-align:top}
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
        $storeSettings = $order->store->settings['details'];
        $orderSettings = $storeSettings['orderTemplate'];
        $primaryColor = $orderSettings['colors'][0];
        $secondaryColor = $orderSettings['colors'][1]; 

        $company = $order['customized_company_details'];
        $companyLogo = $company['logo'][0]['url'];
        $client = $order['customized_client_details'];
        $clientName = $client['name'] ?? $client['full_name'];
        $companyPhones = isset($company['phones']) ?? [];
        $clientPhones = isset($client['phones']) ?? [];
        $currencySymbol = $order['currency_type']['currency']['symbol'];
        $items = $order['line_items'];

    @endphp

    <div style="background:#f2f2f2;padding: 30px 0px;"> 
        <div style="width:80%;margin: auto;padding: 30px 20px;background: #fff;border-radius: 10px;">
            
            <div style="width:80%;margin:20px auto;">

                <img src="http://oq-bucket.s3.amazonaws.com/company_logos/wGtBJTYTSqXAJmAR0b2udEcPhUG4kqC6mXaeMUhS.png" 
                     style="width:auto;max-height:120px;" class="mb-1">

                <h1>Thank you for your order</h1>
                
                <p>
                    Your order has been received and is currently being processed. 
                    The order details are show below for your reference. 
                </p>

                <div class="dashed-divider"></div>

                @if( $mailConfig['attach_bank_details_pdf'] )
                    <h3 class="mt-3">How To Pay?</h3>
                    <p>
                        We have attached our bank account details for your reference. Payment can be
                        done via bank deposit, bank transfer or cheque. Make sure to take a picture or
                        to download your receipt. Use this link below to attach your receipt or proof of 
                        payment so that your order is completed. Contact (+267) 75993221 for any 
                        assistance you need. Thank you.
                    </p>

                    <a href="#" class="btn">Attach Proof Of Payment</a>

                    <div class="dashed-divider"></div>

                @endif

                <h1 class="mt-1 mb-1">Order #{{ $order->id }}</h1>

            </div>
                
            <div class="tg-wrap" style="width:80%;margin:20px auto;">

              <table class="tg" style="undefined;table-layout: fixed;">
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
                    <td class="tg-ryr3" rowspan="2">{{ $item['unit_price'] }}</td>
                    <td class="tg-2hug" rowspan="2">{{ $item['quantity'] * $item['unit_price'] }}</td>
                  </tr>
                  <tr style="background-color:{{ ( ($key + 1) % 2 ) ? $secondaryColor . ' !important': '' }} ;">
                    <td class="tg-eaj8">{{ $item['description'] }}</td>
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
                    <span style="font-weight:bold">Account Name:</span>
                      {{ $orderSettings['bank_details']['account_name'] }}<br>
                    <span style="font-weight:bold">Branch :</span> 
                      {{ $orderSettings['bank_details']['branch_name'] }}<br>
                    <span style="font-weight:bold">Branch Code:</span> 
                      {{ $orderSettings['bank_details']['branch_code'] }}<br>
                    <span style="font-weight:bold">Swift Code:</span> 
                     {{ $orderSettings['bank_details']['swift_code'] }}<br>
                    <span style="font-weight:bold">Account Number:</span> 
                     {{ $orderSettings['bank_details']['account_number'] }}
                  </td>
                </tr>
                <tr>
                  <td class="tg-zv4m" colspan="4"></td>
                </tr>
                <tr>
                  <td class="tg-iwpf">Billing Address</td>
                  <td class="tg-iwpf" colspan="3">Delivery Address</td>
                </tr>
                <tr>
                  <td class="tg-2fdn"><span style="font-weight:bold">First Name: </span>Julian<br><span style="font-weight:bold">Last Name: </span>Tabona<br><span style="font-weight:bold">Email: </span>brandontabona@gmail.com<br><span style="font-weight:bold">Phone: </span>(+267) 75993221<br><span style="font-weight:bold">Country: </span>Botswana<br><span style="font-weight:bold">Province: </span>South-East<br><span style="font-weight:bold">City/Town: </span>Gaborone<br><span style="font-weight:bold">Address: </span>Extension 12, Plot 4567</td>
                  <td class="tg-2fdn" colspan="3"><span style="font-weight:bold">First Name: </span>Julian<br><span style="font-weight:bold">Last Name: </span>Tabona<br><span style="font-weight:bold">Country: </span>Botswana<br><span style="font-weight:bold">Province: </span>South-East<br><span style="font-weight:bold">City/Town: </span>Gaborone<br><span style="font-weight:bold">Address: </span>Extension 12, Plot 4567<br></td>
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