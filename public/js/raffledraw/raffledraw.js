// function GetAllEntry() {
//     $.ajax({
//         url: "/api/get-raflle-entry", // Replace with your endpoint URL
//         type: "GET",
//         success: function (response) {
//             console.log(response)
//         },
//         error: function (xhr, status, error) {
//             console.error("Error fetching data:", error);
//         },
//     });
// }

function GetAllClusterSelect() {
    $.ajax({
        url: "/api/get-cluster", // Replace with your endpoint URL
        type: "GET",
        success: function (response) {
            const data = response.data;

            const selectElement = document.getElementById("selectCluster");

            selectElement.innerHTML = "";

            const defaultOption = document.createElement("option");
            defaultOption.text = "Select a cluster";
            defaultOption.value = "";
            selectElement.appendChild(defaultOption);

            data.forEach((element) => {
                const newOption = document.createElement("option");
                newOption.value = element.cluster_id;
                newOption.text = element.cluster_name;
                selectElement.appendChild(newOption);
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
        },
    });
}
let serial_number = [];
let cluster_id = '';
function SelectEntry(id){
    cluster_id = id.value;
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    const formData = new FormData();
    formData.append("_token", csrfToken);
    formData.append('id',id.value);

    $.ajax({
        url: "/api/get-raflle-entry",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if(response.length == 0){
                document.getElementById("drawButton").disabled=true;
            }else{
                document.getElementById("drawButton").disabled=false;
            }
            serial_number.length=0;
            response.forEach((element) => {
                serial_number.push(element.serial_number);
            });
        },
        error: function (xhr, status, error) {
            console.error("Error posting data:", error);
        },
    });
    
}

const raffleInput = document.getElementById("raffleInput");
const drawButton = document.getElementById("drawButton");

function startRaffle() {
    drawButton.disabled = true;
    let counter = 0;

    // Shuffle names every 100ms
    const shuffleInterval = setInterval(() => {
        const randomIndex = Math.floor(Math.random() * serial_number.length);
        raffleInput.value = serial_number[randomIndex];
        counter++;
    }, 100);

    // After 3 seconds, pick the final name
    // setTimeout(() => {
    //     clearInterval(shuffleInterval);
    //     const finalIndex = Math.floor(Math.random() * serial_number.length);
    //     raffleInput.value = serial_number[finalIndex];
    //     drawButton.disabled = false;

    //     // Trigger confetti when the final name is picked
    //     triggerConfetti();
    // }, 5000);

    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    const formData = new FormData();
    formData.append("_token", csrfToken);
    formData.append("id", cluster_id);

    $.ajax({
        url: "/api/raffle-draw",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response)
            clearInterval(shuffleInterval);
            drawButton.disabled = false;
        },
        error: function (xhr, status, error) {
            console.error("Error posting data:", error);
        },
    });

}

function triggerConfetti() {
    const duration = 2 * 1000; // 2 seconds
    const end = Date.now() + duration;

    (function frame() {
        confetti({
            particleCount: 10,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
        });
        confetti({
            particleCount: 10,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();
}

drawButton.addEventListener("click", startRaffle);

$(document).ready(function () {
    GetAllClusterSelect();
    if (serial_number.length == 0) {
        document.getElementById("drawButton").disabled = true;
    } else {
        document.getElementById("drawButton").disabled = false;
    }
});
