import auth from './auth.js';

class Cart {
    constructor () {
        
        console.log('Cart.js - Get the users cart in local storage');

        //  Get the cart stored in the local storage
        this.cart = window.localStorage.getItem('cart') ? JSON.parse( window.localStorage.getItem('cart') ) : [];
    
        //  If the cart does not exist
        if (!this.cart) {
            //  If the the user is authenticated
            if( auth.check() ){
                // Check if the authenticated user has a cart stored in the database
                this.getDBCart();
            }
        }
    }

    getDBCart() {

        console.log('Cart.js - Get authenticated user cart stored in database');

        //  Make an Api call to get the authenticated user with the assigned auth cart
        return api.call('get', '/api/cart')
            .then(({data}) => {
                //  Update the cart object
                this.cart = data;
                //  Update the cart local storage
                window.localStorage.setItem('cart', JSON.stringify(data));
            });
    }

    buildItem (item) {   

        console.log('Cart.js - Build cart item');   
        console.log(item);

        //  If we have a sale price then use the sale price otherwise use the regular price
        var price = (item['unit_sale_price']) ? item['unit_sale_price'] : item['unit_price'];
        var quantity = (item['quantity'] || 1);
        var sub_total = price * quantity;
        var tax_total = 0;
        var discount_total = 0;
        var grand_total = 0;

        //  Calculate the item taxes
        for( var x=0; x < (item['taxes'] || {}).length; x++){
            tax_total = tax_total + (item['taxes'][x]['rate'] * sub_total);
        }

        //  Calculate the item discounts
        for( var x=0; x < (item['discounts'] || {}).length; x++){

            //  If its a percentage rate based discount
            if( item['discounts'][x]['type'] == 'rate' ){

                discount_total = discount_total + (item['discounts'][x]['rate'] * sub_total);

            //  If its a fixed rate based discount
            }else if( item['discounts'][x]['type'] == 'fixed' ){

                discount_total = discount_total + (item['discounts'][x]['amount']);

            }
        }

        //  Calculate the grand total
        grand_total = sub_total + tax_total - discount_total;

        var cartItem = {
                id: item.id || null,
                primary_image: item.primary_image || null,
                name: item.name || '',
                store_currency_symbol: item.store_currency_symbol || '',
                unit_price: item.unit_price || 0,
                unit_sale_price: item.unit_sale_price || 0,
                quantity: quantity,
                sub_total: sub_total || 0,
                tax_total: tax_total || 0,
                grand_total: grand_total || 0,
                taxes: item.taxes || [],
                discounts: item.discounts || [],
                selectedVariable: null
        }

        return cartItem;
    }

    addItem (item) {   

        console.log('Cart.js - Add item to the cart');   
        console.log(item);

        var cartItem = this.buildItem(item);    
        
        //  Lets check if the cart has items in it
        if( (this.cart.items || {}).length ){
            //  Push item into already existing items
            this.cart.items.push( cartItem );
        }else{
            //  Add item as the first in the cart
            this.cart.items = [cartItem];
        }

        //  Lets update the local cart to calculat the subtotals, grand
        this.updateCart(this.cart);
    }

    updateItemQuantity (item, action) {
        
        for( var x=0; x < (this.cart.items || {}).length; x++){
            
            if( item.id == this.cart.items[x].id){
                
                var currentQuantity = parseInt(item['quantity']);

                //  If the action is to increase the quantity of the item
                if(action == 'add'){
                    
                    //  Increase quantity
                    item['quantity'] = currentQuantity + 1;

                //  If the action is to reduce the quantity of the item
                }else if(action == 'subtract'){
                    
                    if( (currentQuantity - 1) != 0 ){
                        //  Increase quantity
                        item['quantity'] = currentQuantity - 1;
                    }

                }else{
                    //  Update with custom number
                    item['quantity'] = (parseInt(action) || 1);

                }
                
                //  Update the cart item
                this.cart.items[x] = this.buildItem(item);

                //  Update the cart
                this.updateCart(this.cart);

            }
        }

    }

    removeItem (item) {
        
        for( var x=0; x < (this.cart.items || {}).length; x++){
            if( item.id == this.cart.items[x].id){

                //  Remove item from the cart
                this.cart.items.splice(x, 1);

                //  Update the cart
                this.updateCart(this.cart);

            }
        }

    }

    updateCart (cart) {   

        console.log('Cart.js - Update the cart');   

            //  Total of only the cart items combined
            var sub_total = 0;

            //  Total of only the cart taxes combined
            var item_tax_total = 0;

            //  Total of only the store taxes combined
            var global_tax_total = 0;

            //  Total of only the store taxes and cart taxes combined
            var grand_tax_total = 0;

            //  Total of only the cart discounts combined
            var item_discount_total = 0;

            //  Total of only the store discounts combined
            var global_discount_total = 0;

            //  Total of only the store discounts and cart discounts combined
            var grand_discount_total = 0;

            //  The total of shipping costs
            var shipping_total = 0;

            //  The total of all the cart item costs and taxes
            var grand_total = 0;

            //  The total of all the cart item costs and taxes
            var grand_total = 0;

            for( var x=0; x < (this.cart.items || {}).length; x++){
                
                //  If we have a sale price then use the sale price otherwise use the regular price
                var price = (this.cart.items[x]['unit_sale_price']) ? this.cart.items[x]['unit_sale_price'] : this.cart.items[x]['unit_price'];
                var quantity = (this.cart.items[x]['quantity'] || 1);
                var item_total_price = (price * quantity);

                //  Calculate the sub total 
                sub_total =  sub_total + item_total_price;

                //  Calculate the cart item taxes
                for( var y=0; y < (this.cart.items[x]['taxes'] || {}).length; y++){
                    item_tax_total = item_tax_total + (items[x]['taxes'][y]['rate'] * item_total_price);
                }

                //  Calculate the cart item discounts
                for( var y=0; y < (this.cart.items[x]['discounts'] || {}).length; y++){

                    //  If its a percentage rate based discount
                    if( this.cart.items[x]['discounts'][y]['type'] == 'rate' ){

                        item_discount_total = item_discount_total + (this.cart.items[x]['discounts'][y]['rate'] * item_total_price);

                    //  If its a fixed rate based discount
                    }else if( this.cart.items[x]['discounts'][y]['type'] == 'fixed' ){

                        item_discount_total = item_discount_total + (this.cart.items[x]['discounts'][y]['amount']);

                    }
                }

            }

            //  Calculate the store taxes
            for( var x=0; x < (this.cart.taxes || {}).length; x++){
                global_tax_total = global_tax_total + (this.cart.taxes[x]['rate'] * sub_total);
            }

            //  Calculate the cart item discounts
            for( var x=0; x < (this.cart.discounts || {}).length; x++){

                //  If its a percentage rate based discount
                if( this.cart.discounts[x]['type'] == 'rate' ){

                    global_discount_total = global_discount_total + (this.cart.discounts[x]['rate'] * sub_total);

                //  If its a fixed rate based discount
                }else if( this.cart.discounts[x]['type'] == 'fixed' ){

                    global_discount_total = global_discount_total + (this.cart.discounts[x]['amount']);

                }
            }

            //  Calculate the grand total tax
            grand_tax_total = item_tax_total + global_tax_total;

            //  Calculate the grand total discount
            grand_discount_total = item_discount_total + global_discount_total;

            //  Calculate the grand total
            grand_total = sub_total + grand_tax_total + shipping_total - grand_discount_total;

            var updatedCart = {
                    items: this.cart.items,
                    sub_total: sub_total,
                    item_tax_total: item_tax_total,
                    global_tax_total: global_tax_total,
                    grand_tax_total: grand_tax_total,
                    item_discount_total: item_discount_total,
                    global_discount_total: global_discount_total,
                    grand_discount_total: grand_discount_total,
                    grand_total: grand_total
                }

        window.localStorage.removeItem('cart');
        
        this.cart = updatedCart;
        window.localStorage.setItem('cart', JSON.stringify(updatedCart));
        
        Event.$emit('cartUpdated', updatedCart);
    }

    destroyCart(){
        console.log('Cart.js - Remove local cart');

        localStorage.removeItem('cart');
        this.cart = null;
    
        Event.$emit('cartDestroyed');
    }

    check () {
        console.log('Cart.js - Check if we have a cart');   
        return !! this.cart;
    }

}

export default Cart;