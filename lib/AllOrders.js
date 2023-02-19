const apiKey = "79fa822521904854874faa60d523540e";
const apiSecret = "f032e15faf8e85d4b848e652017630ed";

const options = {
    method: 'GET',
    headers: new Headers({
        'Content-Type': 'application/json',
        'Authorization': 'Basic ' + btoa(`${apiKey}:${apiSecret}`),
    })
};

let page = 1;

const orders = async () => {
    try {
        const response = await fetch('https://api.webshopapp.com/nl/orders.json?page=' + 1, options)
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

async function fetchorders(){
    let test = await orders();
    console.log(test);
}

fetchorders();
async function getAllProducts(id) {
    const jsonOrder = await fetchOrder(id);
    const order = jsonOrder["order"];
    const customer = order["firstname"] + " " + order["middlename"] + " " + order["lastname"]
    const link = order["products"]["resource"]["link"];

    const orderProducts = await fetchProducts(link);
    const products = orderProducts["orderProducts"];
    let list = [];
    for (let p in products){
        const tempProduct = products[p];
        const link2 = tempProduct["product"]["resource"]["link"];
        const product = await fetchProduct(link2).then(data => data["product"]);
        product["description"] = product["description"].replace(/[\r\n]/g, '');
        const desiredProperties = ["fulltitle", "description"];
        const filteredObject = Object.fromEntries(
            Object.entries(product)
                .filter(([key]) => desiredProperties.includes(key))
        );
        filteredObject["variantTitle"] = tempProduct["variantTitle"];
        filteredObject["customer"] = customer;
        filteredObject["orderId"] = order["id"];
        list.push(filteredObject)
    }
    console.log(list)
    return list;
}
//getAllProducts(235217584)

function makePDFsOfOrderId(id) {
    const products = getAllProducts(id);

    for (let p in products) {
        const product = products[p];
        const id = product["orderId"];
        const customer = product["customer"];
        const title = product["fulltitle"];
        const description = product["description"];
        const variant = product["variantTitle"];

        makeDocument(id, customer, title, description, variant);
    }
}
//makePDFsOfOrderId(document.getElementById("orderId").value);
function logText(text) {
    console.log(text);
}


