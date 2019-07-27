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
        .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
        .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
        .tg .tg-qq6b{font-weight:bold;font-size:14px;font-family:serif !important;;border-color:inherit;text-align:right;vertical-align:top}
        .tg .tg-lj2s{font-size:14px;font-family:serif !important;;border-color:inherit;text-align:right;vertical-align:top}
        .tg .tg-o4sj{font-weight:bold;font-size:14px;font-family:serif !important;;background-color:#017bb8;color:#ffffff;border-color:inherit;text-align:left;vertical-align:top}
        .tg .tg-frhq{font-size:14px;font-family:serif !important;;background-color:#eef4ff;border-color:inherit;text-align:right}
        .tg .tg-a4ox{font-size:14px;font-family:serif !important;;border-color:inherit;text-align:left;vertical-align:top}
        .tg .tg-34fp{font-weight:bold;font-size:14px;font-family:serif !important;;background-color:#017bb8;color:#ffffff;border-color:inherit;text-align:center;vertical-align:top}
        .tg .tg-07vy{font-weight:bold;font-size:14px;font-family:serif !important;;background-color:#017bb8;color:#ffffff;border-color:inherit;text-align:right;vertical-align:top}
        .tg .tg-ezbp{font-weight:bold;font-size:14px;font-family:serif !important;;background-color:#eef4ff;border-color:inherit;text-align:left;vertical-align:top}
        .tg .tg-ws6f{font-size:14px;font-family:serif !important;;background-color:#eef4ff;border-color:inherit;text-align:center}
        .tg .tg-ma6c{font-size:14px;font-family:serif !important;;background-color:#eef4ff;border-color:inherit;text-align:left;vertical-align:top}
        .tg .tg-xdfh{font-size:16px;font-family:serif !important;;border-color:inherit;text-align:left;vertical-align:top}
        @media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;}}
    </style>
    
</head> 

<body> 

    <div class="es-wrapper-color" style="background-color:#F6F6F6;">
        <div>
            <h1>Thank you for your order</h1>
        </div>

        <div>
            
            <p>
                Your order has been received and is currently being processed. 
                The order details are show below for your reference. 
            </p>

            //  Check the mail configurations if we can attach the bank account details PDF
            @if( $mailConfig['attach_bank_details_pdf'] )
                
                <p>
                    We have also attached the bank transfer details for payment. Use this
                    link below to attach your proof of payment so that your order is completed.
                    Contact (+267) 75993221 for any assistance you need.
                </p>

                <a href="#">Upload Proof Of Payment</a>

            @endif
                
            <div>
                <h1>Order #001</h1>
                
                //  ORDER TABLE DETAILS
                <div class="tg-wrap">
                    <table class="tg" style="table-layout: fixed; width: 1204px">
                        <colgroup>
                            <col style="width: 584px">
                            <col style="width: 110px">
                            <col style="width: 147px">
                            <col style="width: 363px">
                        </colgroup>
                        <tr>
                            <th class="tg-o4sj">Services</th>
                            <th class="tg-34fp">Qty</th>
                            <th class="tg-34fp">Price</th>
                            <th class="tg-07vy">Amount</th>
                        </tr>
                        <tr>
                            <td class="tg-ezbp">Rolex Wrist Watch</td>
                            <td class="tg-ws6f" rowspan="2">2</td>
                            <td class="tg-ws6f" rowspan="2">1,300</td>
                            <td class="tg-frhq" rowspan="2">2,600.00</td>
                        </tr>
                        <tr>
                            <td class="tg-ma6c">Stylish x3 series wrist watch</td>
                        </tr>
                        <tr>
                            <td class="tg-qq6b" colspan="3">Total</td>
                            <td class="tg-lj2s">2,600.00</td>
                        </tr>
                        <tr>
                            <td class="tg-lj2s" colspan="3"><span style="font-weight:bold">Tax</span></td>
                            <td class="tg-lj2s">260.00</td>
                        </tr>
                        <tr>
                            <td class="tg-lj2s" colspan="3"><span style="font-weight:bold">Grand Total</span></td>
                            <td class="tg-qq6b">2860.00</td>
                        </tr>
                        <tr>
                            <td class="tg-a4ox" colspan="4"></td>
                        </tr>
                        <tr>
                            <td class="tg-xdfh" colspan="4"><span style="font-weight:bold">Notes</span></td>
                        </tr>
                        <tr>
                            <td class="tg-a4ox" colspan="4">Note that orders may take 24-48 hrs to process. Payments can be made in cash, cheque or via bank transfer. For any queries regarding your order please contact (+267) 75993221</td>
                        </tr>
                        <tr>
                            <td class="tg-xdfh" colspan="4"><span style="font-weight:bold">Bank Account Details</span></td>
                        </tr>
                        <tr>
                            <td class="tg-a4ox" colspan="4"><span style="font-weight:bold">Account Name:</span> Optimum Quality<br><span style="font-weight:bold">Branch :</span> Corporate Branch<br><span style="font-weight:bold">Branch Code:</span> 282267<br><span style="font-weight:bold">Swift Code:</span> FIRNBWGX<br><span style="font-weight:bold">Account Number:</span> 57131113369</td>
                        </tr>
                    </table>
                </div>

            </div>
        

            //  Show Items purchased
            //  Show client billing and delivery information
            //  Show company contact details
            
        </div>
    </div>

</body>

</html>