function createLabel(height, width, items) {
    let canvas = createCanvas(width, height);
    let ctx = createCtxAndLabel(canvas);

    for (let i in items) {
        let item = items[i];
        let alignX = determineX(canvas, item.align, item.x);

        createText(canvas, item.text, item.fontSize, item.fontType, alignX, item.fontColor, item.y);
    }
    // Get the label image data as a base64 string
    const labelImgData = canvas.toDataURL();


    // Create an image element with the label image
    const labelImg = document.createElement('img');
    labelImg.src = labelImgData;
    //labelImg.alt = `${title} - ${variant}`;

    // Add the label image to the DOM
    document.body.appendChild(labelImg);
}


function createCanvas(width, height) {
// Create a canvas element
    let canvas = document.createElement('canvas');
    // Set the canvas dimensions to the height and width variables
    canvas.width = width * 8;
    canvas.height = height * 8;
    return canvas;
}

function createCtxAndLabel(canvas) {
    // Get the canvas context
    let ctx = canvas.getContext('2d');
    // Set background color
    ctx.fillStyle = '#FFFFFF'; // white
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    return ctx;
}

function determineX(canvas, align, offset) {
    let ctx = canvas.getContext('2d');
    if (align === 'C') {
        ctx.textAlign = "center";
        return canvas.width / 2 + offset;
    } else if (align === 'L') {
        ctx.textAlign = "left";
        return 0 + offset;
    } else if (align === 'R') {
        ctx.textAlign = "right";
        return canvas.width + offset;
    } else {
        console.log("Something went wrong with alignment");
        console.log("Align was " + align);
        console.log("Left offset is " + offset)
    }
}

function createText(canvas, text, fontSize, fontType, x, color, y) {
    let ctx = canvas.getContext('2d');

    ctx.fillStyle = color; // Set the text color to black
    ctx.font = `${fontSize}px ${fontType}`;

    const maxWidth = canvas.width - 20; // subtracting 20 to add padding
    let textWidth = ctx.measureText(text).width;

    // If the text width is greater than the canvas width, break into multiple lines
    if (textWidth > maxWidth) {
        let words = text.split(' ');
        let line = '';
        let lines = [];

        for (let i = 0; i < words.length; i++) {
            let testLine = line + words[i] + ' ';
            let testWidth = ctx.measureText(testLine).width;

            if (testWidth > maxWidth) {
                lines.push(line);
                line = words[i] + ' ';
            } else {
                line = testLine;
            }
        }
        lines.push(line); // push the last line
        for (let i = 0; i < lines.length; i++)
            ctx.fillText(lines[i], x, y + (i * (fontSize + 4)));
    } else {
        ctx.fillText(text, x, y);
    }
}