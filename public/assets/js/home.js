(function homePage() {
    let canvas = document.getElementById("jPromo");
    let ctx = canvas.getContext("2d");
    let circles = [];

    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    function drawCircle(x, y, radius, color) {
        ctx.beginPath();
        ctx.arc(x, y, radius, 0, 2 * Math.PI, false);
        ctx.fillStyle = color;
        ctx.fill();
    }

    function drawText(text) {
        ctx.fillStyle = "#ce1126";
        ctx.font = "90px Tahoma, serif";
        ctx.textAlign = "center";
        ctx.fillText(text, canvas.width/2, canvas.height/2);
    }

    function animate() {
        requestAnimationFrame(animate);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let i = 0; i < circles.length; i++) {
            let circle = circles[i];
            circle.x += circle.vx;
            circle.y += circle.vy;
            if (circle.x < -circle.radius || circle.x > canvas.width + circle.radius ||
                circle.y < -circle.radius || circle.y > canvas.height + circle.radius) {
                circle.x = Math.random() * canvas.width;
                circle.y = Math.random() * canvas.height;
                circle.vx = Math.random() - 0.5;
                circle.vy = Math.random() - 0.5;
            }
            drawCircle(circle.x, circle.y, circle.radius, circle.color);
        }
        drawText('Пахне Чабор!');
    }

    function init() {
        resize();
        for (let i = 0; i < 30; i++) {
            let radius = Math.random() * 100 + 10;
            /*let color = 'rgb(' + Math.floor(Math.random() * 256) + ',' +
                Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ')';*/
            let gray = Math.floor(Math.random() * 168) + 64;
            let color = 'rgb(' + gray + ',' + gray + ',' + gray + ')';
            let circle = {
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: Math.random() - 0.5,
                vy: Math.random() - 0.5,
                radius: radius,
                color: color
            };
            circles.push(circle);
        }
        animate();
    }

    window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            window.addEventListener("resize", resize);
            init();
        }, 255);
    });
})();
