<script>
    var lang = '<?=LANG ?>';
    var name = '<?=$auth->name?>';
    var avatar = '<?=$auth->avatar?>';
    var dateStart = Date.now();
    var editor;
    window.addEventListener('load', function () {
//        delPre();
        new Scrollbar(document.getElementsByClassName('scroll')[0]);
        openPage();
//        setTimeout(function() {
//            getId('generalPreloader').style.opacity = 0;
//        }, 4000);
//        setTimeout(function() {
//            document.body.removeChild(getId('generalPreloader'));
//        }, 4500);
//        chat();
    });

    function openPage(event, page = '<?=PAGE ?>', dir = '<?=DIR ?>', lang = '<?=LANG ?>')
    {
        if(event != undefined)
            event.preventDefault();

        cPre(getId('content'));

        if(event != undefined)
        {
            var child = document.getElementsByClassName('menu');
            for(i = 0; i < child.length; i++)
            {
                if(child[i].classList.contains('active'))
                {
                    child[i].classList.remove('active');
                    i = child.length;
                }
            }
            if(event.target.classList.contains('menu'))
                event.target.classList.add('active');
            else
                event.target.parentNode.classList.add('active');
        }

        callingA('<?=DIR; ?>lib/php/openPage', false, '&page=' + page + '&dir=' + dir + '&lang=' + lang, function(text) {
            if(getId('checkmenu') != undefined){ getId('checkmenu').checked = false; }
            if(getId('newScript') != undefined)
            {
                document.head.removeChild(getId('newScript'));
            }
            var newScript = document.createElement('script');
            newScript.type = 'text/javascript';
            newScript.setAttribute('id', 'newScript');
            newScript.text = text;
            document.head.insertBefore(newScript, document.getElementsByTagName('title')[0]);
            <?php
                if($auth->type == 'admin') {
                    require('editorscript.js');
                }
            ?>
        });

//        callingA('<?//=DIR; ?>//lib/php/sql_get_titlePage', false, '&page=' + page + '&lang=<?//=LANG?>//', function(text) {
//            document.title = text;
//        });

        var pathPage = '';
        if(page == '/' || page == '')
            page = '';
        else
            pathPage = '/' + page;

        history.pushState('', '', '/' + lang + pathPage);

        //scrollAnim();
    }

    function getNameCategory(key, game) {
        if(key == 'Качество' || key == 'Quality') {
            if(game == 'csgo') {
                return 'rarity';
            } else {
                return 'quality';
            }
        } else if(key == 'Ячейка' || key == 'Slot') {
            return 'cell';
        } else if(key == 'Тип' || key == 'Type') {
            return 'type';
        } else if(key == 'Категория' || key == 'Category') {
            return 'category';
        } else if(key == 'Герой' || key == 'Hero') {
            return 'hero';
        } else if(key == 'Оформление' || key == 'Exterior') {
            return 'exterior';
        } else if(key == 'Набор' || key == 'Collection') {
            return 'collection';
        } else if(key == 'Оружие' || key == 'Weapon') {
            return 'weapon';
        } else if(key == 'Редкость' || key == 'Rarity') {
            return 'rarity';
        } else if(key == 'Цвет граффити' || key == 'Graffiti Color') {
            return 'graffiti';
        }
    }
    
    function cPre(el) {
        
    }

    function sandPreloader(el, deletePre = false) {
        if(deletePre) {
            if(getId('parentSandPre') != undefined) {
                getId('parentSandPre').parentNode.removeChild(getId('parentSandPre'));
            }
            return;
        }
        var can = document.createElement('div');
        can.setAttribute('id', 'parentSandPre');
        can.innerHTML = '<canvas id="timePreloader" width="80" height="80"></canvas>';
        el.appendChild(can);
        sandTime(getId('timePreloader'));
    }

    function delPre(type = 'preloader-back')
    {
        getId(type).parentNode.removeChild(getId(type));
    }

    function chooseLang(event)
    {
        event.preventDefault();
        if(event.target.innerHTML == 'eng')
        {
            if(!~String(window.location).indexOf('.ru/en'))
            {
                window.location.href = String(window.location).replace('.ru/ru', '.ru/en');
            }
        }
        else if(event.target.innerHTML == 'ru')
        {
            if(!~String(window.location).indexOf('.ru/ru'))
            {
                window.location.href = String(window.location).replace('.ru/en', '.ru/ru');
            }
        }
    }

    function deleteActive(event)
    {
        if(event != undefined)
        {
            const el = event.target || event;
            el.parentNode.getElementsByClassName('active')[0].classList.remove('active');
            el.classList.add('active');
        }
    }

    function scrollAnim(event) {
        window.requestAnimFrame = (function(){
            return  window.requestAnimationFrame       ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame    ||
                window.oRequestAnimationFrame      ||
                window.msRequestAnimationFrame
        })();

        var pageY = window.pageYOffset || document.documentElement.scrollTop;
        var to = getId('content').offsetTop;
        function fullAnimationScroll() {
            document.getElementsByClassName('scrollbar')[0].scrollTo(0, pageY);
            pageY += 10;
            if(pageY < to)
            {
                window.requestAnimationFrame(fullAnimationScroll);
            }
        }
        window.requestAnimationFrame(fullAnimationScroll);
    }

    function getClass(obj) {
        return {}.toString.call(obj).slice(8, -1);
    }

    function setAbsolute(el) {
        if(getComputedStyle(el).position != 'absolute' && getComputedStyle(el).position != 'fixed') {
            el.style.position = 'relative';
        }
    }

    function scrolling(thisId, functionName)
    {
        var scrolled = window.pageYOffset || document.documentElement.scrollTop;
        if(scrolled + document.body.clientHeight/2 > document.getElementById(thisId).offsetTop)
        {
            functionName();
            window.removeEventListener('scroll', totalEarned);
        }
    }

    function getId(id)
    {
        return document.getElementById(id);
    }
    function getClass(clss) {
        return document.getElementsByClassName(clss);
    }

    function callingA(file, isFile, postMess, handler)
    {
        var xmlhttp;
        if (window.XMLHttpRequest)
        {
            xmlhttp=new XMLHttpRequest();
        }
        else
        {
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.open("POST", file + ".php",true);
        if(!isFile)
        {
            xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=utf-8");
        }
        xmlhttp.send(postMess);
        xmlhttp.onreadystatechange = function()
        {
            if (xmlhttp.readyState != 4) return;

            if(xmlhttp.status == 200)
            {
                handler(xmlhttp.responseText);
            }
        }
    }

    <?php
    require('lib/general/sandTime.js.php');
    require('lib/general/Scrollbar.js.php');
    require('lib/general/cookie.js.php');
    require('lib/general/Chat.js.php');
    require('lib/general/Chunk.js.php');
    require('lib/general/autoResize.js.php');
    ?>
</script>