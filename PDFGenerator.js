import { jsPDF } from "jspdf";
import {font} from "/lib/Courgette-Regular-normal.js";

import fs from 'fs';

export function makeDocument (orderId, customer, title, description, variant) {
    const doc = new jsPDF('l', 'mm', [54, 25]);

    doc.addFileToVFS("Courgette-Regular-normal.ttf", font);
    doc.addFont("Courgette-Regular-normal.ttf", "Courgette", "normal");
    doc.setFont("Courgette");

    const splitDescription = splitText(description);
    doc.setFontSize(12);
    doc.text(title, 26, 7, "center");
    doc.setFontSize(8);
    doc.text(splitDescription[0], 26, 11, "center");
    doc.text(splitDescription[1], 26, 14, "center");
    doc.text(splitDescription[2], 26, 17, "center");
    doc.text(variant, 26, 20, "center");

    const folderName = orderId + " " + customer;//"./" + orderId +  " " + customer;

    try {
        fs.mkdirSync(folderName);
    } catch (err) {
    }

    doc.save(orderId +  " " + customer + "/" + title + ".pdf"); // will save the file in the current working directory
    return doc;
}


function splitText(text) {
    let parts = text.split(" ");
    let len = parts.length;
    let part1 = parts.slice(0, len / 3).join(" ");
    let part2 = parts.slice(len / 3, (len / 3) * 2).join(" ");
    let part3 = parts.slice((len / 3) * 2).join(" ");
    return [part1, part2, part3];
}