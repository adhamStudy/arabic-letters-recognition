<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter Classification</title>
    <style>
        /* Previous CSS remains the same */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: 'Arial', sans-serif;
        }

        .container {
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: #333;
        }

        canvas {
            border: 2px solid #d1d5db;
            background-color: white;
            display: block;
            margin: 0 auto;
            border-radius: 0.5rem;
            cursor: crosshair;
        }

        .buttons {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        button {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 1rem;
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .clear-btn {
            background-color: #ef4444;
        }

        .clear-btn:hover {
            background-color: #dc2626;
        }

        .submit-btn {
            background-color: #3b82f6;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }

        .result {
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            text-align: center;
            min-height: 2rem;
            font-size: 1rem;
            color: #333;
        }

        .error {
            color: #dc2626;
            background-color: #fee2e2;
            border-color: #fca5a5;
        }

        .success {
            color: #16a34a;
            background-color: #dcfce7;
            border-color: #86efac;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>ارسم حرفاً</h1>
        <canvas id="canvas" width="300" height="300"></canvas>
        <div class="buttons">
            <button class="clear-btn" onclick="clearCanvas()">مسح الحرف</button>
            <button class="submit-btn" onclick="submitCanvas()">موافق</button>
        </div>
        <div class="result" id="result">ارسم حرفاُ ثم اضغط موافق لرؤية النتيجة</div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        function initCanvas() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 15;
            ctx.lineCap = 'round';
        }

        function getMousePos(event) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            let x, y;

            if (event.touches) {
                x = (event.touches[0].clientX - rect.left) * scaleX;
                y = (event.touches[0].clientY - rect.top) * scaleY;
            } else {
                x = (event.clientX - rect.left) * scaleX;
                y = (event.clientY - rect.top) * scaleY;
            }

            return {
                x,
                y
            };
        }

        function startDrawing(event) {
            event.preventDefault();
            drawing = true;
            const {
                x,
                y
            } = getMousePos(event);
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function draw(event) {
            event.preventDefault();
            if (!drawing) return;
            const {
                x,
                y
            } = getMousePos(event);
            ctx.lineTo(x, y);
            ctx.stroke();
        }

        function stopDrawing() {
            drawing = false;
            ctx.closePath();
        }

        function clearCanvas() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 15;
            document.getElementById('result').textContent = 'ارسم حرفاُ ثم اضغط موافق لرؤية النتيجة';
            document.getElementById('result').className = 'result';
        }

        async function submitCanvas() {
            const submitBtn = document.querySelector('.submit-btn');
            const resultDiv = document.getElementById('result');

            try {
                // Show loading state
                submitBtn.disabled = true;
                resultDiv.textContent = 'جاري المعالجة...';
                resultDiv.className = 'result';

                // Convert canvas to blob
                const blob = await new Promise((resolve, reject) => {
                    canvas.toBlob((blob) => {
                        if (blob) {
                            resolve(blob);
                        } else {
                            reject(new Error('Canvas to Blob conversion failed'));
                        }
                    }, 'image/jpg');
                });

                // Create form data
                const formData = new FormData();
                formData.append('image', blob, 'drawing.jpg');

                // Send request to Flask API
                const response = await fetch('http://127.0.0.1:5000/predictImage', {
                    method: 'POST',
                    body: formData,
                    mode: 'cors', // Add this
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    resultDiv.textContent = `prediction: ${data.prediction}`;
                    resultDiv.className = 'result success';
                } else {
                    throw new Error(data.error || 'حدث خطأ غير معروف');
                }

            } catch (error) {
                console.error('Error:', error);
                resultDiv.textContent = `خطأ: ${error.message}`;
                resultDiv.className = 'result error';
            } finally {
                submitBtn.disabled = false;
            }
        }

        // Event Listeners
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        // Initialize
        initCanvas();
    </script>
</body>

</html>
