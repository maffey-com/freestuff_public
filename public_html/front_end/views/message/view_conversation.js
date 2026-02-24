$(function () {
    showMessageHistory(false);

    setInterval(function () {
        showMessageHistory(true);
    }, 15000);

    setTimeout(
        function() {
                $('#input-your_message').focus();
            },
        3000
    );
});

// ios special
document.body.onload = function() {
    document.querySelector('#input-your_message').focus();
};

