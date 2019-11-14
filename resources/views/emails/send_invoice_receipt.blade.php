<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;">
 <head> 
  <meta charset="UTF-8"> 
  <meta content="width=device-width, initial-scale=1" name="viewport"> 
  <meta name="x-apple-disable-message-reformatting"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
  <meta content="telephone=no" name="format-detection"> 
  <title>Payment Receipt</title> 
  
  <!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--> 
  <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--> 

  <style type="text/css">
      @media only screen and (max-width:600px) {p, ul li, ol li, a { font-size:16px!important; line-height:150%!important } h1 { font-size:30px!important; text-align:center; line-height:120%!important } h2 { font-size:26px!important; text-align:center; line-height:120%!important } h3 { font-size:20px!important; text-align:center; line-height:120%!important } h1 a { font-size:30px!important } h2 a { font-size:26px!important } h3 a { font-size:20px!important } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:16px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:block!important } a.es-button { font-size:20px!important; display:block!important; border-width:10px 0px 10px 0px!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0px!important } .es-m-p0r { padding-right:0px!important } .es-m-p0l { padding-left:0px!important } .es-m-p0t { padding-top:0px!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } .es-desk-hidden { display:table-row!important; width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } .es-desk-menu-hidden { display:table-cell!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } }
      #outlook a {
        padding:0;
      }
      .ExternalClass {
        width:100%;
      }
      .ExternalClass,
      .ExternalClass p,
      .ExternalClass span,
      .ExternalClass font,
      .ExternalClass td,
      .ExternalClass div {
        line-height:100%;
      }
      .es-button {
        mso-style-priority:100!important;
        text-decoration:none!important;
      }
      a[x-apple-data-detectors] {
        color:inherit!important;
        text-decoration:none!important;
        font-size:inherit!important;
        font-family:inherit!important;
        font-weight:inherit!important;
        line-height:inherit!important;
      }
      .es-desk-hidden {
        display:none;
        float:left;
        overflow:hidden;
        width:0;
        max-height:0;
        line-height:0;
        mso-hide:all;
      }

      #page{position:absolute;top:0px;left:0px;right:0px;bottom:0px;z-index:-1;width:100%;}

      body{
          background:#FFFFFF;
      }

</style> 
 </head> 
 <body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;"> 
  
  
  @php 
      $company = $invoice['customized_company_details'];
      $client = $invoice['customized_customer_details'];
      $clientName = $client['name'] ?? $client['full_name'];
      $companyPhones = isset($company['phones']) ? collect(array_filter($company['phones'], function($phone){ return $phone['show']; }))->values() : [];
      $clientPhones = isset($client['phones']) ? collect(array_filter($client['phones'], function($phone){ return $phone['show']; }))->values() : [];
      $currencySymbol = $invoice['currency_type']['currency']['symbol'];
  @endphp
    
  <div id="page" class="es-wrapper-color" style="background-color:#F6F6F6;"> 
   <!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#f6f6f6"></v:fill>
			</v:background>
		<![endif]--> 
   <table cellpadding="0" cellspacing="0" class="es-wrapper" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;"> 
     <tr style="border-collapse:collapse;"> 
      <td valign="top" style="padding:0;Margin:0;"> 
       <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top;"> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;background-position:left top;"> 
           <table class="es-header-body" align="center" cellpadding="0" cellspacing="0" width="600" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" esdev-eq="true" bgcolor="#ffffff" style="padding:0;Margin:0;background-color:#FFFFFF;background-position:left top;"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td width="600" align="center" valign="top" style="padding:0;Margin:0;"> 
                   <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-position:left top;"> 
                     <tr style="border-collapse:collapse;"> 
                        <td align="center" style="padding:0;Margin:0;"> 
                          <a href="#" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#1376C8;">
                            <img src="https://drive.google.com/uc?export=view&id=1yz3yFBUFXGB37AMdYaOlQqvHGsOxVn7Q" alt="" width="150" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" class="adapt-img">
                          </a> 
                        </td> 
                     </tr> 
                   </table> </td> 
                 </tr> 
               </table> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table>

       
       @if($msg)
       <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;padding-bottom:20px;"> 
          <tr style="border-collapse:collapse;"> 
           <td align="center" style="padding:0;Margin:0;"> 
            <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;"> 
              <tr style="border-collapse:collapse;"> 
               <td align="left" bgcolor="#ffffff" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;background-color:#FFFFFF;background-position:left top;"> 
                <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                  <tr style="border-collapse:collapse;"> 
                   <td width="560" align="center" valign="top" style="padding:0;Margin:0;"> 
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                      <tr style="border-collapse:collapse;"> 
                        <td align="left" style="padding:0;Margin:0;padding-bottom:15px;"> 
                            {!! $msg !!}
                        </td> 
                      </tr>
                    </table> </td> 
                  </tr> 
                </table> </td> 
              </tr> 
            </table> </td> 
          </tr> 
        </table> 
        @endif

       <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" bgcolor="#ffffff" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;background-color:#FFFFFF;background-position:left top;"> 
               <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td width="560" align="center" valign="top" style="padding:0;Margin:0;"> 
                   <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="left" style="padding:0;Margin:0;padding-bottom:15px;"> <h2 style="Margin:0;line-height:29px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:24px;font-style:normal;font-weight:normal;color:#43BB18;text-align:center;"><strong>Payment Receipt</strong></h2> </td> 
                     </tr> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:30px;"> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#43BB18;">
                        <strong>INVOICE #{{ $invoice['reference_no_value'] }}</strong></p> 
                      </td> 
                     </tr> 
                   </table> </td> 
                 </tr> 
               </table> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> 
       
       <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
            <tr style="border-collapse:collapse;"> 
                <td align="center" style="padding:0;Margin:0;"> 
                    <table bgcolor="#efefef" class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#EFEFEF;"> 
                            <tr style="border-collapse:collapse;"> 
                                <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;padding-top:40px;background-position:center top;"> 
                                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                                        <tr style="border-collapse:collapse;"> 
                                        <td width="560" align="center" valign="top" style="padding:0;Margin:0;"> 
                                        <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                                            <tr style="border-collapse:collapse;"> 
                                              <td align="left" style="padding:0;Margin:0;padding-bottom:20px;"> 
                                                <h2 style="Margin:0;line-height:29px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:24px;font-style:normal;font-weight:normal;color:#000000;text-align:center;">
                                                  <strong>Amount Paid: {{ $currencySymbol . number_format($invoice['grand_total'],2,",",".") }}</strong>
                                                </h2>
                                                <h3 style="Margin:0;margin-top: 5px;line-height:14px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:12px;font-style:normal;font-weight:normal;color:#000000;text-align:center;">
                                                  Payment Method: BANK PAYMENT
                                                </h3> 
                                              </td> 
                                            </tr> 
                                        </table> </td> 
                                        </tr> 
                                    </table> 
                                </td> 
                            </tr> 
                            <tr style="border-collapse:collapse;"> 
                                <td align="left" style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:30px;background-position:center top;"> 
                                    <!--[if mso]>
                                        <table width="560" cellpadding="0" cellspacing="0">
                                            <tr><td width="270" valign="top">
                                        <![endif]--> 
                                        <table cellpadding="0" cellspacing="0" style="width: 50%; float: left;"> 
                                            <tr style="border-collapse:collapse;"> 
                                                <td width="270" class="es-m-p20b" align="left" style="padding:0;Margin:0;"> 
                                                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                                                        <tr style="border-collapse:collapse;"> 
                                                            <td align="center" style="padding:0;Margin:0;"> 
                                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:16px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#1B1B1B;">
                                                                  <strong>PAYMENT BY</strong>
                                                              </p>
                                                              <br>
                                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                {{ $clientName }}
                                                              </p>
                                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                {{ $client['email'] }}
                                                              </p>
                                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                @if(COUNT($clientPhones))
                                                                    @foreach($clientPhones as $key => $phone){{($key != 0 ? ', ': '') . '(+' . $phone['calling_code'] . ') ' . $phone['number']}} @endforeach
                                                                @endif
                                                              </p>
                                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                <br>
                                                              </p>
                                                              <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                  {{ $client['address'] }}@if(!empty($client['address'] && ($client['city']) || !empty($client['country']) ) ), @endif
                                                                  {{ $client['city'] }}@if(!empty($client['city'] && !empty($client['country']) ) ), @endif
                                                                  {{ $client['country'] }}
                                                              </p> 
                                                            </td> 
                                                        </tr> 
                                                    </table> 
                                                </td> 
                                            </tr> 
                                        </table> 
                                    <!--[if mso]></td><td width="20"></td><td width="270" valign="top"><![endif]--> 
                                        <table cellpadding="0" cellspacing="0" style="width: 50%; float: left;"> 
                                            <tr style="border-collapse:collapse;"> 
                                                <td width="270" class="es-m-p20b" align="left" style="padding:0;Margin:0;"> 
                                                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                                                        <tr style="border-collapse:collapse;"> 
                                                            <td align="center" style="padding:0;Margin:0;"> 
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:16px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:24px;color:#1B1B1B;">
                                                                    <strong>RECEIPT FROM</strong>
                                                                </p>
                                                                <br>
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                  {{ $company['name'] }}
                                                                </p>
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                  {{ $company['email'] }}
                                                                </p>
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                  @if(COUNT($companyPhones))
                                                                      @foreach($companyPhones as $key => $phone){{($key != 0 ? ', ': '') . '(+' . $phone['calling_code'] . ') ' . $phone['number']}} @endforeach
                                                                  @endif
                                                                </p>
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                  <br>
                                                                </p>
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#1B1B1B;">
                                                                    {{ $company['address'] }}@if(!empty($company['address'] && ($company['city']) || !empty($company['country']) ) ), @endif
                                                                    {{ $company['city'] }}@if(!empty($company['city'] && !empty($company['country']) ) ), @endif
                                                                    {{ $company['country'] }}
                                                                </p> 
                                                            </td> 
                                                        </tr> 
                                                    </table> 
                                                </td> 
                                            </tr> 
                                        </table> 
                                    <!--[if mso]></td></tr></table><![endif]--> 
                                    <div style="clear: both;"></div>
                                </td> 
                            </tr> 
                    </table> 
                </td> 
            </tr> 
       </table> 

       <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
            <tr style="border-collapse:collapse;"> 
             <td align="center" style="padding:0;Margin:0;"> 
              <table class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;"> 
                <tr style="border-collapse:collapse;"> 
                 <td align="left" style="padding:0;Margin:0;"> 
                  <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                    <tr style="border-collapse:collapse;"> 
                     <td width="600" align="center" valign="top" style="padding:0;Margin:0;"> 
                      <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                        <tr style="border-collapse:collapse;"> 
                         <td align="center" height="15" style="padding:0;Margin:0;"> </td> 
                        </tr> 
                        <tr style="border-collapse:collapse;"> 
                         <td align="center" style="padding:0;Margin:0;"> <img class="adapt-img" src="https://drive.google.com/uc?export=view&id=1ckHHvs_6OvkKPp6_iqIbHrsS1VD90uCG" alt="" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" width="200"></td> 
                        </tr> 
                      </table> </td> 
                    </tr> 
                  </table> </td> 
                </tr> 
              </table> </td> 
            </tr> 
          </table> 
          <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
            <tr style="border-collapse:collapse;"> 
             <td align="center" style="padding:0;Margin:0;"> 
              <table class="es-content-body" align="center" cellpadding="0" cellspacing="0" width="600" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;" bgcolor="#ffffff"> 
                <tr style="border-collapse:collapse;"> 
                 <td align="left" style="Margin:0;padding-left:20px;padding-right:20px;padding-top:30px;padding-bottom:30px;background-position:center top;"> 
                  <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                    <tr style="border-collapse:collapse;"> 
                     <td width="560" align="center" valign="top" style="padding:0;Margin:0;"> 
                      <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                        <tr style="border-collapse:collapse;"> 
                         <td class="es-infoblock" align="center" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC;"> <a href="#" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:12px;text-decoration:underline;color:#CCCCCC;"> <img src="https://drive.google.com/uc?export=view&id=1yz3yFBUFXGB37AMdYaOlQqvHGsOxVn7Q" alt="" width="100" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"> </a> </td> 
                        </tr> 
                        <tr style="border-collapse:collapse;"> 
                         <td align="center" style="padding:0;Margin:0;"> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;"><strong>POWERED BY OPTIMUM Q</strong></p> </td> 
                        </tr> 
                      </table> </td> 
                    </tr> 
                  </table> </td> 
                </tr> 
              </table> </td> 
            </tr> 
          </table> </td> 
        </tr> 
      </table> 
     
      </div>  
    </body>
   </html>