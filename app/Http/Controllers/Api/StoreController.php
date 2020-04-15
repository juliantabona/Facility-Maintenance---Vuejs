<?php

namespace App\Http\Controllers\Api;

use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getStores()
    {
        //  Check if the user is authourized to view all stores
        if ($this->user->can('viewAll', Store::class)) {

            //  Get the stores
            $stores = Store::paginate();

            //  Check if the stores exist
            if ($stores) {

                //  Return an API Readable Format of the Store Instance
                return ( new \App\Store() )->convertToApiFormat($stores);

            } else {

                //  Not Found
                return oq_api_notify_no_resource();

            }
        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function createStore(Request $request)
    {
        //  Check if the user is authourized to create the store
        if ($this->user->can('create', Store::class)) {

            //  Create store
            $store = ( new Store )->initiateCreate( $storeInfo = $request->all() );

            //  Check if the store was created
            if ($store) {
                
                //  Return an API Readable Format of the Store Instance
                return $store->convertToApiFormat();

            } else {

                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getStore($store_id)
    {
        //  Get the store
        $store = Store::where('id', $store_id)->first() ?? null;

        //  Check if the store exists
        if ($store) {

            //  Check if the user is authourized to view the store
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Store Instance
                return $store->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function updateStore(Request $request, $store_id)
    {
        //  Check if the user is authourized to update the store
        if ($this->user->can('update', Store::class)) {

            //  Get the updated store
            $store = Store::find($store_id);

            //  Update store
            $updatedStore = $store->initiateUpdate( $storeInfo = $request->all() );

            //  Check if the store was updated
            if ($updatedStore) {
                
                //  Return an API Readable Format of the Store Instance
                return $updatedStore->convertToApiFormat();

            } else {

                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    /*********************************
     *  OWNERSHIP RELATED RESOURCES  *
    *********************************/

    public function getStoreAccount($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store account
        $owner = $store->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the store owner
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Owning Model Instance
                return $owner->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DOCUMENT RELATED RESOURCES   *
    *********************************/

    public function getStoreLogo($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store logo
        $logo = $store->logo ?? null;

        //  Check if the logo exists
        if ($logo) {

            //  Check if the user is authourized to view the store logo
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Document Instance
                return $logo->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreDocuments($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store documents
        $documents = $store->documents()->paginate() ?? null;

        //  Check if the documents exist
        if ($documents) {

            //  Check if the user is authourized to view the store documents
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Document Instance
                return ( new \App\Document() )->convertToApiFormat($documents);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreDocument($store_id, $document_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store document
        $document = $store->documents()->where('id', $document_id)->first() ?? null;

        //  Check if the document exists
        if ($document) {

            //  Check if the user is authourized to view the store document
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Document Instance
                return $document->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  PHONE RELATED RESOURCES      *
    *********************************/

    public function getStorePhones($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the type if set
        $type = request()->input('type');

        if( $type = request()->input('type') ){

            //  Get the filtered store phones
            $phones = $store->phones()->whereType($type)->paginate() ?? null;
            
        }else{

            //  Get the store phones
            $phones = $store->phones()->paginate() ?? null;

        }


        //  Check if the phones exist
        if ($phones) {

            //  Check if the user is authourized to view the store phones
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Phone Instance
                return ( new \App\Phone() )->convertToApiFormat($phones);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
                
            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreDefaultPhone($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store default mobile
        $phone = $store->default_mobile ?? null;

        //  Check if the phone exists
        if ($phone) {

            //  Check if the user is authourized to view the store phone
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Phone Instance
                return $phone->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStorePhone($store_id, $phone_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store phone
        $phone = $store->phones()->where('id', $phone_id)->first() ?? null;

        //  Check if the phone exists
        if ($phone) {

            //  Check if the user is authourized to view the store phone
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Phone Instance
                return $phone->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  ADDRESS RELATED RESOURCES    *
    *********************************/

    public function getStoreAddresses($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store addresses
        $addresses = $store->addresses()->paginate() ?? null;


        //  Check if the addresses exist
        if ($addresses) {

            //  Check if the user is authourized to view the store addresses
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Address Instance
                return ( new \App\Address() )->convertToApiFormat($addresses);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
                
            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreAddress($store_id, $address_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store address
        $address = $store->addresses()->where('id', $address_id)->first() ?? null;

        //  Check if the address exists
        if ($address) {

            //  Check if the user is authourized to view the store address
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Address Instance
                return $address->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  EMAIL RELATED RESOURCES    *
    *********************************/

    public function getStoreEmails($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store emails
        $emails = $store->emails()->paginate() ?? null;


        //  Check if the emails exist
        if ($emails) {

            //  Check if the user is authourized to view the store emails
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Email Instance
                return ( new \App\Email() )->convertToApiFormat($emails);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
                
            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreEmail($store_id, $email_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store email
        $email = $store->emails()->where('id', $email_id)->first() ?? null;

        //  Check if the email exists
        if ($email) {

            //  Check if the user is authourized to view the store email
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Email Instance
                return $email->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  CONTACT RELATED RESOURCES    *
    *********************************/

    public function getStoreContacts($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the type if set
        $type = request()->input('type');

        //  Get the with mobile if set
        $withMobile = request()->input('withMobile');

        //  Filter by type e.g customer/vendor
        if( $type == 'customer' ){

            //  Filter to get only customer contacts
            $contacts = $store->customerContacts();
            
        }elseif( $type == 'vendor' ){

            //  Filter to get only vendor contacts
            $contacts = $store->vendorContacts();

        }else{

            //  Get the contacts unfiltered
            $contacts = $store->contacts();
            
        }

        //  Filter by mobile (Whether or not the contact has a mobile number)
        if( $withMobile == 'true' ){

            //  Filter to get only customer contacts
            $contacts = $contacts->withMobilePhone();
            
        }

        //  Paginate the results 
        $contacts = $contacts->paginate();

        //  Check if the contacts exist
        if ($contacts) {

            //  Check if the user is authourized to view the store contacts
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Contact Instance
                return ( new \App\Contact() )->convertToApiFormat($contacts);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
                
            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreContact($store_id, $contact_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store contact
        $contact = $store->contacts()->where('id', $contact_id)->first() ?? null;

        //  Check if the contact exists
        if ($contact) {

            //  Check if the user is authourized to view the store contact
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Contact Instance
                return $contact->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  USER RELATED RESOURCES       *
    *********************************/

    public function getStoreUsers($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store users
        $users = $store->users()->paginate() ?? null;

        //  Check if the users exist
        if ($users) {

            //  Check if the user is authourized to view the store users
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User() )->convertToApiFormat($users);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreAdmins($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store admins
        $admins = $store->admins()->paginate() ?? null;

        //  Check if the admins exist
        if ($admins) {

            //  Check if the user is authourized to view the store admins
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User() )->convertToApiFormat($admins);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreStaff($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store staff
        $staff = $store->staff()->paginate() ?? null;

        //  Check if the staff exists
        if ($staff) {

            //  Check if the user is authourized to view the store staff
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the User Instance
                return ( new \App\User() )->convertToApiFormat($staff);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreUser($store_id, $user_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store phone
        $user = $store->users()->where('users.id', $user_id)->first() ?? null;

        //  Check if the user exists
        if ($user) {

            //  Check if the user is authourized to view the store user
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the User Instance
                return $user->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /****************************************
     *  USSD INTERFACE RELATED RESOURCES    *
    ****************************************/

    public function getStoreUssdInterface($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store Ussd Interface
        $ussdInterface = $store->ussdInterface ?? null;

        //  Check if the Ussd Interface exists
        if ($ussdInterface) {

            //  Check if the user is authourized to view the store ussdInterface
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Contact Instance
                return $ussdInterface->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  PRODUCT RELATED RESOURCES    *
    *********************************/

    public function getStoreProducts($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);
        
        //  Get the store products
        $products = $store->products()->paginate() ?? null;

        //  Check if the products exist
        if ($products) {

            //  Check if the user is authourized to view the store products
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Product Instance
                return ( new \App\Product() )->convertToApiFormat($products);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreProduct($store_id, $product_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store product
        $product = $store->products()->where('id', $product_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the store product
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Product Instance
                return $product->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  ORDER RELATED RESOURCES      *
    *********************************/

    public function getStoreOrders(Request $request, $store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store orders
        $orders = $store->orders();

        //  If we have specified filters
        if( $request->get('status') || $request->get('paymentStatus') || $request->get('fulfillmentStatus') ){

            if( !empty( $request->get('status') ) ){

                $status = strtolower( trim( $request->get('status') ));

                //  Filter by status e.g open, draft, archieved, cancelled
                $orders = $orders->where('status', $status);

            }

            if( !empty( $request->get('paymentStatus') ) ){

                $statuses = $request->get('paymentStatus');
    
                //  Get statues and separate into an array by comma separator
                $statuses = explode(',', $statuses);
    
                //  Foreach status
                $statuses = collect($statuses)->map(function($status){
    
                    //  Trim the status text and lowercase every word
                    return strtolower( trim($status) );
    
                })->toArray();

                //  Filter by payment status e.g paid, unpaid, pending
                $orders = $orders->whereIn('payment_status', $statuses);

            }

            if( !empty( $request->get('fulfillmentStatus') ) ){

                $statuses = $request->get('fulfillmentStatus');
    
                //  Get statues and separate into an array by comma separator
                $statuses = explode(',', $statuses);

                //  Foreach status
                $statuses = collect($statuses)->map(function($status){

                    //  Trim the status text and lowercase every word
                    return strtolower( trim($status) );

                })->toArray();

                //  Filter by fulfillment status e.g fulfilled, unfulfilled
                $orders = $orders->whereIn('fulfillment_status', $statuses);

            }
        
        }
                
        //  Paginate the results
        $orders = $orders->paginate();

        //  Check if the orders exist
        if ($orders) {

            //  Check if the user is authourized to view the store orders
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Order Instance
                return ( new \App\Order() )->convertToApiFormat($orders);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreOrder($store_id, $order_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store order
        $order = $store->orders()->where('orders.id', $order_id)->first() ?? null;

        //  Check if the order exists
        if ($order) {

            //  Check if the user is authourized to view the store order
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Order Instance
                return $order->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  TAX RELATED RESOURCES        *
    *********************************/

    public function getStoreTaxes($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store taxes
        $taxes = $store->taxes()->paginate() ?? null;

        //  Check if the taxes exist
        if ($taxes) {

            //  Check if the user is authourized to view the store taxes
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Tax Instance
                return ( new \App\Tax() )->convertToApiFormat($taxes);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreTax($store_id, $tax_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store tax
        $tax = $store->taxes()->where('taxes.id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the store tax
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Tax Instance
                return $tax->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  DISCOUNT RELATED RESOURCES   *
    *********************************/

    public function getStoreDiscounts($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store discounts
        $discounts = $store->discounts()->paginate() ?? null;

        //  Check if the discounts exist
        if ($discounts) {

            //  Check if the user is authourized to view the store discounts
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Discount Instance
                return ( new \App\Discount() )->convertToApiFormat($discounts);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreDiscount($store_id, $discount_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store discount
        $discount = $store->discounts()->where('discounts.id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the store discount
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Discount Instance
                return $discount->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  COUPONS RELATED RESOURCES    *
    *********************************/

    public function getStoreCoupons($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store coupons
        $coupons = $store->coupons()->paginate() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the store coupons
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Coupon Instance
                return ( new \App\Coupon() )->convertToApiFormat($coupons);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreCoupon($store_id, $coupon_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store coupons
        $coupons = $store->coupons()->where('coupons.id', $coupon_id)->first() ?? null;

        //  Check if the coupons exist
        if ($coupons) {

            //  Check if the user is authourized to view the store coupons
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Coupon Instance
                return $coupons->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  MESSAGE RELATED RESOURCES    *
    *********************************/

    public function getStoreMessages($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store messages
        $messages = $store->messages()->paginate() ?? null;

        //  Check if the messages exist
        if ($messages) {

            //  Check if the user is authourized to view the store messages
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Message Instance
                return ( new \App\Message() )->convertToApiFormat($messages);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreMessage($store_id, $message_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store message
        $message = $store->messages()->where('messages.id', $message_id)->first() ?? null;

        //  Check if the message exists
        if ($message) {

            //  Check if the user is authourized to view the store message
            if ($this->user->can('view', $store)) {
                
                //  Return an API Readable Format of the Message Instance
                return $message->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  REVIEW RELATED RESOURCES     *
    *********************************/

    public function getStoreReviews($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store reviews
        $reviews = $store->reviews()->paginate() ?? null;

        //  Check if the reviews exist
        if ($reviews) {

            //  Check if the user is authourized to view the store reviews
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Review Instance
                return ( new \App\Review() )->convertToApiFormat($reviews);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreReview($store_id, $review_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store review
        $review = $store->reviews()->where('reviews.id', $review_id)->first() ?? null;

        //  Check if the review exists
        if ($review) {

            //  Check if the user is authourized to view the store review
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Reviews Instance
                return $review->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreSettings($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store settings
        $settings = $store->settings ?? null;

        //  Check if the settings exist
        if ($settings) {

            //  Check if the user is authourized to view the store settings
            if ($this->user->can('view', $store)) {

                //  Return an API Readable Format of the Setting Instance
                return $settings->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getStoreStatistics($store_id)
    {
        //  Get the store
        $store = Store::findOrFail($store_id);

        //  Get the store statistics
        $statistics = $store->statistics ?? null;

        //  Check if the statistics exist
        if ($statistics) {

            //  Check if the user is authourized to view the store statistics
            if ($this->user->can('view', $store)) {

                //  Return the statistics
                return $statistics;

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        } else {

            //  Not Found
            return oq_api_notify_no_resource();

        }
    }


    /*
    public function index()
    {
        //  Store Instance
        $data = ( new Store() )->initiateGetAll();
        $success = $data['success'];
        $response = $data['response'];

        //  If the stores were found successfully
        if ($success) {
            //  If this is a success then we have the stores
            $stores = $response;

            //  Action was executed successfully
            return oq_api_notify($stores, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function show($store_id)
    {
        //  Store Instance
        $data = ( new Store() )->initiateShow($store_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the store was found successfully
        if ($success) {
            //  If this is a success then we have the store
            $store = $response;

            //  Action was executed successfully
            return oq_api_notify($store, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the store
        $data = ( new Store() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the store was created successfully
        if ($success) {
            //  If this is a success then we have a store returned
            $store = $response;

            //  Action was executed successfully
            return oq_api_notify($store, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($store_id)
    {
        //  Store Instance
        $data = ( new Store() )->initiateUpdate($store_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the store was updated successfully
        if ($success) {
            //  If this is a success then we have a store returned
            $store = $response;

            //  Action was executed successfully
            return oq_api_notify($store, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getImage(Request $request, $store_id)
    {
        try {
            //  Get the associated store
            $store = Store::where('id', $store_id)->first();
            $storeImage = $store->primaryImage;

            //  Action was executed successfully
            return oq_api_notify($storeImage, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }
    */
}
