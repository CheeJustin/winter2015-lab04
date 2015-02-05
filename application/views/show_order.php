<p class="lead">
    Order # {order_num} for {total}
</p>
<table class="item-table">
<tr>
    <td>Quantity</td><td>Price</td><td>Item #</td><td>Item Description</td>
</tr>
{items}
<tr>
    <td>{quantity}</td>
    <td>{price}</td>
    <td>{item}</td>
    <td>{name}</td>
</tr>
{/items}
</table>
<div class="row">
    <a href="/order/proceed/{order_num}" class="btn btn-large btn-success {okornot}">Proceed</a>
    <a href="/order/display_menu/{order_num}" class="btn btn-large btn-primary">Keep shopping</a>
    <a href="/order/cancel/{order_num}" class="btn btn-large btn-danger">Forget about it</a>
</div>