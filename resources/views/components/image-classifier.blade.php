<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canvas Drawing</title>
    <style>
        .container {
            padding: 1rem;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 18rem;
            user-select: none;
        }

        canvas {
            border: 1px solid #d1d5db;
            background-color: white;
        }

        .buttons {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            color: white;
            cursor: pointer;
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
            margin-top: 1rem;
            padding: 0.5rem;
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Canvas for Drawing -->
        <canvas id="canvas" width="200" height="200"></canvas>

        <!-- Buttons -->
        <div class="buttons">
            <button class="clear-btn" onclick="clearCanvas()">Clear</button>
            <button class="submit-btn" onclick="submitCanvas()">Submit</button>
        </div>

        <!-- Result Display -->
        <div class="result" id="result">Waiting for result...</div>
    </div>

    <script>
        // Canvas and Context
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        // State
        let drawing = false;
        let prediction = '';

        // Initialize Canvas
        function initCanvas() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 4;
            ctx.lineCap = 'round';
        }

        // Get Mouse/Touch Position
        function getMousePos(event) {
            const rect = canvas.getBoundingClientRect();
            let x, y;

            if (event.touches) {
                // Touch event
                x = event.touches[0].clientX - rect.left;
                y = event.touches[0].clientY - rect.top;
            } else {
                // Mouse event
                x = event.clientX - rect.left;
                y = event.clientY - rect.top;
            }

            return {
                x,
                y
            };
        }

        // Start Drawing
        function startDrawing(event) {
            event.preventDefault();
            drawing = true;
            const {
                x,
                y
            } = getMousePos(event);
            ctx.beginPath();
            ctx.moveTo(x, y);
            console.log("Start drawing at:", x, y);
        }

        // Draw
        function draw(event) {
            if (!drawing) return;
            const {
                x,
                y
            } = getMousePos(event);
            ctx.lineTo(x, y);
            ctx.stroke();
            console.log("Drawing to:", x, y);
        }

        // Stop Drawing
        function stopDrawing(event) {
            drawing = false;
            ctx.closePath();
            console.log("Stopped drawing.");
        }

        // Clear Canvas
        function clearCanvas() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = 'black';
            ctx.lineWidth = 4;
            prediction = '';
            document.getElementById('result').textContent = 'Waiting for result...';
            console.log("Canvas cleared.");
        }

        // Submit Canvas
        async function submitCanvas() {
            // Convert the canvas to a Blob
            canvas.toBlob(async (blob) => {
                // Create a FormData object
                const formData = new FormData();
                formData.append('image', blob, 'drawing.png');

                // Send the image to Flask for classification
                try {
                    const response = await fetch('http://127.0.0.1:5000', {
                        method: 'get',
                        body: formData, // Send as FormData
                    });

                    if (response.ok) {
                        const result = await response.json();
                        prediction = result.format || 'Unknown';
                    } else {
                        prediction = 'Failed to classify image.';
                    }
                } catch (error) {
                    console.error("Error sending request:", error);
                    prediction = 'Error connecting to server.';
                }

                // Update result display
                document.getElementById('result').textContent = prediction;
            }, 'image/png');
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
