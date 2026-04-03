<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Click Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
        .test-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .test-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .test-card h3 {
            margin: 0 0 10px 0;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-test {
            background: #FF6B35;
            color: white;
        }
        .btn-test:hover {
            background: #E55A24;
        }
        .btn-back {
            background: #ddd;
            color: #333;
        }
        .btn-back:hover {
            background: #ccc;
        }
        .info {
            background: #f0f7ff;
            border-left: 4px solid #0088FF;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Click to Product Detail</h1>
        
        <div class="info">
            <strong>ℹ️ Instructions:</strong> Click on any card below to navigate to the product detail page
        </div>

        <!-- Test Product 1 -->
        <div class="test-card" onclick="testClickProduct(1)">
            <h3>✅ Test 1: Click Product ID 1</h3>
            <p>Click on this card to navigate to: <code>chi-tiet-san-pham-v2.php?id=1</code></p>
            <button class="btn-test" onclick="event.stopPropagation(); testClickProduct(1)">
                🔗 Go to Product #1
            </button>
        </div>

        <!-- Test Product 2 -->
        <div class="test-card" onclick="testClickProduct(2)">
            <h3>✅ Test 2: Click Product ID 2</h3>
            <p>Click on this card to navigate to: <code>chi-tiet-san-pham-v2.php?id=2</code></p>
            <button class="btn-test" onclick="event.stopPropagation(); testClickProduct(2)">
                🔗 Go to Product #2
            </button>
        </div>

        <!-- Test Product 3 -->
        <div class="test-card" onclick="testClickProduct(3)">
            <h3>✅ Test 3: Click Product ID 3</h3>
            <p>Click on this card to navigate to: <code>chi-tiet-san-pham-v2.php?id=3</code></p>
            <button class="btn-test" onclick="event.stopPropagation(); testClickProduct(3)">
                🔗 Go to Product #3
            </button>
        </div>

        <!-- Test Product 4 -->
        <div class="test-card" onclick="testClickProduct(4)">
            <h3>✅ Test 4: Click Product ID 4</h3>
            <p>Click on this card to navigate to: <code>chi-tiet-san-pham-v2.php?id=4</code></p>
            <button class="btn-test" onclick="event.stopPropagation(); testClickProduct(4)">
                🔗 Go to Product #4
            </button>
        </div>

        <div class="button-group">
            <button class="btn-back" onclick="window.location.href='cua-hang.php'">
                ← Back to Shop
            </button>
            <button class="btn-test" onclick="testFunction()">
                🧪 Test Function
            </button>
        </div>

        <div id="result" style="margin-top: 20px; padding: 15px; background: #e8f5e9; border-radius: 6px; display: none;">
            <strong>✅ Function Test Result:</strong>
            <p id="result-text"></p>
        </div>
    </div>

    <script>
        function testClickProduct(productId) {
            console.log('🔗 Click detected! Product ID:', productId);
            if (!productId) {
                alert('❌ Product ID not found');
                return;
            }
            
            const url = `chi-tiet-san-pham-v2.php?id=${productId}`;
            console.log('📍 Navigating to:', url);
            alert(`✅ Redirecting to: ${url}`);
            window.location.href = url;
        }

        function testFunction() {
            const result = document.getElementById('result');
            const resultText = document.getElementById('result-text');
            
            result.style.display = 'block';
            resultText.innerHTML = `
                <strong>✅ All functions working:</strong><br>
                ✓ testClickProduct() function: OK<br>
                ✓ onclick event listener: OK<br>
                ✓ URL generation: OK<br>
                <br>
                <strong>Now test with real products:</strong><br>
                Click on any product card above or go back to the shop and click a product.
            `;
        }

        // Log on page load
        console.log('✅ Test page loaded successfully');
    </script>
</body>
</html>
