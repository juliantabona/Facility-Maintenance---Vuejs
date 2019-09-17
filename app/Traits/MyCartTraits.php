<?php

namespace App\Traits;

use Cart;
use App\Store;
use App\Product;

trait MyCartTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return all the cart items.
     *
     */
    public function initiateGetAll()
    {
    }

    public function initiateGetCartDetails($cart, $storeId)
    {
        try {
            //  Get the store and settings
            $store = Store::find($storeId);

            $cart_items = $this->buildItems($cart['items']);

            //  Total of only the cart items combined
            $sub_total = 0;

            //  Total of only the cart taxes combined
            $item_tax_total = 0;

            //  Total of only the store taxes combined
            $global_tax_total = 0;

            //  Total of only the store taxes and cart taxes combined
            $grand_tax_total = 0;

            //  Total of only the cart discounts combined
            $item_discount_total = 0;

            //  Total of only the store discounts combined
            $global_discount_total = 0;

            //  Total of only the store discounts and cart discounts combined
            $grand_discount_total = 0;

            //  The total of shipping costs
            $shipping_total = 0;

            //  The total of all the cart item costs and taxes
            $grand_total = 0;

            foreach ($cart_items as $key => $item) {
                //  If we have a sale price then use the sale price otherwise use the regular price
                $price = !empty($item['unit_sale_price']) ? $item['unit_sale_price'] : $item['unit_price'];
                $item_total_price = ($price * $item['quantity']);

                //  Calculate the sub total
                $sub_total += $item_total_price;

                //  Calculate the cart item taxes
                foreach ($item['taxes'] as $key => $tax) {
                    $item_tax_total += $tax['rate'] * $item_total_price;
                }

                //  Calculate the cart item discounts
                foreach ($item['discounts'] as $key => $discount) {
                    //  If its a percentage rate based discount
                    if ($discount['type'] == 'rate') {
                        $item_discount_total += $discount['rate'] * $item_total_price;

                    //  If its a fixed rate based discount
                    } elseif ($discount['type'] == 'fixed') {
                        $item_discount_total += $discount['amount'];
                    }
                }
            }

            //  Calculate the store taxes
            foreach ($store['taxes'] as $key => $tax) {
                $global_tax_total += $tax['rate'] * $sub_total;
            }

            //  Calculate the store discounts
            foreach ($store['discounts'] as $key => $discount) {
                //  If its a percentage rate based discount
                if ($discount['type'] == 'rate') {
                    $global_discount_total += $discount['rate'] * $sub_total;

                //  If its a fixed rate based discount
                } elseif ($discount['type'] == 'fixed') {
                    $global_discount_total += $discount['amount'];
                }
            }

            //  Calculate the grand total tax
            $grand_tax_total = $item_tax_total + $global_tax_total;

            //  Calculate the grand total discount
            $grand_discount_total = $item_discount_total + $global_discount_total;

            //  Calculate the grand total
            $grand_total = $sub_total + $grand_tax_total + $shipping_total - $grand_discount_total;

            $cartDetails = [
                'items' => $cart_items,
                'item_tax_total' => $item_tax_total,
                'global_tax_total' => $global_tax_total,
                'grand_tax_total' => $grand_tax_total,
                'item_discount_total' => $item_discount_total,
                'global_discount_total' => $global_discount_total,
                'grand_discount_total' => $grand_discount_total,
                'grand_total' => $grand_total,
            ];

            //  Action was executed successfully
            return ['success' => true, 'response' => $cartDetails];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    public function buildItems($items)
    {
        $cartItems = [];

        //  Lets get the ids of the items in the cart
        $relatedItemIds = collect($items)->map(function ($item) {
            return $item['id'];
        });

        //  Lets get the items from the db related to the cart
        $relatedItems = Product::whereIn('id', $relatedItemIds)->get();

        foreach ($items as $key => $item) {
            foreach ($relatedItems as $key => $relatedItem) {
                if ($relatedItem['id'] == $item['id']) {
                    //  If we have a sale price then use the sale price otherwise use the regular price
                    $price = ($relatedItem['unit_sale_price']) ? $relatedItem['unit_sale_price'] : $relatedItem['unit_price'];
                    $quantity = ($item['quantity'] ?? 1);
                    $sub_total = $price * $quantity;
                    $tax_total = 0;
                    $discount_total = 0;
                    $grand_total = 0;

                    //  Calculate the item taxes
                    foreach ($relatedItem['taxes'] as $key => $tax) {
                        $tax_total = $tax_total + ($tax['rate'] * $sub_total);
                    }

                    //  Calculate the item discounts
                    foreach ($relatedItem['discounts'] as $key => $discount) {
                        //  If its a percentage rate based discount
                        if ($discount['type'] == 'rate') {
                            $discount_total = $discount_total + ($discount['rate'] * $sub_total);

                        //  If its a fixed rate based discount
                        } elseif ($discount['type'] == 'fixed') {
                            $discount_total = $discount_total + ($discount['amount']);
                        }
                    }

                    //  Calculate the grand total
                    $grand_total = $sub_total + $tax_total - $discount_total;

                    $cartItem = [
                        'id' => $relatedItem['id'],
                        'primary_image' => $relatedItem['primary_image'],
                        'name' => $relatedItem['name'],
                        'store_currency_symbol' => $relatedItem['store_currency_symbol'],
                        'unit_price' => $relatedItem['unit_price'],
                        'unit_sale_price' => $relatedItem['unit_sale_price'],
                        'quantity' => $quantity,
                        'sub_total' => $sub_total,
                        'tax_total' => $tax_total,
                        'grand_total' => $grand_total,
                        'taxes' => $relatedItem['taxes'],
                        'discounts' => $relatedItem['discounts'],
                        'selectedVariable' => null,
                    ];

                    array_push($cartItems, $cartItem);
                }
            }
        }

        return $cartItems;
    }

    public function initiateCreate()
    {
    }

    public function initiateUpdateItem()
    {
    }

    public function initiateDeleteItem()
    {
    }

    public function initiateEmptyCart()
    {
    }
}
