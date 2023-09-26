const btnProduce = document.querySelector('#btnProduce');
const btnClear = document.querySelector('#btnClear');
const inputMessage = document.querySelector('#inputMessage');
const inputQueue = document.querySelector('#inputQueue');
const inputExchange = document.querySelector('#inputExchange');
const queue = document.querySelector('#queue');
const doConsume = document.querySelector('#doConsume');

btnProduce.addEventListener('click', () => {
    send();
});

btnClear.addEventListener('click', () => {
    clear();
});

inputMessage.addEventListener('keyup', validateForm);
inputQueue.addEventListener('change', validateForm);
inputExchange.addEventListener('change', validateForm);

setInterval(() => {
    if (doConsume.checked) {
        fetchMessages()
            .then(function (response) {
                queue.innerHTML = response.join('<br>');
            });
    }
}, 1000);

async function produceMessage(message, queue, exchange) {
    const response = await fetch("/api/produce.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            message: message,
            queue: queue,
            exchange: exchange,
        }),
    });
    return await response.json();
}

async function fetchMessages() {
    const response = await fetch("/api/fetch.php");
    return await response.json();
}

async function clearLogs() {
    const response = await fetch("/api/clear.php");
    return await response.json();
}

function validateForm(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        send();
        return;
    }
    btnProduce.disabled = inputMessage.value === '' || inputQueue.value === '' || inputExchange.value === '';
}

function send() {
    produceMessage(inputMessage.value, inputQueue.value, inputExchange.value)
        .then(function () {
            inputMessage.value = '';
            btnProduce.disabled = true;
        });
}

function clear() {
    clearLogs().then(function () {
        queue.innerHTML = '';
    });
}