function createLabel(width, height, items, image, imageX,imageY, imageWidth, imageHeight) {
    let canvas = createCanvas(width, height);
    let ctx = createCtxAndLabel(canvas)

    for (let i in items) {
        if (i === 'image')
            continue;
        let item = items[i];
        let alignX = determineX(canvas, item.align, item.x);

        createText(canvas, item.text, item.fontSize, item.fontType, alignX, item.fontColor, item.y, item.fontExtra);
    }

    addImage(canvas, image, imageX, imageY, imageWidth, imageHeight)

    return returnLabel(canvas)
}

function createCtxAndLabel(canvas) {
    // Get the canvas context
    let ctx = canvas.getContext('2d');
    // Set background color
    ctx.fillStyle = '#FFFFFF'; // white
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    return ctx;
}

function addImage(canvas, image, imageX, imageY, imageWidth, imageHeight) {
    let ctx = canvas.getContext('2d');
    if (image != null) {
        ctx.drawImage(base_image, imageX, imageY, imageWidth, imageHeight);
    }
}

function returnLabel(canvas) {
    const labelImgData = canvas.toDataURL();

    const labelImg = document.createElement('img');
    labelImg.src = labelImgData;

    return labelImg;
}


function createCanvas(width, height) {
    let canvas = document.createElement('canvas');
    let ctx = canvas.getContext('2d');
    ctx.fillStyle = "white"; // Set background color to white
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    canvas.width = width * 8;
    canvas.height = height * 8;
    return canvas;
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

function createText(canvas, text, fontSize, fontType, x, color, y, fontExtra) {
    let ctx = canvas.getContext('2d');

    ctx.fillStyle = color; // Set the text color to black
    ctx.font = fontExtra + ` ${fontSize}px ${fontType}`;



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