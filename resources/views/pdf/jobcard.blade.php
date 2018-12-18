<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <title>Laravel</title>
  
        <style>

            /*  This text is in Helvetica   */
            body { 
                font-style: normal; 
                font-variant: normal; 
                font-family: Helvetica Neue,Helvetica,Arial,sans-serif; 
            }

            h1 { 
                font-size: 24px;
                font-weight: 700; 
                line-height: 26.4px; 
            } 
            
            h3 { 
                font-size: 14px;
                font-weight: 700; 
                line-height: 15.4px; 
            } 

            .text-highlight{
                background:#F0F0F0;
                padding:5px;
            }
            
            p, span { 
                font-size: 14px;
                font-weight: 400; 
                line-height: 20px; 
            } 
            
            blockquote { 
                font-size: 21px;
                font-weight: 400; 
                line-height: 30px; 
            } 
                
            pre { 
                font-size: 13px; 
                font-weight: 400; 
                line-height: 18.5714px; 
            }

            table{
                width:100%;
            }

            table, th, td {
                /*border: 1px solid black;*/
                /*   This collapses border lines to be single lines. E.g if two columns with borders
                    intersect their border lines will be merged into one line.
                */
                border-collapse:collapse;
            }

            table.print-friendly, table.print-friendly tr, table.print-friendly tr td, table.print-friendly tr th {
                /*   Stops the table from being split into two pages. This can be undesirable as related data
                    is being presented in two different pages, meaning the user would have to print both pages   
                    with the data split.
                */
                page-break-inside: avoid;
            }

            th, td {
                padding: 5px;
                text-align: left;    
            }

            .border{
                border: 1px solid black;
            }

            .bg-highlight{
                background:#FFFFE0;
            }
        </style>

    </head>
    <body>
        <!--   Company Logo/Letterhead   -->
        
        <table class="print-friendly">
            <tr>
                <td colspan="2" style="text-align: right;">
                    <img src="http://acmelogos.com/images/logo-1.svg"
                    style="max-height: 100px;">
                </td>
            </tr>
        </table>


        <!--   Jobcard Details   -->
        @if($options['showJobcard'])
            <table class="print-friendly">
                <tr>
                    <td colspan="2">
                        <h1 class="text-highlight">Jobcard Summary</h1>
                        @if($options['JobcardOptions']['Title'])
                            <p><strong>Title:</strong> {{ $jobcard->title }}</p>
                        @endif
                        @if($options['JobcardOptions']['Description'])
                            <p>
                                <strong>Description: </strong>
                                <span>{{ $jobcard->description }}</span>
                            </p>
                        @endif
                        @if($options['JobcardOptions']['Deadline'])
                            <p>
                                <strong>Deadline: </strong>{{ $jobcard->deadlineInWords }}
                            </p>
                        @endif
                    </td>
                </tr>
                @if($options['JobcardOptions']['Start_Date'] || $options['JobcardOptions']['End_Date'])
                    <tr>
                        @if($options['JobcardOptions']['Start_Date'])
                            <td colspan="1">
                                <span><strong>Start Date:</strong></span>
                                <span>
                                    {{ \Carbon\Carbon::parse($jobcard->start_date)->format('d M Y, H:iA') }}
                                </span>
                            </td>
                        @endif
                        @if($options['JobcardOptions']['End_Date'])
                            <td colspan="1">
                                <span><strong>End Date:</strong></span>
                                <span>
                                    {{ \Carbon\Carbon::parse($jobcard->end_date)->format('d M Y, H:iA') }}
                                </span>
                            </td>
                        @endif
                    </tr>
                @endif
                @if($options['JobcardOptions']['Priority'] || $options['JobcardOptions']['Category'])
                    <tr>
                        @if($options['JobcardOptions']['Priority'])
                            <td>
                                <span><strong>Priority:</strong></span>
                                <span>
                                    @foreach($jobcard->priorities as $priority)
                                        {{ $priority->name }}
                                    @endforeach
                                </span>
                            </td>
                        @endif
                        @if($options['JobcardOptions']['Category'])
                            <td>
                                <span><strong>Category:</strong></span>
                                <span>
                                    @foreach($jobcard->categories as $category)
                                        {{ $category->name }}
                                    @endforeach
                                </span>
                            </td>
                        @endif
                    </tr>
                @endif
                @if($options['JobcardOptions']['Cost_Centers'])
                    <tr>
                        <td colspan="2">
                            <span><strong>Cost Centers:</strong></span>
                            <span>
                                @foreach($jobcard->costcenters as $costcenter)
                                    {{ $costcenter->name }}
                                @endforeach
                            </span>
                        </td>
                    </tr>
                @endif
            </table>
            @if($options['JobcardOptions']['Created_By'] || $options['JobcardOptions']['Created_Date'] ||
                $options['JobcardOptions']['Authourized_By'] || $options['JobcardOptions']['Authourized_Date'])

                <table class="bg-highlight print-friendly" style="margin-top:15px;margin-bottom:30px;">
                    @if($options['JobcardOptions']['Created_By'] || $options['JobcardOptions']['Created_Date'])
                        <tr>
                            @if($options['JobcardOptions']['Created_By'])
                                <td class="border">
                                    <span><strong>Created By:</strong></span>
                                    <span>
                                        @if($jobcard->createdBy)
                                            {{ $jobcard->createdBy->first_name }} 
                                        @endif
                                        @if($jobcard->createdBy)
                                            {{ $jobcard->createdBy->last_name }} 
                                        @endif
                                    </span>
                                </td>
                            @endif
                            @if($options['JobcardOptions']['Created_Date'])
                                <td class="border">
                                    <span><strong>Created Date:</strong></span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($jobcard->created_at)->format('d M Y, H:iA') }}
                                    </span>
                                </td>
                            @endif
                        </tr>
                    @endif
                    @if($options['JobcardOptions']['Authourized_By'] || $options['JobcardOptions']['Authourized_Date'])
                        <tr>
                            @if($options['JobcardOptions']['Authourized_By'])
                                <td class="border">
                                    <span><strong>Authorized By:</strong></span>
                                    <span>
                                        @if($jobcard->authourizedBy)
                                            {{ $jobcard->authourizedBy->first_name }} 
                                        @endif
                                        @if($jobcard->authourizedBy)
                                            {{ $jobcard->authourizedBy->last_name }} 
                                        @endif
                                    </span>
                                </td>
                            @endif
                            @if($options['JobcardOptions']['Authourized_Date'])
                                <td class="border">
                                    <span><strong>Authorized Date:</strong></span>
                                    <span>
                    
                                        @foreach($jobcard->recentActivities as $recentActivity)
                                            @if($recentActivity->activity['type'] == 'authourized')
                                                {{ \Carbon\Carbon::parse($recentActivity->created_at)->format('d M Y, H:iA') }}
                                            @endif
                                        @endforeach
                                    </span>
                                </td>
                            @endif
                        </tr>
                    @endif
                </table>
            @endif
        @endif

        <!--   Client Details   -->
        @if($options['showClient'] && $jobcard->client)
            <table class="print-friendly" style="width:100%;margin-bottom:30px;">
                <tr>
                    <td colspan="3">
                        <h1 class="text-highlight">Client Details</h1>
                        @if($options['ClientOptions']['Logo'])
                            <img src="{{ $jobcard->client->logo_url }}" style="max-height: 100px;">
                        @endif
                    </td>
                </tr>
                @if($options['ClientOptions']['Name'])
                    <tr>
                        <td colspan="3">
                            <span><strong>Name:</strong> {{ $jobcard->client->name }}</span>
                        </td>
                    </tr>
                @endif
                @if($options['ClientOptions']['Address'] || $options['ClientOptions']['City'] || $options['ClientOptions']['State_Region'])
                    <tr>
                        @if($options['ClientOptions']['Address'])
                            <td>
                                <span><strong>Address:</strong> {{ $jobcard->client->address }}</span>
                            </td>
                        @endif
                        @if($options['ClientOptions']['City'])
                            <td>
                                <span><strong>City:</strong> {{ $jobcard->client->city }}</span>
                            </td>
                        @endif
                        @if($options['ClientOptions']['State_Region'])
                            <td>
                                <span><strong>State/Region:</strong> {{ $jobcard->client->state_or_region }}</span>
                            </td>
                        @endif
                    </tr>
                @endif
                @if($options['ClientOptions']['Industry'] || $options['ClientOptions']['Type'])
                <tr>
                    @if($options['ClientOptions']['Industry'])
                        <td colspan="1">
                            <span><strong>Industry:</strong> {{ $jobcard->client->industry }}</span>
                        </td>
                    @endif
                    @if($options['ClientOptions']['Type'])
                        <td colspan="1">
                            <span><strong>Type:</strong> {{ $jobcard->client->type }}</span>
                        </td>
                    @endif
                </tr>
                @endif
                @if($options['ClientOptions']['Email'] || $options['ClientOptions']['Phone'] || $options['ClientOptions']['Fax'])
                <tr>
                    <td colspan="3">
                        <table class="bg-highlight print-friendly" style="margin-bottom:30px;">
                            <tr>
                                @if($options['ClientOptions']['Email'])
                                    <td class="border">
                                        <span><strong>Email:</strong> {{ $jobcard->client->email }}</span>
                                    </td>
                                @endif
                                @if($options['ClientOptions']['Phone'])
                                    <td class="border">
                                        <span><strong>Phone:</strong> {{ $jobcard->client->phone }}</span>
                                    </td>
                                @endif
                                @if($options['ClientOptions']['Fax'])
                                    <td class="border">
                                        <span><strong>Fax:</strong> {{ $jobcard->client->fax }}</span>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
            </table>
        @endif

        <!--   Supplier Details   -->
        @if($options['showSupplier'] && COUNT($jobcard->supplierslist))

            <table class="print-friendly">
                <tr>
                    <td>
                        <h1 class="text-highlight">Supplier Details</h1>
                        @foreach($jobcard->supplierslist as $supplier)
                        
                            <table class="print-friendly" style="width:100%;margin-bottom:30px;">
                                @if($options['SupplierOptions']['Logo'])
                                    <tr>
                                        <td colspan="3">
                                            <img src="{{ $supplier->logo_url }}" style="max-height: 100px;">
                                        </td>
                                    </tr>
                                @endif
                                @if($options['SupplierOptions']['Name'])
                                    <tr>
                                        <td colspan="3">
                                            <span><strong>Name:</strong> {{ $supplier->name }}</span>
                                        </td>
                                    </tr>
                                @endif
                                @if($options['SupplierOptions']['Address'] || $options['SupplierOptions']['City'] || $options['SupplierOptions']['State_Region'])
                                    <tr>
                                        @if($options['SupplierOptions']['Address'])
                                            <td>
                                                <span><strong>Address:</strong> {{ $supplier->address }}</span>
                                            </td>
                                        @endif
                                        @if($options['SupplierOptions']['City'])
                                            <td>
                                                <span><strong>City:</strong> {{ $supplier->city }}</span>
                                            </td>
                                        @endif
                                        @if($options['SupplierOptions']['State_Region'])
                                            <td>
                                                <span><strong>State/Region:</strong> {{ $supplier->state_or_region }}</span>
                                            </td>
                                        @endif
                                    </tr>
                                @endif
                                @if($options['SupplierOptions']['Industry'] || $options['SupplierOptions']['Type'])
                                <tr>
                                    @if($options['SupplierOptions']['Industry'])
                                        <td colspan="1">
                                            <span><strong>Industry:</strong> {{ $supplier->industry }}</span>
                                        </td>
                                    @endif
                                    @if($options['SupplierOptions']['Type'])
                                        <td colspan="1">
                                            <span><strong>Type:</strong> {{ $supplier->type }}</span>
                                        </td>
                                    @endif
                                </tr>
                                @endif
                                @if($options['SupplierOptions']['Email'] || $options['SupplierOptions']['Phone'] || $options['SupplierOptions']['Fax'])
                                <tr>
                                    <td colspan="3">
                                        <table class="bg-highlight print-friendly" style="margin-bottom:30px;">
                                            <tr>
                                                @if($options['SupplierOptions']['Email'])
                                                    <td class="border">
                                                        <span><strong>Email:</strong> {{ $supplier->email }}</span>
                                                    </td>
                                                @endif
                                                @if($options['SupplierOptions']['Phone'])
                                                    <td class="border">
                                                        <span><strong>Phone:</strong> {{ $supplier->phone }}</span>
                                                    </td>
                                                @endif
                                                @if($options['SupplierOptions']['Fax'])
                                                    <td class="border">
                                                        <span><strong>Fax:</strong> {{ $supplier->fax }}</span>
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>
                    
                        @endforeach  

                    </td>
                </tr>
            </table> 

        @endif

    </body>
  </html>
  