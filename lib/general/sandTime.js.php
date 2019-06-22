function sandTime(el, color = 'rgb(38,83,98)') {
    var ctx = el.getContext("2d");

    window.requestAnimFrame = (function(){
        return  window.requestAnimationFrame       ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame    ||
            window.oRequestAnimationFrame      ||
            window.msRequestAnimationFrame
    })();

    ctx.strokeStyle = color;
    ctx.lineWidth = 1;

    function diagonalDraw(massCoor, interval) {
        var i = 0;
        function controllerDraw() {
            ctx.beginPath();
            var xFrom = massCoor[i][0], yFrom = massCoor[i][1], xTo = massCoor[i][2], yTo = massCoor[i][3];
            if(massCoor[i][4] != undefined)
                ctx.strokeStyle = massCoor[i][4];
            if(massCoor[i][5] != undefined)
                ctx.globalCompositeOperation = massCoor[i][5];
            var deltaX = Math.abs(xTo-xFrom);
            var deltaY = Math.abs(yTo-yFrom);
            var hTo = Math.sqrt( Math.pow(deltaX, 2) + Math.pow(deltaY, 2) );
            var h = 0;
            var tg = deltaX > deltaY ? deltaY/deltaX : deltaX/deltaY;
            var sin = Math.sqrt( Math.pow(tg, 2) / (1 + Math.pow(tg, 2)) );
            var x = 0;
            var y = 0;
            ctx.moveTo(xFrom, yFrom);
            function draw() {
                if(Math.floor(h) < Math.floor(hTo))
                {
                    if(deltaX > deltaY)
                    {
                        y = h*sin;
                        x = Math.sqrt( Math.pow(h, 2) - Math.pow(y, 2) );
                    } else {
                        x = h*sin;
                        y = Math.sqrt( Math.pow(h, 2) - Math.pow(x, 2) );
                    }
                    ctx.lineTo( xFrom + ((xTo-xFrom)>0 ? x : -x) , yFrom + ((yTo-yFrom)>0 ? y : -y) );
                    ctx.stroke();
                    h += interval;
                    requestAnimFrame(draw);
                }
                else
                {
                    ctx.lineTo( xTo, yTo );
                    ctx.stroke();
                    i++;
                    if(i < massCoor.length)
                    {
                        controllerDraw();
                    }
                    else
                    {
                        el.style.transform = 'rotate(360deg)';
                        setTimeout(function () {
                            ctx.clearRect(0,0,80,80);
                            ctx.strokeStyle = color;
                            el.style.transform = '';
                            i = 0;
                            controllerDraw();
                        }, 1000);
                    }
                }
            }
            requestAnimFrame(draw);
        }
        controllerDraw();
    }
    diagonalDraw([
        [32, 4, 4, 32, undefined, 'source-over'],
        [4, 32, 17, 45],
        [17, 45, 36, 45],
        [36, 45, 36, 63],
        [36, 63, 49, 77],
        [49, 77, 77, 49],
        [77, 49, 63, 36],
        [63, 36, 45, 36],
        [45, 36, 45, 17],
        [45, 17, 32, 4],
        [32, 13, 13, 32],
        [33, 14, 14, 33],
        [34, 15, 15, 34],
        [35, 16, 16, 35],
        [36, 17, 17, 36],
        [37, 18, 18, 37],
        [38, 19, 19, 38],
        [39, 20, 20, 39],
        [40, 21, 21, 40],
        [41, 22, 22, 41],
        [41, 24, 24, 41],
        [41, 26, 26, 41],
        [41, 28, 28, 41],
        [41, 30, 30, 41],
        [41, 32, 32, 41],
        [41, 34, 34, 41],
        [41, 36, 36, 41],
        [41, 38, 38, 41],
        [41, 40, 40, 41],
        [41, 42, 42, 41],
        [41, 46, 46, 41],
        [41, 48, 48, 41],
        [41, 50, 50, 41],
        [41, 52, 52, 41],
        [41, 54, 54, 41],
        [41, 56, 56, 41],
        [41, 58, 58, 41],
        [41, 60, 60, 41],
        [42, 61, 61, 42],
        [43, 62, 62, 43],
        [44, 63, 63, 44],
        [45, 64, 64, 45],
        [46, 65, 65, 46],
        [47, 66, 66, 47],
        [48, 67, 67, 48],
        [49, 68, 68, 49],
        [50, 69, 69, 50],
        [32, 13, 13, 32, 'rgb(0,0,0)', 'destination-out'],
        [33, 14, 14, 33],
        [34, 15, 15, 34],
        [35, 16, 16, 35],
        [36, 17, 17, 36],
        [37, 18, 18, 37],
        [38, 19, 19, 38],
        [39, 20, 20, 39],
        [40, 21, 21, 40],
        [41, 22, 22, 41],
        [41, 24, 24, 41],
        [41, 26, 26, 41],
        [41, 28, 28, 41],
        [41, 30, 30, 41],
        [41, 32, 32, 41],
        [41, 34, 34, 41],
        [41, 36, 36, 41],
        [41, 38, 38, 41],
        [41, 40, 40, 41],
        [41, 42, 42, 41]
    ], 5);
}

function gritPreloader(el, color = 'rgb(255, 228, 196)') {
    if(el.getElementsByClassName('gritPreloader')[0] != undefined) {
        return;
    }
    var ctx2d = document.createElement('canvas');
        ctx2d.width = 80;
        ctx2d.height = 80;
        ctx2d.style.cssText = " \
            width: 80px; \
            height: 80px; \
            transition: all 1s; \
        ";

    var can = document.createElement('div');
        can.className = 'gritPreloader';
        can.style.cssText = " \
            display: flex; \
            align-items: center; \
            justify-content: center; \
            position: absolute; \
            top: 0px; \
            left: 0px; \
            right: 0px; \
            bottom: 0px; \
            background-color: rgba(0,34,64,0.7); \
        ";
    can.appendChild(ctx2d);
    el.appendChild(can);
    sandTime(ctx2d, color);
}
