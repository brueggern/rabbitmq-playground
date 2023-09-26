const btnProduce = document.querySelector('#btnProduce');
const inputMessage = document.querySelector('#inputMessage');
const inputQueue = document.querySelector('#inputQueue');
const queue = document.querySelector('#queue');
const doConsume = document.querySelector('#doConsume');

btnProduce.addEventListener('click', () => {
    send();
});

inputMessage.addEventListener('keyup', validateForm);

setInterval(() => {
    if (doConsume.checked) {
        fetchMessages()
            .then(function (response) {
                queue.innerHTML = response.join('<br>');
            });
    }
}, 1000);

async function produceMessage(message) {
    const response = await fetch("/api/produce.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            message: message,
        }),
    });
    return await response.json();
}

async function fetchMessages() {
    const response = await fetch("/api/fetch.php");
    return await response.json();
}

function validateForm(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        send();
        return;
    }
    btnProduce.disabled = inputMessage.value === '';
}

function send() {
    produceMessage(inputMessage.value)
        .then(function () {
            inputMessage.value = '';
            btnProduce.disabled = true;
        });
}