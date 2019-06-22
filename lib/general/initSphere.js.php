let rad = 135;
let nameElem = '';
let earned = '';
if(document.getElementById('profile-earned-today') != undefined) {
    nameElem = 'profile-earned-today';
    earned = 'Сегодня заработано';
} else {
    nameElem = 'bonus-page-total-earned';
    earned = 'Всего заработано';
}
const canvas = document.getElementById(nameElem);
const context = canvas.getContext("2d");
let x = canvas.width / 2;
let y = canvas.height / 2;
let radius = 90;
let startAngle = (Math.PI/180)*135;
let endAngle = (Math.PI/180)*405;

function fillText() {
    context.strokeStyle = 'rgb(0,99,132)';
    context.lineWidth = 1;
    context.beginPath();
    context.moveTo(
        (radius-10)*Math.cos(startAngle)+canvas.width/2,
        (radius-10)*Math.sin(startAngle)+canvas.height/2
    );
    context.lineTo(
        (radius+10)*Math.cos(startAngle)+canvas.width/2,
        (radius+10)*Math.sin(startAngle)+canvas.height/2
    );
    context.stroke();
    context.beginPath();
    context.moveTo(
        (radius-10)*Math.cos(endAngle)+canvas.width/2,
        (radius-10)*Math.sin(endAngle)+canvas.height/2
    );
    context.lineTo(
        (radius+10)*Math.cos(endAngle)+canvas.width/2,
        (radius+10)*Math.sin(endAngle)+canvas.height/2
    );
    context.stroke();
    context.beginPath();
    context.arc(canvas.width/2, canvas.height/2, radius-10, startAngle, endAngle);
    context.stroke();
    context.beginPath();
    context.arc(canvas.width/2, canvas.height/2, radius+10, startAngle, endAngle);
    context.stroke();

    context.fillStyle = 'white';
    context.font = '13px Arial';
    context.textAlign = 'center';
    context.fillText(earned, canvas.width/2, 100);
    context.fillStyle = 'rgb(255,238,71)';
    context.font = '26px Arial';
    context.fillText('0 ₽', canvas.width/2, 130);
}

window.requestAnimFrame = (function(){
    return  window.requestAnimationFrame       ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame    ||
        window.oRequestAnimationFrame      ||
        window.msRequestAnimationFrame
})();

function scrolling(thisId, functionName)
{
    let scrolled = document.body.children[0].pageYOffset || document.body.children[0].scrollTop;
    if(scrolled + window.innerHeight/2 > getId(thisId).offsetTop + getId(nameElem).offsetParent.offsetTop)
    {
        canvas.className = 'animated bounceIn';
        functionName();
        return true;
    }
    return false;
}

function animloop() {
    if(rad <= 270)
    {
        let context = canvas.getContext("2d");
        context.clearRect(0,0,canvas.width,canvas.height);
        fillText();
        context.restore();
        rad = rad+2;
        let endAngle = (Math.PI/180)*rad;

        context.strokeStyle = 'rgb(0,99,132)';
        context.beginPath();
        context.lineWidth = 20;
        context.arc(x, y, radius, startAngle, endAngle);
        context.stroke();
        requestAnimFrame(animloop);
    }
}

let mainComp = document.body.children[0];
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    mainComp = window;
}
mainComp.addEventListener('scroll', function startPaint() {
    if(scrolling(nameElem, animloop)) {
        mainComp.removeEventListener('scroll', startPaint);
    }
});
