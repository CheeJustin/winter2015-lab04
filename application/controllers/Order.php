<?php

/**
 * Order handler
 * 
 * Implement the different order handling usecases.
 * 
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Order extends Application {

    function __construct() {
        parent::__construct();
    }

    // start a new order
    function neworder()
    {
        $order_num = $this->orders->highest() + 1;
        
        date_default_timezone_set("America/Vancouver");
        
        $newOrder = $this->orders->create();
        $newOrder->num = $order_num;
        $newOrder->date = date("Y/m/d/H/i/s");
        $newOrder->status = "a";
        $newOrder->total = 0;
        
        $this->orders->add($newOrder);
        
        $this->display_menu($order_num);
    }

    // add to an order
    function display_menu($order_num = null, $warning = "") {
        if ($order_num == null)
            $this->neworder();

        $this->data['warning'] = $warning;
        $this->data['pagebody'] = 'show_menu';
        $this->data['order_num'] = $order_num;
        $this->data['title'] = "Order: #" . $order_num . " || Total: $" . $this->orders->get($order_num)->total;
        
        // Make the columns
        $this->data['meals'] = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');
        
        foreach ($this->data['meals'] as $meal)
        {
            $meal->order_num = $order_num;
        }
        foreach ($this->data['drinks'] as $meal)
        {
            $meal->order_num = $order_num;
        }
        foreach ($this->data['sweets'] as $meal)
        {
            $meal->order_num = $order_num;
        }
        
        $this->render();
    }

    // make a menu ordering column
    function make_column($category)
    {
        return $this->menu->some('category', $category);
    }

    // add an item to an order
    function add($order_num, $item)
    {
        $this->orders->add_item($order_num, $item);
        $this->display_menu($order_num);
    }

    // checkout
    function checkout($order_num)
    {
        // If order is no valid, display a warning.
        if (!$this->orders->validate($order_num))
        {
            $this->display_menu ($order_num, "Please select an item from each catagory");
            return;
        }
        
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;
        $this->data['total'] = $this->orders->get($order_num)->total;
        $this->data['items'] = $this->orders->details($order_num);
        
        $this->render();
    }

    // proceed with checkout
    function proceed($order_num)
    {
        $this->orders->complete($order_num);
        $this->data['pagebody'] = 'welcome';
        $this->render();
    }

    // cancel the order
    function cancel($order_num)
    {
        $this->orders->flush($order_num);
        $this->data['pagebody'] = 'welcome';
        $this->render();
    }

}
