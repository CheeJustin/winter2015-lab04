<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code)
    {
        
        if ($record = $this->orderitems->get($num, $code))
        {
            $record->quantity++;
            $this->orderitems->update($record);
        }
        else
        {
            $record = $this->orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $this->orderitems->add($record);
        }
        
        $this->total($num);
    }

    // calculate the total for an order
    function total($num) {
        // Get all the items(rows) that are linked to this order
        $total = 0;
        $items = $this->orderitems->some('order', $num);
        
        //var_dump($items);
        foreach ($items as $item)
        {
            $itemPrice = $this->menu->get($item->item)->price;
            $itemQuantity = $item->quantity;
            
            $total += $itemQuantity * $itemPrice;
        }
        
        //var_dump($total);
        $order = $this->orders->get($num);
        $order->total = $total;
        $this->orders->update($order);
        
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        return false;
    }

}
