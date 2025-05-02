<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shopping Cart</title>
  <link rel="icon" href="assets/img/favicon.png"/>
  <style>
    /* General Reset and Styling */
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 0;
      text-align: center;
    }

    header {
      background-color: #007bff;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      cursor: pointer;
    }

    #cartCount {
      background-color: #ff5722;
      color: white;
      padding: 8px 15px;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.3s;
    }

    #cartCount:hover {
      background-color: #e64a19;
      transform: scale(1.1);
    }

    #cartCount:active {
      background-color: #d84315;
      transform: scale(0.95);
    }

    main {
      max-width: 700px;
      margin: 20px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    ul {
      list-style-type: none;
      padding: 0;
    }

    li {
      background: #ffffff;
      margin: 10px 0;
      padding: 15px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    li:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .item-details {
      flex: 1;
      text-align: left;
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .quantity-btn, .remove-btn {
      padding: 5px 10px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .quantity-btn {
      background-color: #28a745;
      color: white;
    }

    .quantity-btn:hover {
      background-color: #218838;
    }

    .remove-btn {
      background-color: #dc3545;
      color: white;
    }

    .remove-btn:hover {
      background-color: #c82333;
    }

    .quantity-display {
      font-size: 16px;
      font-weight: bold;
      width: 24px;
      display: inline-block;
      text-align: center;
    }

    .total-section {
      font-size: 18px;
      font-weight: bold;
      margin: 20px 0;
    }

    button#checkoutBtn {
      background-color: #28a745;
      color: white;
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button#checkoutBtn:hover {
      background-color: #218838;
    }

    button#checkoutBtn:disabled {
      background-color: #6c757d;
      cursor: not-allowed;
    }
  </style>
</head>
<body>

  <header>
    <h1 onclick="location.href='afterlogin.php'">Shopping Cart</h1>
    <div id="cartCount">Cart: 0</div>
  </header>

  <main>
    <ul id="cartItems"></ul>

    <div class="total-section">
      Total: Rs.<span id="cartTotal">0.00</span>
    </div>

    <button id="checkoutBtn" disabled >Checkout</button>
  </main>

  <script>
    window.history.forward();
    function noBack() {
      window.history.forward();
    }
    window.onload = noBack;

    function loadCart() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const cartItemsContainer = document.getElementById('cartItems');
      const cartTotalElem = document.getElementById('cartTotal');
      const checkoutBtn = document.getElementById('checkoutBtn');

      cartItemsContainer.innerHTML = '';
      let total = 0;

      cart.forEach((item, index) => {
        const listItem = document.createElement('li');

        const itemDetails = document.createElement('div');
        itemDetails.className = 'item-details';
        itemDetails.textContent = `${item.name} - Rs.${item.price}`;

        const quantityControl = document.createElement('div');
        quantityControl.className = 'quantity-control';

        const decreaseBtn = createButton('-', 'quantity-btn', () => updateQuantity(index, item.quantity - 1));
        const quantityDisplay = document.createElement('span');
        quantityDisplay.className = 'quantity-display';
        quantityDisplay.textContent = item.quantity;

        const increaseBtn = createButton('+', 'quantity-btn', () => updateQuantity(index, item.quantity + 1));
        quantityControl.append(decreaseBtn, quantityDisplay, increaseBtn);

        const removeBtn = createButton('Remove', 'remove-btn', () => removeItem(index));

        listItem.append(itemDetails, quantityControl, removeBtn);
        cartItemsContainer.appendChild(listItem);

        total += item.price * item.quantity;
      });

      cartTotalElem.textContent = total.toFixed(2);
      document.getElementById('cartCount').textContent = `Cart: ${cart.length}`;
      checkoutBtn.disabled = total === 0;
    }

    function createButton(label, className, onClick) {
      const button = document.createElement('button');
      button.textContent = label;
      button.className = className;
      button.onclick = onClick;
      return button;
    }

    function updateQuantity(index, newQty) {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      if (newQty <= 0) {
        cart.splice(index, 1);
      } else {
        cart[index].quantity = newQty;
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      loadCart();
    }

    function removeItem(index) {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      cart.splice(index, 1);
      localStorage.setItem('cart', JSON.stringify(cart));
      loadCart();
    }

    document.getElementById('checkoutBtn').addEventListener('click', () => {
      const user = JSON.parse(localStorage.getItem('user'));
      if (!user) {
        window.location.href = '/database/checkout.php';
      } else {
        alert('Proceeding to checkout...');
        // Add further checkout logic here
      }
    });

    loadCart();
  </script>
</body>
</html>
