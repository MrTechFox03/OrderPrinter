const apiKey = "79fa822521904854874faa60d523540e";
const apiSecret = "f032e15faf8e85d4b848e652017630ed";

const options = {
    method: 'GET',
    headers: new Headers({
        'Content-Type': 'application/json',
        'Authorization': 'Basic ' + btoa(`${apiKey}:${apiSecret}`)
    })
};