import {options} from "./HeaderOptions.js";

const fetchProducts = async (page) => {
    try {
        const response = await fetch('https://api.webshopapp.com/nl/products.json?page=' + page, options)
        return await response.json();
    } catch (error) {
        console.log(error);
    }
}

for (let i = 0;  i < 100; i++) {
    const json = await fetchProducts(i);
    const products = json["products"];
    if (products.length === 0)
        break;
    for (let p in products){
        if (products[p]["title"] === "White Coconut Truffle")
            console.log(products[p])
    }
}

