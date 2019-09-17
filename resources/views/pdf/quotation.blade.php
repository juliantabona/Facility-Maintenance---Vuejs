<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <meta http-equiv="X-UA-Compatible" content="IE=8"></meta>

        <title>Laravel</title>

        <style type="text/css">
            
            html,body {width:100%;height:100%;margin: 0px;padding: 0px; }

            body {margin-top: 20px;}
            body {font-family:Arial, Helvetica, sans-serif !important;}
            
            #page_1 {
                
            }
            
            /** Define the footer rules **/
            footer {
                position: fixed; 
                bottom: 0; 
                left: 0; 
                right: 0;
                height: 40px;

                /** Extra personal styles **/
                font-size:12px;
                background-color: #000;
                color: white;
                text-align: center;
                line-height: 30px;
            }
            
            #page_1 #p1dimg1 {position:absolute;top:5px;left:0px;z-index:-1;width:100%;height:170px;}
            #page_1 #p1dimg1 #p1img1 {width:150px;height:auto;}
            
            .ft0{font: 40px 'Arial';line-height: 45px;}
            .ft1{font: bold 13px 'Arial';line-height: 16px;}
            .ft2{font: 13px 'Arial';line-height: 16px;}
            .ft3{font: 13px 'Arial';line-height: 14px;}
            .ft4{font: 13px 'Arial';color: #8c959a;line-height: 16px;}
            .ft5{font: 1px 'Arial';line-height: 1px;}
            .ft6{font: bold 13px 'Arial';line-height: 15px;}
            .ft7{font: 1px 'Arial';line-height: 15px;}
            .ft8{font: 1px 'Arial';line-height: 12px;}
            .ft9{font: 1px 'Arial';line-height: 11px;}
            .ft10{font: 1px 'Arial';line-height: 6px;}
            .ft11{font: 1px 'Arial';line-height: 10px;}
            .ft12{font: bold 13px 'Arial';color: #ffffff;line-height: 16px;}
            .ft13{font: 1px 'Arial';line-height: 9px;}
            .ft14{font: 13px 'Arial';line-height: 15px;}
            .ft15{font: 1px 'Arial';line-height: 14px;}
            .ft16{font: 1px 'Arial';line-height: 8px;}
            .ft17{font: 13px 'Arial';color: #808080;line-height: 16px;}
            .ft18{font: 20px 'Arial';line-height: 25px;}
            
            .p0{text-align: left;padding-left: 558px;margin-top: 0px;margin-bottom: 0px;}
            .p1{text-align: right;padding-right: 29px;margin-top: 13px;margin-bottom: 0px;}
            .p2{text-align: right;padding-right: 29px;margin-top: 0px;margin-bottom: 0px;}
            .p3{text-align: right;padding-right: 29px;margin-top: 11px;margin-bottom: 0px;}
            .p4{text-align: left;padding-left: 29px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p5{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p6{text-align: right;padding-right: 12px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p7{text-align: right;padding-right: 59px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p8{text-align: center;padding-right: 29px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p9{text-align: right;padding-right: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p10{text-align: right;padding-right: 27px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p11{text-align: right;padding-right: 71px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
            .p12{text-align: left;padding-left: 29px;margin-top: 7px;margin-bottom: 0px;}
            .p13{text-align: left;padding-left: 29px;margin-top: 1px;margin-bottom: 0px;}
            .p14{text-align: left;padding-left: 29px;margin-top: 2px;margin-bottom: 0px;}
            .p15{text-align: left;margin-top: 0px;margin-bottom: 0px;}
            
            .td0{padding: 0px;margin: 0px;width: 383px;vertical-align: bottom;}
            .td1{padding: 0px;margin: 0px;width: 121px;vertical-align: bottom;}
            .td2{padding: 0px;margin: 0px;width: 150px;vertical-align: bottom;}
            .td3{padding: 0px;margin: 0px;width: 74px;vertical-align: bottom;}
            .td4{padding: 0px;margin: 0px;width: 59px;vertical-align: bottom;}
            .td5{padding: 0px;margin: 0px;width: 2px;vertical-align: bottom;}
            .td6{padding: 0px;margin: 0px;width: 27px;vertical-align: bottom;}
            .td7{padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
            .td8{padding: 0px;margin: 0px;width: 281px;vertical-align: bottom;}
            .td9{padding: 0px;margin: 0px;width: 119px;vertical-align: bottom;}
            .td10{padding: 0px;margin: 0px;width: 150px;vertical-align: bottom;background: #f4f5f5;}
            .td11{padding: 0px;margin: 0px;width: 74px;vertical-align: bottom;background: #f4f5f5;}
            .td12{padding: 0px;margin: 0px;width: 59px;vertical-align: bottom;background: #f4f5f5;}
            .td13{padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;background: #f4f5f5;}
            .td14{padding: 0px;margin: 0px;width: 119px;vertical-align: bottom;background: #f4f5f5;}
            .td15{padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
            .td16{padding: 0px;margin: 0px;width: 383px;vertical-align: bottom;}
            .td17{padding: 0px;margin: 0px;width: 121px;vertical-align: bottom;}
            .td18{padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
            .td19{padding: 0px;margin: 0px;width: 119px;vertical-align: bottom;}
            .td20{padding: 0px;margin: 0px;width: 74px;vertical-align: bottom;}
            .td21{padding: 0px;margin: 0px;width: 88px;vertical-align: bottom;}
            .td22{padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
            .td23{padding: 0px;margin: 0px;width: 27px;vertical-align: bottom;}
            .td24{padding: 0px;margin: 0px;width: 88px;vertical-align: bottom;}
            .td25{border-bottom: #dee1e2 1px solid;padding: 0px;margin: 0px;width: 383px;vertical-align: bottom;}
            .td26{border-bottom: #dee1e2 1px solid;padding: 0px;margin: 0px;width: 121px;vertical-align: bottom;}
            .td27{border-bottom: #dee1e2 1px solid;padding: 0px;margin: 0px;width: 31px;vertical-align: bottom;}
            .td28{border-bottom: #dee1e2 1px solid;padding: 0px;margin: 0px;width: 193px;vertical-align: bottom;}
            .td29{border-bottom: #dee1e2 1px solid;padding: 0px;margin: 0px;width: 61px;vertical-align: bottom;}
            .td30{border-bottom: #dee1e2 1px solid;padding: 0px;margin: 0px;width: 27px;vertical-align: bottom;}
            .td31{padding: 0px;margin: 0px;width: 193px;vertical-align: bottom;}
            
            .tr0{height: 19px;}
            .tr1{height: 15px;}
            .tr2{height: 27px;}
            .tr3{height: 12px;}
            .tr4{height: 16px;}
            .tr5{height: 11px;}
            .tr6{height: 6px;}
            .tr7{height: 10px;}
            .tr8{height: 23px;}
            .tr9{height: 9px;}
            .tr10{height: 14px;}
            .tr11{height: 8px;}
            .tr12{height: 33px;}
            .tr13{height: 26px;}
            .tr14{height: 20px;}
            .tr15{height: 34px;}
            .tr16{height: 69px;}
            .tr17{height: 17px;}
            
            .t0{width: 100%;margin-top: 34px;font: 13px 'Arial';}

            /*	PADDING	*/

            .pd0{padding:0px !important;}
            .pd1{padding:5px !important;}
            .pd2{padding:10px !important;}
            .pd3{padding:20px !important;}
            .pd4{padding:30px !important;}
            .pd5{padding:40px !important;}
            .pd6{padding:50px !important;}

            .pdt0{padding-top:0px !important;}
            .pdt1{padding-top:5px !important;}
            .pdt2{padding-top:10px !important;}
            .pdt3{padding-top:20px !important;}
            .pdt4{padding-top:30px !important;}
            .pdt5{padding-top:40px !important;}
            .pdt6{padding-top:50px !important;}

            .pdb0{padding-bottom:0px !important;}
            .pdb1{padding-bottom:5px !important;}
            .pdb2{padding-bottom:10px !important;}
            .pdb3{padding-bottom:20px !important;}
            .pdb4{padding-bottom:30px !important;}
            .pdb5{padding-bottom:40px !important;}
            .pdb6{padding-bottom:50px !important;}

            .pdl0{padding-left:0px !important;}
            .pdl1{padding-left:5px !important;}
            .pdl2{padding-left:10px !important;}
            .pdl3{padding-left:20px !important;}
            .pdl4{padding-left:30px !important;}
            .pdl5{padding-left:40px !important;}
            .pdl6{padding-left:50px !important;}

            .pdr0{padding-right:0px !important;}
            .pdr1{padding-right:5px !important;}
            .pdr2{padding-right:10px !important;}
            .pdr3{padding-right:20px !important;}
            .pdr4{padding-right:30px !important;}
            .pdr5{padding-right:40px !important;}
            .pdr6{padding-right:50px !important;}

            /*	MARGIN	*/
            
            .mr0{margin:0px !important;}
            .mr1{margin:5px !important;}
            .mr2{margin:10px !important;}
            .mr3{margin:20px !important;}
            .mr4{margin:30px !important;}
            .mr5{margin:40px !important;}
            .mr6{margin:50px !important;}

            .mrt0{margin-top:0px !important;}
            .mrt1{margin-top:5px !important;}
            .mrt2{margin-top:10px !important;}
            .mrt3{margin-top:20px !important;}
            .mrt4{margin-top:30px !important;}
            .mrt5{margin-top:40px !important;}
            .mrt6{margin-top:50px !important;}

            .mrb0{margin-bottom:0px !important;}
            .mrb1{margin-bottom:5px !important;}
            .mrb2{margin-bottom:10px !important;}
            .mrb3{margin-bottom:20px !important;}
            .mrb4{margin-bottom:30px !important;}
            .mrb5{margin-bottom:40px !important;}
            .mrb6{margin-bottom:50px !important;}

            .mrl0{margin-left:0px !important;}
            .mrl1{margin-left:5px !important;}
            .mrl2{margin-left:10px !important;}
            .mrl3{margin-left:20px !important;}
            .mrl4{margin-left:30px !important;}
            .mrl5{margin-left:40px !important;}
            .mrl6{margin-left:50px !important;}

            .mrr0{margin-right:0px !important;}
            .mrr1{margin-right:5px !important;}
            .mrr2{margin-right:10px !important;}
            .mrr3{margin-right:20px !important;}
            .mrr4{margin-right:30px !important;}
            .mrr5{margin-right:40px !important;}
            .mrr6{margin-right:50px !important;}

            .brt{border-top: 1px solid #dee1e2 !important;}
            .brl{border-left: 1px solid #dee1e2 !important;}
            .brr{border-right: 1px solid #dee1e2 !important;}
            .brb{border-bottom: 1px solid #dee1e2 !important;}

            .brt2{border-top: 1px solid #dee1e2 !important;}
            .brl2{border-left: 1px solid #dee1e2 !important;}
            .brr2{border-right: 1px solid #dee1e2 !important;}
            .brb2{border-bottom: 1px solid #dee1e2 !important;}
            

        </style>
    </head>

    <body>

        @php 

            $primaryColor = $quotation['colors'][0];
            $secondaryColor = $quotation['colors'][1]; 
            $company = $quotation['customized_company_details'];
            $client = $quotation['customized_client_details'];
            $clientName = $client['name'] ?? $client['full_name'];
            $companyPhones = isset($company['phones']) ? collect(array_filter($company['phones'], function($phone){ return $phone['show']; }))->values() : [];
            $clientPhones = isset($client['phones']) ? collect(array_filter($client['phones'], function($phone){ return $phone['show']; }))->values() : [];
            $currencySymbol = $quotation['currency_type']['currency']['symbol'];
            $notes = $quotation['notes'];

        @endphp

        <main id="page_1">

            <div id="p1dimg1">
                <img id="p1img1" class="mrl3" src="/images/assets/logo/OQ-B.png">
            </div>

            <div id="id1_1">

                <div class="mrb2 mrr4">
                    <p class="ft0 p2" style="text-align:right;">{{ $quotation['heading'] }}</p>
                    <p class="ft1 p2" style="text-align:right;">{{ $company['name'] }}</p>
                    <p class="ft2 p2">{{ $company['email'] }}</p>
                    <p class="ft2 p2">
                        @if(COUNT($companyPhones))
                            @foreach($companyPhones as $key => $phone){{($key != 0 ? ', ': '') . '(+' . $phone['calling_code']['calling_code'] . ') ' . $phone['number']}} @endforeach
                        @endif
                    </p>
                    <br />
                    
                    @if($company['address'])
                        <p class="ft2 p2">{{ $company['address'] }}</p>
                    @endif
                    @if($company['city'])
                        <p class="ft2 p2">{{ $company['city'] }}</p>
                    @endif
                    @if($company['country'])
                        <p class="ft2 p2">{{ $company['country'] }}</p>
                    @endif


                </div>

                <table cellpadding="0" cellspacing="0" class="brt mrb0 mrt1 pdb4" style="width:100%;">
                    <tbody>
                        <tr>

                            <td colspan="8" class="mrt3">
                                <p class="ft18 p4 pdb1">{{ $quotation['quotation_to_title'] }}:</p>
                                <p class="p4 ft6 pdb1">{{ $clientName }}</p>
                                <p class="p4 ft2">{{ $client['email'] }}</p>
                                <p class="p4 ft2">
                                    @if(COUNT($clientPhones))
                                        @foreach($clientPhones as $key => $phone){{($key != 0 ? ', ': '') . '(+' . $phone['calling_code']['calling_code'] . ') ' . $phone['number']}} @endforeach
                                    @endif
                                </p>

                                @if($client['address'])
                                    <p class="p4 ft2">{{ $client['address'] }}</p>
                                @endif
                                @if($client['city'])
                                    <p class="p4 ft2">{{ $client['city'] }}</p>
                                @endif
                                @if($client['country'])
                                    <p class="p4 ft2">{{ $client['country'] }}</p>
                                @endif
            
                            </td>

                            <td colspan="3" class="tr0 td2 mrt3">
            
                                <table cellpadding="0" cellspacing="0" class="mrt2">
  
                                    <tr>
                                        <td class="tr0 td2">
                                            <p class="p6 ft1 pdl6">{{ $quotation['reference_no_title'] }}:</p>
                                        </td>
            
                                        <td class="tr0 td2">
                                            <p class="p5 ft2 pdr4">{{ $quotation['reference_no_value'] }}</p>
                                        </td>
            
                                    </tr>
                
                                        <tr>
                
                                            <td class="tr0 td2">
                                                <p class="p6 ft1">{{ $quotation['created_date_title'] }}:</p>
                                            </td>
                
                                            <td class="tr0 td2">
                                                <p class="p5 ft2 pdr4">{{ Carbon\Carbon::parse($quotation['created_date'])->format('M d Y') }}</p>
                                            </td>
                
                                        </tr>
                
                                        <tr>
                                            <td class="tr0">
                                                <p class="p6 ft1">{{ $quotation['expiry_date_title'] }}</p>
                                            </td>
                
                                            <td class="tr0">
                                                <p class="p5 ft2 pdr4">{{ Carbon\Carbon::parse($quotation['expiry_date'])->format('M d Y') }}</p>
                                            </td>
                
                                        </tr>
                
                                        <tr>
                                            <td class="td10 tr0">
                                                <p class="ft1 p6 pd2">{{ $quotation['grand_total_title'] }}:</p>
                                            </td>
                
                                            <td class="td10 td14">
                                                <p class="ft1 p5 pd2">{{ $currencySymbol . number_format($quotation['grand_total'],2,",",".") }}</p>
                                            </td>
                
                                        </tr>

                                </table>
                                
                            </td>

                        </tr>

                    </tbody>

                </table>

                <table cellpadding="0" cellspacing="0" class="pdb0 pdt0 mrt2 mrb0 t0">
                    <tbody>

                        <tr style="background-color:{{ $primaryColor }} !important;">
                            <td colspan="7" class="pdb1 pdt1 td16">
                                <p class="ft12 pdl4">{{ $quotation['table_columns'][0]['name'] }}</p>
                            </td>

                            <td colspan="1" class="pdb1 pdt1 td17">
                                <p class="ft12">{{ $quotation['table_columns'][1]['name'] }}</p>
                            </td>

                            <td colspan="1" class="pdb1 pdt1 td19">
                                <p class="ft12">{{ $quotation['table_columns'][2]['name'] }}</p>
                            </td>

                            <td colspan="1" class="pdb1 pdt1 td21">
                                <p class="ft12 pdr6">{{ $quotation['table_columns'][3]['name'] }}</p>
                            </td>
                        </tr>
                        @if($quotation['items'])
                            @foreach($quotation['items'] as $key => $item)
                                <tr style="background-color:{{ ( ($key + 1) % 2 ) ? $secondaryColor . ' !important': '' }} ;">
                                    <td colspan="7" style="word-wrap: break-word" class="pdt1">
                                        <p class="ft1 p4 mrt1" style="line-height:16px;">{{ $item['name'] }}</p>
                                    </td>

                                    <td rowspan="2" colspan="1" class="pdt1">
                                        <p class="ft2 p11 mrt1" style="line-height:16px;margin-top: 0px;margin-bottom: 0px;text-align:center;">{{ $item['quantity'] }}</p>
                                    </td>

                                    <td rowspan="2" colspan="1" class="pdt1">
                                        <p class="ft2 mrt1" style="line-height:16px;margin-top: 0px;margin-bottom: 0px;">{{ number_format($item['unit_price'],2,",",".") }}</p>
                                    </td>

                                    <td rowspan="2" colspan="1" class="pdt1">
                                        <p class="ft2 mrt1" style="line-height:16px;margin-top: 0px;margin-bottom: 0px;">{{ number_format($item['total_price'],2,",",".") }}</p>
                                    </td>
                                </tr>

                                <tr style="background-color:{{ ( ($key + 1) % 2 ) ? $secondaryColor . ' !important': '' }} ;">
                                    <td colspan="7" class="tr0 pdb1">
                                        <p class="ft14 p14" style="line-height:16px;">{{ $item['description'] }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>

                <table cellpadding="0" cellspacing="0" class="pdb0 pdt0 mrt0 mrb0 t0 pdr4">
                    <tbody>

                        <tr>
                            <td colspan="5" class="tr12 td31"></td>
                            <td colspan="4" class="tr12 td31">
                                <p class="ft1 p11 pdr3" style="text-align:right;">{{ $quotation['sub_total_title'] }}:</p>
                            </td>

                            <td colspan="1" class="tr12 td24">
                                <p class="p10 ft2 pdr4">{{ $currencySymbol . number_format($quotation['sub_total_value'],2,",",".") }}</p>
                            </td>
                        </tr>
                        @if($quotation['calculated_taxes'])
                            @foreach($quotation['calculated_taxes'] as $tax)
                                <tr>
                                    <td colspan="5" class="tr12 td31"></td>
                                    <td colspan="4" class="tr2 td31">
                                        <p class="ft2 p10 pdr3">{{ $tax['name'] . '(' .$tax['rate']*100 .'%)' }}:</p>
                                    </td>

                                    <td colspan="1" class="tr2 td24">
                                        <p class="p10 ft2 pdr4">{{ $currencySymbol . number_format($tax['amount'],2,",",".") }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        <tr>
                            <td colspan="5" class="tr12 td31"></td>
                            <td colspan="4"></td>
                            <td colspan="1"></td>
                        </tr>

                        <tr>
                            <td colspan="5" class="tr12 td31"></td>
                            <td colspan="4" class="brt brb2">
                                <p class="ft1 p11 pdr3">{{ $quotation['grand_total_title'] }}:</p>
                            </td>

                            <td colspan="1" class="brt brb2">
                                <p class="p10 ft1 pdr4">{{ $currencySymbol . number_format($quotation['grand_total'],2,",",".") }}</p>
                            </td>
                        </tr>

                    </tbody>
                </table>

                <table cellpadding="0" cellspacing="0" class="pdb0 pdt0 mrt0 mrb0 t0" style="width:100%;">
                    <tbody>

                        <tr>
                            <td>
                                <p class="ft1 ft18 p4 pdb2 pdr4">{{ $notes['title'] }}</p>
                                <div class="p4 ft2 pdr6" style="white-space: normal;">{!! $notes['details'] !!}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </main>
        
        <footer style="background-color:{{ $primaryColor }} !important;">
            <p style="margin-top:2px;">{{ $quotation['footer'] }}</p>
        </footer>

        <script type="text/javascript"> 
            @if(isset($print) && $print == 1)
                this.print();
            @endif 
        </script> 

    </body>
</html>