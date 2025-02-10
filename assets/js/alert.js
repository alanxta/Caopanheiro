window.onload = function alerta() {
    var alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 600);
            window.location.href = history.go(-2);
        }, 1500);
    
    }
};