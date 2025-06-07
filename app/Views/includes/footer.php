<?php
// footer.php inside /views
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- olive -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare - Veterinary Medical Clinic</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Your Custom Footer CSS (if needed) -->
    <link rel="stylesheet" href="../../assets/css/footer.css">

</head>
<body>

<!-- Footer -->
<footer class="bg-[#8ec6db] mt-auto relative">
    <div class="max-w-6xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 border-b border-black">
        
        <!-- Quick Links -->
        <div class="border-r border-black pr-6 mb-8 md:mb-0">
    <h3 class="font-bold mb-4">Quick Links</h3>
    <div class="space-y-3">
        <a href="/" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
            <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                <i class="fas fa-home text-black text-xs"></i>
            </div>
            <span class="font-medium">Home</span>
        </a>
        <a href="/about" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
            <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                <i class="fas fa-info-circle text-black text-xs"></i>
            </div>
            <span class="font-medium">About</span>
        </a>
        <a href="/services" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
            <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                <i class="fas fa-stethoscope text-black text-xs"></i>
            </div>
            <span class="font-medium">Services</span>
        </a>
        <a href="/products" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
            <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                <i class="fas fa-shopping-cart text-black text-xs"></i>
            </div>
            <span class="font-medium">Products</span>
        </a>
        <a href="/reviews" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
            <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                <i class="fas fa-star text-black text-xs"></i>
            </div>
            <span class="font-medium">Reviews</span>
        </a>
    </div>
</div>

        <!-- Center Logo and Tagline -->
        <div class="flex flex-col items-center justify-center border-r border-black px-6 mb-8 md:mb-0">
            <div class="bg-white rounded-full w-14 h-14 flex items-center justify-center mb-2">
                <i class="fas fa-paw text-black text-xl"></i>
            </div>
            <span class="font-greatvibes text-xl select-none mb-3">MaVetCare</span>
            <p class="text-center text-sm max-w-[180px]">Leave your pets in safe hands.</p>
        </div>

        <!-- Contact Section -->
        <div class="pl-6 relative">
            <h3 class="font-bold mb-4">Get in Touch!</h3>

            <div class="flex items-center space-x-2 mb-4 max-w-[250px]">
                <input class="rounded-full py-1 px-4 text-xs w-full outline-none" value="deliamontanez92@gmail.com" type="email" readonly/>
                <div class="bg-[#c9b176] rounded-full w-8 h-8 flex items-center justify-center cursor-pointer">
                    <i class="fas fa-paw text-black text-sm"></i>
                </div>
            </div>

            <div class="mt-4">
                <h4 class="font-semibold text-sm mb-2">Contact Numbers:</h4>
                <div class="space-y-2">
                    <a href="tel:2332039" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
                        <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                            <i class="fas fa-phone text-black text-xs"></i>
                        </div>
                        <span class="font-medium">233 2039</span>
                    </a>
                    <a href="tel:+639162958059" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
                        <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                            <i class="fas fa-mobile-alt text-black text-xs"></i>
                        </div>
                        <span class="font-medium">+63 916 295 8059</span>
                    </a>
                    <a href="tel:+639567346746" class="flex items-center gap-2 text-sm hover:text-blue-700 transition group">
                        <div class="bg-white rounded-full w-7 h-7 flex items-center justify-center group-hover:bg-[#c9b176] transition">
                            <i class="fas fa-mobile-alt text-black text-xs"></i>
                        </div>
                        <span class="font-medium">+63 956 734 6746</span>
                    </a>
                </div>
            </div>

            </div>

    </div>

    <!-- Bottom Text -->
    <div class="text-center text-xs py-3">
        All Rights Reserved to <strong>MaVetCare 2025</strong>
    </div>

    <!-- Footer dogs image -->
    <img alt="footer dogs" class="absolute bottom-0 right-0 max-w-[150px] md:max-w-[350px]" height="150" src="/assets/images/footer_lower_right.png" style="transform: translateY(-0%)" width="330"/>
    
    <!-- Footer cat image -->
    <img alt="footer cat" class="absolute bottom-0 left-0 max-w-[200px] md:max-w-[200px]" height="100" src="/assets/images/footer_lower_left.png" style="transform: translateY(-0%)" width="180"/>
</footer>


</body>
</html>
