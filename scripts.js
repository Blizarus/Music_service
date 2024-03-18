function hideButtonAndShowInput(buttonId, index) {
    var button = document.getElementById(buttonId);
    var input = document.getElementById('input' + index);
    var button_hide = document.getElementById('button_hide' + index);

    if (button && input && button_hide) {
        button.style.display = 'none';
        input.style.display = 'inline-block';
        button_hide.style.display = 'inline-block';
    }
}
function hideButtonAndShowInfo(buttonId, index) {
    var button = document.getElementById(buttonId);

    var info1 = document.getElementById('info1' + index);
    var info2 = document.getElementById('info2' + index);
    var info3 = document.getElementById('info3' + index);
    var info4 = document.getElementById('info4' + index);

    if (button && info1  && info2  && info3  && info4)  {
        button.style.display = 'none';
        info1.style.display = 'block';
        info2.style.display = 'block';
        info3.style.display = 'block';
        info4.style.display = 'block';
    }
}
function hideInputAndShowButton(buttonId, index) {
    var button_hide = document.getElementById(buttonId);
    var input = document.getElementById('input' + index);
    var button = document.getElementById('button' + index);

    if (button && input && button_hide) {
        button_hide.style.display = 'none';
        input.value = '';
        input.style.display = 'none';
        button.style.display = 'inline-block';
    }
}

function redirectToPage(url) {
    window.location.href = url;
}

function playAudio(audioSource) {
    var audioPlayer = document.getElementById('audioPlayer');
    console.log("FFFF");
    if (audioPlayer) {
        audioPlayer.style.display = 'block';
        audioPlayer.src = audioSource;
        audioPlayer.play();
    }
}

function playAudio(audioSource, compositionId) {
    var audioPlayer = document.getElementById('audioPlayer');
    console.log(decodeURIComponent(audioSource));
    if (audioPlayer) {
        audioPlayer.style.display = 'block';
        audioPlayer.src = decodeURIComponent(audioSource);
        audioPlayer.play();
        addStatistic(compositionId);
    }
}


function addStatistic(compositionId) {
    fetch('add_statistic.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'compositionId=' + compositionId,
    })
    .then(response => response.text())
    .then(message => {
        console.log(message);
    })
    .catch(error => console.error('Error:', error));
}
