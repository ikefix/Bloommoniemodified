<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    // Notification via database
    public function via($notifiable)
    {
        return ['database']; // Ensure you're sending it through the 'database' channel
    }

    // Store the notification in the database
    public function toDatabase($notifiable)
    {
        return [
            'product_name' => $this->product->name,
            'stock_quantity' => $this->product->stock_quantity,
            'message' => 'Stock is low for ' . $this->product->name,
        ];
    }
}





