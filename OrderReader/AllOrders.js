const apiKey = "79fa822521904854874faa60d523540e";
const apiSecret = "f032e15faf8e85d4b848e652017630ed";

const options = {
    method: 'GET',
    headers: new Headers({
        'Content-Type': 'application/json',
        'Authorization': 'Basic ' + btoa(`${apiKey}:${apiSecret}`)
    })
};

let page = 1;

const orders = async () => {
    try {
        const response = await fetch('https://api.webshopapp.com/nl/orders.json?page=' + page, options)
        return await response.json();
    } catch (error) {
        console.log(error);
    }
}

const fetchOrder = async (id) => {
    try {
        const response = await fetch('https://api.webshopapp.com/nl/orders/' + id + '.json', options)
        return await response.json();
    } catch (error) {
        console.log(error);
    }
}

const fetchProducts = async (link) => {
    try {
        const response = await fetch(link, options)
        return await response.json();
    } catch (error) {
        console.log(error);
    }
}

const fetchProduct = async (link) => {
    try {
        const response = await fetch(link, options)
        return await response.json();
    } catch (error) {
        console.log(error);
    }
}

async function getAllProducts() {
    const tempOrder = await fetchOrder(235217584);
    const order = tempOrder["order"];
    const link = order["products"]["resource"]["link"];

    const orderProducts = await fetchProducts(link);
    const products = orderProducts["orderProducts"];

    for (let p in products){
        const tempProduct = products[p];
        const link2 = tempProduct["product"]["resource"]["link"];
        const product = await fetchProduct(link2).then(data => data["product"]);
        product["description"] =  product["description"].replace(/[\r\n]/g, '');
        const desiredProperties = ["fulltitle", "description"];
        const filteredObject = Object.fromEntries(
            Object.entries(product)
                .filter(([key]) => desiredProperties.includes(key))
        );
        filteredObject["variantTitle"] = tempProduct["variantTitle"];
        console.log(filteredObject);
    }
}

getAllProducts();




