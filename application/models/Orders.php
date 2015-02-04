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
    function add_item($num, $code) {
        if ($record = $this->orderitems->get($num, $code))
        {
            $this->orderitems->update($record->quantity++);
        }
        else
        {
            $record = $this->orderitems->create();
            $this->orderitems->update($record->quantity++);
        }
        
    }

    // calculate the total for an order
    function total($num) {
        // Get all the items(rows) that are linked to this order
        $total = 0;
        $items = $this->orderitems->get('order', $num);
        foreach ($items as $item)
        {
            $total = $item['quantity'] * $item['price'];
        }
        
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
