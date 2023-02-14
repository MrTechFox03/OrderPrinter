function createLabel(
    height,
    width,

    title,
    titleAlign,
    titleX,
    titleY,

    description,
    descriptionAlign,
    descriptionFontSize,
    descriptionX,
    descriptionY,

    variant,
    variantAlign,
    variantFontSize,
    variantX,
    variantY
){
 let titleNode = document.createTextNode(title);
 document.body.appendChild(titleNode);

 let descriptionNode = document.createTextNode(description);
 document.body.appendChild(descriptionNode);

 let variantNode = document.createTextNode(variant);
 document.body.appendChild(variantNode);

}