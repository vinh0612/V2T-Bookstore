<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'V2T Bookstore')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col bg-[var(--color-v2t-bg)] text-[var(--color-v2t-text)]">
    
    @include('partials.header')

    <main class="flex-grow container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline-block font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    // Hàm 1: Thêm nhanh ở trang chủ / cửa hàng
    function addToCart(event, bookId) {
        event.preventDefault(); 
        
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        button.innerHTML = '⏳ Đang thêm...';
        button.disabled = true;

        fetch(`/cart/add/${bookId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json()) // ĐÃ FIX: Yêu cầu dịch ra JSON
        .then(data => {
            if (data.success) {
                // ĐÃ FIX: Cập nhật con số thật từ Server
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.innerText = data.cart_count;
                    // Hiệu ứng giật nhẹ cho đẹp mắt
                    cartBadge.classList.add('animate-bounce');
                    setTimeout(() => cartBadge.classList.remove('animate-bounce'), 1000);
                }
                
                Toastify({
                    text: "✅ Đã thêm sách vào giỏ hàng!",
                    duration: 3000,
                    gravity: "top", 
                    position: "right", 
                    style: { background: "linear-gradient(to right, #00b09b, #96c93d)" }
                }).showToast();
            } else {
                Toastify({
                    text: "❌ Lỗi: " + (data.message || "Không thể thêm sách."),
                    style: { background: "linear-gradient(to right, #ff5f6d, #ffc371)" }
                }).showToast();
            }
        })
        .catch(error => console.error('Lỗi:', error))
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }

    // Hàm 2: Thêm kèm số lượng ở trang chi tiết
    function addDetailToCart(event, bookId) {
        event.preventDefault(); 
        
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        
        const qtyInput = document.getElementById('book-qty');
        const quantity = qtyInput ? qtyInput.value : 1;

        button.innerHTML = '⏳ Đang thêm...';
        button.disabled = true;

        fetch(`/cart/add/${bookId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json()) // ĐÃ FIX: Yêu cầu dịch ra JSON
        .then(data => {
            if (data.success) {
                // ĐÃ FIX: Cập nhật con số thật từ Server
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.innerText = data.cart_count;
                    cartBadge.classList.add('animate-bounce');
                    setTimeout(() => cartBadge.classList.remove('animate-bounce'), 1000);
                }

                Toastify({
                    text: `✅ Đã thêm ${quantity} cuốn vào giỏ!`,
                    duration: 3000,
                    gravity: "top", 
                    position: "right",
                    style: { background: "linear-gradient(to right, #00b09b, #96c93d)" }
                }).showToast();
            } else {
                Toastify({
                    text: "❌ Lỗi: " + (data.message || "Kho không đủ."),
                    style: { background: "linear-gradient(to right, #ff5f6d, #ffc371)" }
                }).showToast();
            }
        })
        .catch(error => console.error('Lỗi:', error))
        .finally(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
</script>
    
</body>
</html>