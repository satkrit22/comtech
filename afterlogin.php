<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Comtech</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<body class="index-page">
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="home.html" class="logo d-flex align-items-center me-auto me-xl-0">
        <img src="assets/img/logo.png" alt="Company Logo">
        <h1 class="sitename">Comtech Group Co Pvt.Ltd</h1>
      </a>

      <nav id="navmenu" class="navmenu" aria-label="Main Navigation">
        <ul>
          <li><a href="#Sales" class="active">Sales</a></li>          
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
<div class="header-right d-flex align-items-center">
  
  <div class="search-box me-3">
    <form class="search-form d-flex" id="search-form">
        <input type="text" class="form-control" placeholder="Search products..." id="search-input">
        <button type="submit" class="btn btn-link"><i class="bi bi-search"></i></button>
    </form>
</div>

<script>
  document.getElementById("search-form").addEventListener("submit", function(event) {
    event.preventDefault();
    filterItems();
  });

  document.getElementById("search-input").addEventListener("input", function() {
    filterItems();
  });

  function filterItems() {
    const query = document.getElementById("search-input").value.trim().toLowerCase();
    const items = document.querySelectorAll(".menu-item");
    const tabs = document.querySelector(".nav-tabs");
    const tabContent = document.querySelector(".tab-content");
    const sectionTitle = document.querySelector(".section-title");
    const searchBox = document.querySelector(".search-box");

    let anyMatch = false;

    items.forEach(item => {
      const productName = item.querySelector("h4").innerText.toLowerCase();
      if (productName.includes(query)) {
        item.style.display = "block";
        anyMatch = true;
      } else {
        item.style.display = "none";
      }
    });

    if (query) {
      if (tabs) tabs.style.display = "none";
      if (tabContent) tabContent.style.display = "block"; // Keep tab-content open for showing matched items
      if (sectionTitle) sectionTitle.style.display = "block"; // Keep "Our Selling Items" title visible
      if (searchBox) searchBox.style.display = "block";
      const salesSection = document.getElementById("Sales");
      if (salesSection) {
        salesSection.scrollIntoView({ behavior: "smooth" });
      }
    } else {
      if (tabs) tabs.style.display = "flex";
      if (tabContent) tabContent.style.display = "block";
      if (sectionTitle) sectionTitle.style.display = "block";
      if (searchBox) searchBox.style.display = "block";

      items.forEach(item => {
        item.style.display = "block";
      });
    }
  }
</script>
  <div class="header-cart me-3">
    <a href="cart.html" class="header-cart-link position-relative">
      <i class="bi bi-cart3"></i>
      <span class="header-cart-count">0</span>
    </a>
  </div>
  <div class="dropdown">
    <a class="btn btn-link dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown">
      <i class="bi bi-person-circle"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="/database/signout.php">Logout</a></li>
  </div>
</div>
  </header>
  <main class="main">
    <!-- Sales Section -->
    <section id="Sales" class="menu section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Our Selling Items</h2>
        <p><span>Check Our</span> <span class="description-title">Selling Items</span></p>
      </div>
      <div class="container">
        <ul class="nav nav-tabs d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
          <li class="nav-item">
            <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#menu-starters">
              <h4>ALL</h4>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#menu-breakfast">
              <h4>Link PC</h4>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#menu-lunch">
              <h4>Computer Accessories</h4>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#menu-dinner">
              <h4>Laptop & Accessories</h4>
            </a>
          </li>
        </ul>
        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
          <div class="tab-pane fade active show" id="menu-starters">
            <div class="tab-header text-center">
              <h3>ALL</h3>
            </div>
            
            <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-start;">
              <!-- Link PC -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
                <div class="menu-item-box" style="gap: 5px;">
                  <a href="assets/img/menu/linkpc.jpg" class="glightbox">
                    <img src="assets/img/menu/menu-item-1.jpg" class="menu-img img-fluid" alt="Link PC" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                  <a href="productdetails/linkpc.html">
                  <h4>Link PC</h4>
                  <p class="price"><strike>Rs.14,500</strike><br>Rs.10,500</p> </a>
                  <div class="menu-actions">
                    <button class="btn btn-cart" onclick="addToCart('Link PC', 10500)">
                      <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                    <button class="btn btn-buy" onclick="buyNow('Link PC', 10500)">Buy Now</button>
                  </div>
                
                </div>
              </div>
              
              <!-- Link PC+ -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
                <div class="menu-item-box" style="gap: 10px;">
                  <a href="assets/img/menu/linkpc.jpg" class="glightbox">
                    <img src="assets/img/menu/menu-item-1.jpg" class="menu-img img-fluid" alt="Link PC+" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                  <a href="productdetails/linkpc+.html">
                  <h4>Link PC+</h4>
                  <p class="price"><strike>Rs.14,500</strike><br>Rs.13,500</p></a>                
                  <div class="menu-actions">
                    <button class="btn btn-cart" onclick="addToCart('Link PC+', 13500)">
                      <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                    <button class="btn btn-buy" onclick="buyNow('Link PC+', 13500)">Buy Now</button>
                  </div>
                
                </div>
              </div>
              
              <!-- Laptop Cooler -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
                <div class="menu-item-box" style="gap: 10px;">
                  <a href="assets/img/menu/s-l1600-9.jpg" class="glightbox">
                    <img src="assets/img/menu/menu-item-6.jpg" class="menu-img img-fluid" alt="Laptop Cooler" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                  <a href="productdetails/laptopcoller.html">
                  <h4>Laptop Cooler</h4>
                  <p class="price">Rs.1,500</p> </a>
                  <div class="menu-actions">
                    <button class="btn btn-cart" onclick="addToCart('Laptop Cooler',1500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                    <button class="btn btn-buy" onclick="buyNow('Laptop Cooler',1500)">Buy Now</button>
                  </div>
                
                </div>
              </div>
              
              <!-- Mouse -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
                <div class="menu-item-box" style="gap: 10px;">
                  <a href="assets/img/menu/t801-des-1.png" class="glightbox">
                    <img src="assets/img/menu/menu-item-4.png" class="menu-img img-fluid" alt="Mouse" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                  <a href="productdetails/imicemouse.html">
                  <h4>Mouse</h4>
                  <p class="price">Rs.1,500</p> </a>
                  <div class="menu-actions">
                    <button class="btn btn-cart" onclick="addToCart('Mouse',1500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                    <button class="btn btn-buy" onclick="buyNow('Mouse',1500)">Buy Now</button>
                  </div>
                
                </div>
              </div>
              
              <!-- Clamper -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
                <div class="menu-item-box" style="gap: 10px;">
                  <a href="assets/img/menu/61Hi4xhM9ML._AC_UF350,350_QL80_.jpg" class="glightbox">
                    <img src="assets/img/menu/menu-item-5.jpg" class="menu-img img-fluid" alt="Clamper" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                  <a href="productdetails/clamper.html"> </a>
                  <h4>Clamper</h4>
                  <p class="price">Rs.1,300</p>
                  <div class="menu-actions">
                    <button class="btn btn-cart" onclick="addToCart('Clamper',1300)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                    <button class="btn btn-buy" onclick="buyNow('Clamper',1300)">Buy Now</button>
                  </div>
                
                </div>
              </div>
              
              <!-- Keyboard -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
                <div class="menu-item-box" style="gap: 10px;">
                  <a href="assets/img/menu/keyboard.jpg" class="glightbox">
                    <img src="assets/img/menu/menu-item-3.jpg" class="menu-img img-fluid" alt="Keyboard" style="width: 100%; height: 200px; object-fit: cover;">
                  </a>
                  <a href="productdetails/keyboard.html">
                  <h4>Keyboard</h4>
                  <p class="price">Rs.3,200</p> </a>
                  <div class="menu-actions">
                    <button class="btn btn-cart"><i class="bi bi-cart-plus" onclick="addToCart('Keyboard',3200)"></i> Add to Cart</button>
                    <button class="btn btn-buy" onclick="buyNow('Keyboard',3200)">Buy Now</button>
                  </div>
                
                </div>
              </div>
              <!-- Normal Server -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png" class="glightbox">
                <img src="assets/img/menu/prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png" class="menu-img img-fluid" alt="Normal Server" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/normalserver.html">
              <h4>Normal Server</h4>
              <p class="price">Rs.90,500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Normal Server',90500)"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Normal Server',90500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Network Cable -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/DHU7060_DH-PFM920I-5EUN_product-image_1.png" class="glightbox">
                <img src="assets/img/menu/DHU7060_DH-PFM920I-5EUN_product-image_1.png" class="menu-img img-fluid" alt="Network Cable" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/cat6cable.html">
              <h4>Network Cable</h4>
              <p class="price">Rs.19,500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Network Cable',19500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Network Cable',19500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Solid State Drive 128GB -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/S9a9dc286f50c40e1b40caf84ce27dacf9.webp" class="glightbox">
                <img src="assets/img/menu/DAHUA-SATA-256GB-3 (1).png" class="menu-img img-fluid" alt="Solid State Drive 128GB" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/ssd.html">
              <h4>Solid State Drive 128GB</h4>
              <p class="price">Rs.2,200</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('SSD 128GB',2200)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('SSD 128GB',2200)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Monitor -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/Dell-D1918H.jpg" class="glightbox">
                <img src="assets/img/menu/Dell-D1918H.jpg" class="menu-img img-fluid" alt="Monitor" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/monitor.html">
              <h4>Monitor</h4>
              <p class="price">Rs.15,500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Monitor',15500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Monitor',15500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Branded Keyboard & Mouse -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/kb216-ms116-kbm-01-bk-1.png" class="glightbox">
                <img src="assets/img/menu/kb216-ms116-kbm-01-bk-1.png" class="menu-img img-fluid" alt="Branded Keyboard & Mouse" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/brandedkeymouse.html">
              <h4>Branded Keyboard & Mouse</h4>
              <p class="price">Rs.3,200</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Dell Wired Keyboard & Mouse Combo',3200)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Dell Wired Keyboard & Mouse Combo',3200)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Power Supply -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/main-qimg-e94536808be0e0e5a619d37a6cfbeb7b-lq.jpeg" class="glightbox">
                <img src="assets/img/menu/FSP350-60EPN80-lg__34378.jpg" class="menu-img img-fluid" alt="Power Supply" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/powersupply.html">
              <h4>Power Supply</h4>
              <p class="price">Rs.1,500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Power Supply',1500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Power Supply',1500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- External Harddisk 1TB -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/WD-Specification-Element.jpg" class="glightbox">
                <img src="assets/img/menu/5e47a7e207605426186502e15be08e22.jpg" class="menu-img img-fluid" alt="External Harddisk 1TB" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/externalhdd.html">
              <h4>External Harddisk 1TB</h4>
              <p class="price">Rs.5,700</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('External Harddisk 1TB',5700)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('External Harddisk 1TB',5700)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Dell Vostro 3430 -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/Screenshot 2024-07-31 173629.png" class="glightbox">
                <img src="assets/img/menu/3430_.jpg" class="menu-img img-fluid" alt="Dell Vostro 3430" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/dellinspirion.html">
              <h4>Dell Inspirion 3430</h4>
              <p class="price">Rs.90,000</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Dell Inspirion 3430',90000)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Dell Inspirion 3430',90000)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- NVME SSD 128GB -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/Screenshot 2024-07-31 173756.png" class="glightbox">
                <img src="assets/img/menu/HP_1TB_SSD.jpg" class="menu-img img-fluid" alt="NVME SSD 128GB" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/nvme.html">
              <h4>NVME SSD 128GB</h4>
              <p class="price">Rs.3,000</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('NVME SSD 128GB',3000)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('NVME SSD 128GB',3000)">Buy Now</button>
              </div>
              
            </div>
          </div>
          <!-- Headphone -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/cdc9f6847ff74c048ccf21bb49c4594d.jpg" class="glightbox">
                <img src="assets/img/menu/84cf6d5739a034f0b28023fb91453a2e.jpg" class="menu-img img-fluid" alt="Headphone" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/heaphone.html">
              <h4>Headphone</h4>
              <p class="price">Rs.1,500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Headphone',1500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Headphone',1500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Caddy -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/main-qimg-26a89bdf4057c2c0465906e709344d54.webp" class="glightbox">
                <img src="assets/img/menu/hdd_caddy_1.jpg" class="menu-img img-fluid" alt="Caddy" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/caddy.html">
              <h4>Caddy</h4>
              <p class="price">Rs.500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Caddy',500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Caddy',500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Wifi Dongle -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/161839-l4iasj.jpg" class="glightbox">
                <img src="assets/img/menu/4050158915.jpg" class="menu-img img-fluid" alt="Wifi Dongle" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/wifidongle.html">
              <h4>Wifi Dongle</h4>
              <p class="price">Rs.500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Wifi Dongle',500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Wifi Dongle',500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          
          <!-- Ethernet Adapter -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/EU-4306_photo_05_USB-A_RJ45_Gigabit_Network_Adapter_Gigabit_USB3-2_1000x1000.jpg" class="glightbox">
                <img src="assets/img/menu/71-E1Mu48WL._AC_SL1500_.jpg" class="menu-img img-fluid" alt="Ethernet Adapter" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/ethernetconverter.html">
              <h4>Ethernet Adapter</h4>
              <p class="price">Rs.800</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('Ethernet Adapter',800)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('Ethernet Adapter',800)">Buy Now</button>
              </div>
            </div>
          </div>
          
          <!-- CPU Fan -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/61HMyK99U3L.jpg" class="glightbox">
                <img src="assets/img/menu/main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg" class="menu-img img-fluid" alt="CPU Fan" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/CPUfan.html">
              <h4>CPU Fan</h4>
              <p class="price">Rs.500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('CPU Fan',500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('CPU Fan',500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          <!-- HDMI to VGA Converter -->
          <div class="menu-item" style="flex: 0 0 calc(33.33% - 13.33px); box-sizing: border-box;">
            <div class="menu-item-box" style="gap: 10px;">
              <a href="assets/img/menu/dmi to vga.jpeg" class="glightbox">
                <img src="assets/img/menu/hdmi.jpg" class="menu-img img-fluid" alt="HDMI to VGA Converter" style="width: 100%; height: 200px; object-fit: cover;">
              </a>
              <a href="productdetails/hdmitovga.html">
              <h4>HDMI to VGA Converter</h4>
              <p class="price">Rs.500</p> </a>
              <div class="menu-actions">
                <button class="btn btn-cart" onclick="addToCart('HDMI to VGA Converter',500)"><i class="bi bi-cart-plus" ></i> Add to Cart</button>
                <button class="btn btn-buy" onclick="buyNow('HDMI to VGA Converter',500)">Buy Now</button>
              </div>
            
            </div>
          </div>
          </div>
          </div>

          <div class="tab-pane fade" id="menu-breakfast">
            <div class="tab-header text-center">
              <h3>Devices for Link PC</h3>
            </div>
          
            <!-- Use flex layout with spacing -->
            <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-start;">
          
              <!-- Link PC -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 20px); box-sizing: border-box; padding: 10px; margin-bottom: 20px;">
                <a href="assets/img/menu/linkpc.jpg" class="glightbox">
                  <img src="assets/img/menu/menu-item-1.jpg" class="menu-img img-fluid" alt="Link PC" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <a href="productdetails/linkpc.html">
                  <h4>Link PC</h4>
                  <p class="price">
                    <strike>Rs.14,500</strike><br>
                    Rs.10,500
                  </p>
                </a>
                <div class="menu-actions">
                  <button class="btn btn-cart" onclick="addToCart('Link PC', 10500)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn btn-buy" onclick="buyNow('Link PC', 10500)">Buy Now</button>
                </div>
              </div>
          
              <!-- Link PC + -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 20px); box-sizing: border-box; padding: 10px; margin-bottom: 20px;">
                <a href="assets/img/menu/Spec.jpg" class="glightbox">
                  <img src="assets/img/menu/menu-item-2.jpg" class="menu-img img-fluid" alt="Link PC +" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <a href="productdetails/linkpc+.html">
                  <h4>Link PC +</h4>
                  <p class="price">
                    <strike>Rs.15,500</strike><br>
                    Rs.13,500
                  </p>
                </a>
                <div class="menu-actions">
                  <button class="btn btn-cart" onclick="addToCart('Link PC+', 13500)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn btn-buy" onclick="buyNow('Link PC+', 13500)">Buy Now</button>
                </div>
              </div>
          
              <!-- Normal Server -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 20px); box-sizing: border-box; padding: 10px; margin-bottom: 20px;">
                <a href="assets/img/menu/prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png" class="glightbox">
                  <img src="assets/img/menu/prod-445355-desktop-optiplex-7010-sff-inspiron-3020-no-odd-800x620.png" class="menu-img img-fluid" alt="Normal Server" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <a href="productdetails/normalserver.html">
                  <h4>Normal Server</h4>
                  <p class="price">Rs.90,500</p>
                </a>
                <div class="menu-actions">
                  <button class="btn btn-cart" onclick="addToCart('Normal Server', 90500)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn btn-buy" onclick="buyNow('Normal Server', 90500)">Buy Now</button>
                </div>
              </div>
          
              <!-- Network Cable -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 20px); box-sizing: border-box; padding: 10px; margin-bottom: 20px;">
                <a href="assets/img/menu/DHU7060_DH-PFM920I-5EUN_product-image_1.png" class="glightbox">
                  <img src="assets/img/menu/DHU7060_DH-PFM920I-5EUN_product-image_1.png" class="menu-img img-fluid" alt="Network Cable" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <a href="productdetails/cat6cable.html">
                  <h4>Network Cable</h4>
                  <p class="price">Rs.19,500</p>
                </a>
                <div class="menu-actions">
                  <button class="btn btn-cart" onclick="addToCart('Network Cable', 19500)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn btn-buy" onclick="buyNow('Network Cable', 19500)">Buy Now</button>
                </div>
              </div>
          
              <!-- Monitor -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 20px); box-sizing: border-box; padding: 10px; margin-bottom: 20px;">
                <a href="assets/img/menu/Dell-D1918H.jpg" class="glightbox">
                  <img src="assets/img/menu/Dell-D1918H.jpg" class="menu-img img-fluid" alt="Monitor" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <a href="productdetails/monitor.html">
                  <h4>Monitor</h4>
                  <p class="price">Rs.15,500</p>
                </a>
                <div class="menu-actions">
                  <button class="btn btn-cart" onclick="addToCart('Monitor', 15500)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn btn-buy" onclick="buyNow('Monitor', 15500)">Buy Now</button>
                </div>
              </div>
          
              <!-- Branded Keyboard & Mouse -->
              <div class="menu-item" style="flex: 0 0 calc(33.33% - 20px); box-sizing: border-box; padding: 10px; margin-bottom: 20px;">
                <a href="assets/img/menu/kb216-ms116-kbm-01-bk-1.png" class="glightbox">
                  <img src="assets/img/menu/kb216-ms116-kbm-01-bk-1.png" class="menu-img img-fluid" alt="Branded Keyboard & Mouse" style="width: 100%; height: 200px; object-fit: cover;">
                </a>
                <a href="productdetails/brandedkeymouse.html">
                  <h4>Branded Keyboard & Mouse</h4>
                  <p class="price">Rs.3,200</p>
                </a>
                <div class="menu-actions">
                  <button class="btn btn-cart" onclick="addToCart('Dell Wired Keyboard & Mouse Combo', 3200)">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                  </button>
                  <button class="btn btn-buy" onclick="buyNow('Dell Wired Keyboard & Mouse Combo', 3200)">Buy Now</button>
                </div>
              </div>
          
            </div>
          </div>
          

<div class="tab-pane fade" id="menu-lunch">
  <div class="tab-header text-center">
    <h3>Accessories</h3>
  </div>

  <div class="row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;"> <!-- Smaller gap of 10px -->
    
    <!-- External Harddisk 1TB -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/WD-Specification-Element.jpg" class="glightbox">
        <img src="assets/img/menu/5e47a7e207605426186502e15be08e22.jpg" class="menu-img img-fluid" alt="External Harddisk 1TB" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/externalhdd.html">
        <h4>External Harddisk 1TB</h4>
        <p class="price">Rs.5,700</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('External Harddisk 1TB', 5700)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('External Harddisk 1TB', 5700)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Solid State Drive 128GB -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/S9a9dc286f50c40e1b40caf84ce27dacf9.webp" class="glightbox">
        <img src="assets/img/menu/DAHUA-SATA-256GB-3 (1).png" class="menu-img img-fluid" alt="Solid State Drive 128GB" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/ssd.html">
        <h4>Solid State Drive 128GB</h4>
        <p class="price">Rs.2,200</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('SSD 128GB', 2200)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('SSD 128GB', 2200)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Power Supply -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/main-qimg-e94536808be0e0e5a619d37a6cfbeb7b-lq.jpeg" class="glightbox">
        <img src="assets/img/menu/FSP350-60EPN80-lg__34378.jpg" class="menu-img img-fluid" alt="Power Supply" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/powersupply.html">
        <h4>Power Supply</h4>
        <p class="price">Rs.1,500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Power Supply', 1500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Power Supply', 1500)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- CPU Fan -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/61HMyK99U3L.jpg" class="glightbox">
        <img src="assets/img/menu/main-qimg-a372bdcb21705db51641bf33a8c4dc72-lq.jpeg" class="menu-img img-fluid" alt="CPU Fan" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/CPUfan.html">
        <h4>CPU Fan</h4>
        <p class="price">Rs.500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('CPU Fan', 500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('CPU Fan', 500)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Wifi Dongle -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/161839-l4iasj.jpg" class="glightbox">
        <img src="assets/img/menu/4050158915.jpg" class="menu-img img-fluid" alt="Wifi Dongle" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/wifidongle.html">
        <h4>Wifi Dongle</h4>
        <p class="price">Rs.500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Wifi Dongle', 500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Wifi Dongle', 500)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Headphone -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/cdc9f6847ff74c048ccf21bb49c4594d.jpg" class="glightbox">
        <img src="assets/img/menu/84cf6d5739a034f0b28023fb91453a2e.jpg" class="menu-img img-fluid" alt="Headphone" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/heaphone.html">
        <h4>Headphone</h4>
        <p class="price">Rs.1,500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Headphone', 1500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Headphone', 1500)">
          Buy Now
        </button>
      </div>
    </div>

  </div>
</div>


<div class="tab-pane fade" id="menu-dinner">
  <div class="tab-header text-center">
    <h3>Laptop & Accessories</h3>
  </div>

  <div class="row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">

    <!-- Dell Vostro 3430 -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/Screenshot 2024-07-31 173629.png" class="glightbox">
        <img src="assets/img/menu/3430_.jpg" alt="Dell Vostro 3430" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/dellinspirion.html">
        <h4>Dell Inspirion 3430</h4>
        <p class="price">Rs.91,000</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Dell Inspirion 3430', 91000)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Dell Inspirion 3430', 91000)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- NVME SSD 128GB -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/Screenshot 2024-07-31 173756.png" class="glightbox">
        <img src="assets/img/menu/HP_1TB_SSD.jpg" alt="NVME SSD 128GB" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/nvme.html">
        <h4>NVME SSD 128GB</h4>
        <p class="price">Rs.3,000</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('NVME SSD 128GB', 3000)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('NVME SSD 128GB', 3000)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Headphone -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/cdc9f6847ff74c048ccf21bb49c4594d.jpg" class="glightbox">
        <img src="assets/img/menu/84cf6d5739a034f0b28023fb91453a2e.jpg" alt="Headphone" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/heaphone.html">
        <h4>Headphone</h4>
        <p class="price">Rs.1,500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Headphone', 1500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Headphone', 1500)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Caddy -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/main-qimg-26a89bdf4057c2c0465906e709344d54.webp" class="glightbox">
        <img src="assets/img/menu/hdd_caddy_1.jpg" alt="Caddy" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/caddy.html">
        <h4>Caddy</h4>
        <p class="price">Rs.500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Caddy', 500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Caddy', 500)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Ethernet Adapter -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/EU-4306_photo_05_USB-A_RJ45_Gigabit_Network_Adapter_Gigabit_USB3-2_1000x1000.jpg" class="glightbox">
        <img src="assets/img/menu/71-E1Mu48WL._AC_SL1500_.jpg" alt="Ethernet Adapter" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/ethernetconverter.html">
        <h4>Ethernet Adapter</h4>
        <p class="price">Rs.800</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Ethernet Adapter', 800)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Ethernet Adapter', 800)">
          Buy Now
        </button>
      </div>
    </div>

    <!-- Wifi Dongle -->
    <div class="menu-item" style="padding: 10px;">
      <a href="assets/img/menu/161839-l4iasj.jpg" class="glightbox">
        <img src="assets/img/menu/4050158915.jpg" alt="Wifi Dongle" style="width: 100%; height: 200px; object-fit: cover;">
      </a>
      <a href="productdetails/wifidongle.html">
        <h4>Wifi Dongle</h4>
        <p class="price">Rs.500</p>
      </a>
      <div class="menu-actions">
        <button class="btn btn-cart" onclick="addToCart('Wifi Dongle', 500)">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
        <button class="btn btn-buy" onclick="buyNow('Wifi Dongle', 500)">
          Buy Now
        </button>
      </div>
    </div>

  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    function addToCart(name, price) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const index = cart.findIndex(item => item.name === name);
      if (index !== -1) {
        cart[index].quantity++;
      } else {
        cart.push({ name, price, quantity: 1 });
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartCount();
      showAlert(`${name} added to cart!`);
    }

    function buyNow(name, price) {
      window.location.href = `cart.html?items=${encodeURIComponent(JSON.stringify([{ name, price }]))}`;
    }

    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const countElement = document.querySelector('.header-cart-count');
      if (countElement) {
        let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        countElement.textContent = totalItems;
      }
    }

    function showAlert(message) {
      const alert = document.createElement('div');
      alert.className = 'alert alert-success position-fixed top-0 end-0 m-3';
      alert.style.zIndex = 1050;
      alert.textContent = message;
      document.body.appendChild(alert);
      setTimeout(() => alert.remove(), 2000);
    }

    // Make functions globally accessible
    window.addToCart = addToCart;
    window.buyNow = buyNow;

    updateCartCount();
  });
</script>
        </section>
        

    <!-- /Contact Section -->
  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container">
      <div class="row gy-3">
        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-geo-alt icon"></i>
          <div class="address">
            <h4>Address</h4>
            <p>Putalisadak Computer Bazzar 3<sup>rd</sup>floor</p>
            <p>Nepal,Kathmandu</p>
            <p></p>
          </div>

        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-telephone icon"></i>
          <div>
            <h4>Contact</h4>
            <p>
              <strong>Phone:</strong> <span>01-4523161,4523057</span><br>
              <strong>Email:</strong> <span>info@comtechgroup.com.np</span><br>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 d-flex">
          <i class="bi bi-clock icon"></i>
          <div>
            <h4>Opening Hours</h4>
            <p>
              <strong>Sunday-Friday:</strong> <span>10AM - 6PM</span><br>
              <strong>Saturday</strong>: <span>Closed</span>
            </p>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h4>Follow Us</h4>
          <div class="social-links d-flex">
            <a href="https://www.facebook.com/profile.php?id=61555966494936" class="facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com/comtechgcpl/" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="https://www.youtube.com/@ComtechGroup-ss7gv" class="youtube"><i class="bi bi-youtube"></i></a>
  
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright All Rights Reserved</span></p>
      <div class="credits">
        Designed by <a href="https://satkrit.com.np/">Satkrit bhandari</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html> 