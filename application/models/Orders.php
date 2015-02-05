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
    function total($num)
    {
        // Get all the items(rows) that are linked to this order
        $total = 0;
        $items = $this->orderitems->some('order', $num);
        
        foreach ($items as $item)
        {
            $itemPrice = $this->menu->get($item->item)->price;
            $itemQuantity = $item->quantity;
            
            $total += $itemQuantity * $itemPrice;
        }
        
        $order = $this->orders->get($num);
        $order->total = $total;
        $this->orders->update($order);
        
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        $items = $this->orderitems->some('order', $num);
        foreach ($items as $item)
        {
            $item->price = "$" . $this->menu->get($item->item)->price;
            $item->name = $this->menu->get($item->item)->name;
        }
        
        
        return $items;
    }

    // cancel an order
    function flush($num)
    {
        $this->orderitems->delete_some($num);
        $record = $this->orders->get($num);
        $record->status = 'x';
        $record->total = 0;
        $this->orders->update($record);
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num)
    {
        $items = $this->orderitems->some('order', $num);
        
        $meal = false;
        $drink = false;
        $snack = false;
        
        foreach ($items as $item)
        {
            switch($this->menu->some('code', $item->item)[0]->category)
            {
                case 'm':
                    $meal = true;
                    break;
                case 'd':
                    $drink = true;
                    break;
                case 's':
                    $snack = true;
                    break;
            }
            
            if ($meal && $drink && $snack)
                return true;
        }
        
        return false;
    }
    
    function complete($num)
    {
        $record = $this->orders->get($num);
        $record->date = date("Y/m/d/H/i/s");
        $record->status = 'c';
        
        $this->orders->update($record);
    }

}
