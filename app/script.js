const btnProduce = document.querySelector('#btnProduce');
const inputMessage = document.querySelector('#inputMessage');
const inputQueue = document.querySelector('#inputQueue');
const outputQueue = document.querySelector('#outputQueue');
const queue = document.querySelector('#queue');

btnProduce.addEventListener('click', () => {
    send();
});

inputMessage.addEventListener('keyup', validateForm);
inputQueue.addEventListener('keyup', validateForm);

setInterval(() => {
    fetchMessages()
        .then(function (response) {
            queue.innerHTML = response.join('<br>');
        });
}, 1000);

async function produceMessage(message, queue) {
    const response = await fetch("/api/produce.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            message: message,
            queue: queue,
        }),
    });
    return await response.json();
}

async function fetchMessages() {
    const response = await fetch("/api/fetch.php?queue=" + outputQueue.value);
    return await response.json();
}

function validateForm(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        send();
        return;
    }
    btnProduce.disabled = inputMessage.value === '' || inputQueue.value === '';
}

function send() {
    produceMessage(inputMessage.value, inputQueue.value)
        .then(function () {
            inputMessage.value = '';
            btnProduce.disabled = true;
        });
}