let timerOn = true;

function timer(remaining) {
    var m = Math.floor(remaining / 60);
    var s = remaining % 60;

    m = m < 10 ? '0' + m : m;
    s = s < 10 ? '0' + s : s;
    document.getElementById('timer').innerHTML = m + ':' + s;
    remaining -= 1;

    if(remaining >= 0 && timerOn) {
        setTimeout(function() {
            timer(remaining);
        }, 1000);
        return;
    }

    if(!timerOn) {
        // Do validate stuff here
        return;
    }

    // Do timeout stuff here
    $(function() {
        setTimeout(function() {
            $('#sendCodeButton').show();
        });
        $('#sendCodeButton').click(function() {
            $('#sendCode').hide();
        });
    });
}

timer(120);
