(function () {
    const canvas = document.getElementById('gameCar01');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth - 40;

    // create the car image
    const carImage = new Image();
    carImage.src = '/jb/game-car/car.png';

    // create the background pattern
    const bgImage = new Image();
    bgImage.src = '/jb/game-car/tile.png';
    const bgPattern = ctx.createPattern(bgImage, 'repeat');

    // define the variable to store the mouse position
    let mousePosition = { x: 0, y: 0 };

    function getIncreasedCirclePositions(min, max, step, radius) {
        min = min || 0;
        max = max || 3600;
        step = step || 1;
        radius = radius || canvas.height / 2 - 20;

        let iterator = min;

        return function() {
            if (iterator > max || iterator < min) {
                iterator = 0;
            }

            iterator = iterator + step;

            const radians = iterator * Math.PI / 180;
            const x = Math.cos(radians) * radius + canvas.width / 2;
            const y = Math.sin(radians) * radius + canvas.height / 2;

            return [x, y];
        };
    }

    function checkCollision(x, y, width, height) {
        const mouseX = mousePosition.x;
        const mouseY = mousePosition.y;
        return mouseX >= x && mouseX <= x + width && mouseY >= y && mouseY <= y + height;
    }

    canvas.addEventListener('mousemove', function(event) {
        mousePosition.x = event.clientX;
        mousePosition.y = event.clientY;
    });

    const nextCarPositions = getIncreasedCirclePositions();
    let circleX, circleY, isCollision;

    function gameLoop() {
        // clear the canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!isCollision) {
            // get the next X, Y positions of the circle center and the angle of rotation for the car
            [circleX, circleY] = nextCarPositions();
        }

        // draw the background pattern
        ctx.fillStyle = bgPattern;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // save the current context state
        ctx.save();

        // translate the canvas to the center of the circle
        ctx.translate(circleX, circleY);

        const dx = circleX - canvas.width / 2;
        const dy = circleY - canvas.height / 2;
        const carAngle = Math.atan2(-dy, -dx)/* - Math.PI / 2*/;
        // rotate the canvas by the carAngle
        ctx.rotate(carAngle);

        // draw the car image with its center aligned to the center of the canvas
        const carWidth = carImage.width;
        const carHeight = carImage.height;
        const carX = -carWidth / 2;
        const carY = -carHeight / 2;
        ctx.drawImage(carImage, carX, carY);

        // check for collision with the car
        isCollision = checkCollision(circleX + carX, circleY + carY, carWidth, carHeight);

        if (isCollision) {
            // stop moving and show the "Gotcha!" message
            ctx.font = '48px Montserrat,sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';

            // reset the canvas transformation before drawing the text
            ctx.setTransform(1, 0, 0, 1, 0, 0);

            const text = 'Злавіў!';
            // const text = Math.round(circleX) + ' ' + mousePosition.x + ' | ' + Math.round(circleY) + ' ' + mousePosition.y;
            ctx.fillText(text, canvas.width / 2, canvas.height / 2);
        }

        // request the next frame of the game loop
        requestAnimationFrame(gameLoop);

        // restore the previous context state
        ctx.restore();
    }
    gameLoop();
})();