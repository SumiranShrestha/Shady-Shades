<?php

include('header.php');

?>


    <!-- Hero Section with Carousel -->
    <main class="container my-4">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" 
                    aria-label="Slide 4"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4"
                    aria-label="Slide 5"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/SR/image1.webp" class="d-block w-100 hero-image" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="images/SR/image2.webp" class="d-block w-100 hero-image" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="images/SR/image3.webp" class="d-block w-100 hero-image" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="images/SR/image4.webp" class="d-block w-100 hero-image" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="images/SR/image5.webp" class="d-block w-100 hero-image" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <!-- Title / Heading -->
        <h2 class="text-center mb-5">Trending Products</h2>

        <!-- Products Grid -->
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <!-- Card 1 -->
            <div class="col">
                <div class="card h-100">
                    <img src="images/SR/pd1.webp" alt="Spike" class="card-img-top" />
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1">Spike</h5>
                        <p class="card-text">
                            <span class="price-old">रू 20,000</span>
                            <span class="price-new">रू 16,000</span>
                        </p>
                        <span class="badge save-badge">SAVE रू 4,000</span>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col">
                <div class="card h-100">
                    <img src="images/SR/pd2.webp" alt="Casablanca" class="card-img-top" />
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1">Casablanca</h5>
                        <p class="card-text">
                            <span class="price-old">रू 16,495</span>
                            <span class="price-new">रू 12,245</span>
                        </p>
                        <span class="badge save-badge">SAVE रू 4,250</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col">
                <div class="card h-100">
                    <img src="/images/SR/pd3.webp" alt="Cabaret" class="card-img-top" />
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1">Cabaret</h5>
                        <p class="card-text">
                            <span class="price-old">रू 16,495</span>
                            <span class="price-new">रू 12,245</span>
                        </p>
                        <span class="badge save-badge">SAVE रू 4,250</span>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col">
                <div class="card h-100">
                    <img src="images/SR/pd4.webp" alt="Dracula" class="card-img-top" />
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1">Dracula</h5>
                        <p class="card-text">
                            <span class="price-old">रू 16,495</span>
                            <span class="price-new">रू 12,245</span>
                        </p>
                        <span class="badge save-badge">SAVE रू 4,250</span>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- AFTER the Trending Products grid -->
    <div class="row align-items-center my-5">
        <!-- Left column: Text content -->
        <div class="col-md-6">
            <h3 class="fw-bold mb-3">MAKE SURE TO RIDE LIFE YOUR WAY</h3>
            <p>
                At Skull Rider, we offer casual wear that is fueled and inspired by the Café Racer culture,
                an iconic movement where speed, freedom, and individuality reign supreme. Our clothing
                reflects the raw, minimalist aesthetic embraced by riders who stripped down and customized
                their motorcycles to chase thrills on their own terms. Designed for those who walk their own
                path, our apparel is more than just fashion—it's a statement of rebellion, embodying a
                lifestyle that values agility, authenticity, and the spirit of adventure.
            </p>
            <a href="#" class="btn btn-success btn-lg">See Products</a>
        </div>
    
        <!-- Right column: Image -->
        <div class="col-md-6 text-center">
            <!-- Replace with your actual image URL -->
            <img src="images/SR/image2.webp" alt="Skull Rider lifestyle"
                class="img-fluid rounded" />
        </div>
    </div>

    <!-- AFTER the "make sure to ride life your way" section -->
    <section class="py-5" style="background-color: #E9F5E1;">
        <div class="container">
            <!-- Section Title -->
            <h2 class="text-center mb-5">Featured Products</h2>
            
    
            <!-- Product 1 -->
            <div class="row align-items-center mb-5">
                <!-- Image Column -->
                <div class="col-md-5 mb-3 mb-md-0">
                    <img src="images/SR/pd5.webp" alt="Skull Diamond"
                        class="img-fluid rounded" />
                </div>
    
                <!-- Details Column -->
                <div class="col-md-7">
                    <h3 class="fw-bold">Skull Diamond</h3>
                    <!-- Price & Discount -->
                    <p class="mb-1">
                        <span class="text-muted text-decoration-line-through">रू 38,000</span>
                        <span class="ms-2 fw-bold text-success">रू 32,000</span>
                        <span class="badge bg-success ms-2">14% OFF</span>
                    </p>
                    <p class="text-muted">Shipping is calculated at checkout</p>
    
                    <!-- Quantity & Add to Cart -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary" type="button">-</button>
                            <input type="number" class="form-control text-center" value="1" min="1" />
                            <button class="btn btn-outline-secondary" type="button">+</button>
                        </div>
                        <button class="btn btn-success ms-3">ADD TO CART</button>
                    </div>
    
                    <!-- Description -->
                    <p>
                        We introduce to you the <strong>Skull Diamond Limited Edition Sunglasses</strong>.
                        These sunglasses are drawn from the modern hot-rod and a true Skull Rider collectors item
                        with only <em>50 units available</em>. Black on black, this relentless design is polished
                        to perfection and embellished with a pair of lightweight Skull crystals showing
                        remarkable workmanship and craftsmanship...
                    </p>
                </div>
            </div>
    
            <!-- Product 2 -->
            <div class="row align-items-center mb-5">
                <!-- Image Column -->
                <div class="col-md-5 order-2 order-md-1 mt-3 mt-md-0">
                    <img src="images/SR/pd5.webp"
                        alt="Elijah Black (SR Black)" class="img-fluid rounded" />
                </div>

                <!-- Details Column -->
                <div class="col-md-7 order-1 order-md-2">
                    <h3 class="fw-bold">Elijah Black (SR Black)</h3>
                    <!-- Price & Discount -->
                    <p class="mb-1">
                        <span class="text-muted text-decoration-line-through">रू 34,000</span>
                        <span class="ms-2 fw-bold text-success">रू 28,000</span>
                        <span class="badge bg-success ms-2">10% OFF</span>
                    </p>
                    <p class="text-muted">Shipping is calculated at checkout</p>
    
                    <!-- Quantity & Add to Cart -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary" type="button">-</button>
                            <input type="number" class="form-control text-center" value="1" min="1" />
                            <button class="btn btn-outline-secondary" type="button">+</button>
                        </div>
                        <button class="btn btn-success ms-3">ADD TO CART</button>
                    </div>
    
                    <!-- Description -->
                    <p>
                        The Elijah Black sunglasses are inspired by pure strength and resilience,
                        built like a <strong>4x4</strong> to withstand any challenge...
                    </p>
                </div>
            </div>
    
            <!-- About Skull Rider -->
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold">ABOUT SKULL RIDER</h4>
                    <p>
                        Skull Rider Inc, founded in 2015 by MotoGP World Champion Jorge Lorenzo, is a
                        lifestyle brand that embodies rebellion and individuality. Inspired by the
                        Café Racer culture, Skull Rider offers a unique collection of apparel, sunglasses,
                        jewelry, and accessories that merge style with adrenaline...
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <img src="images/SR/image3.webp" alt="About Skull Rider"
                        class="img-fluid rounded" />
                </div>
            </div>
    
        </div> <!-- container -->
    </section>


<?php
include('footer.php')
?>