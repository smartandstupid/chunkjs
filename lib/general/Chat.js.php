function chat() {
    /**
     * @var startMess {number} Указывает на первое загруженное сообщение
     * Загружаем 40 сообщений
     * @var lastMess {number} Идентификатор последнего сообщения
     * @var lastWriter {string} Последний кто написал сообщение
     * Если при добавлении нового сообщения lastWriter совпадает с написавшим
     * то сообщение добавляется в последний стек
     * @var open {bool} Заполнен ли ранее был чат
     */
    var startMess = 0;
    var lastMess = 0;
    var lastWriter = '';

    var elChat = document.createElement('div');
    elChat.innerHTML =
        '<div id="open-chat">Чат&#9998;</div>' +
        '<div id="chat"><p>Общий чат</p>' +
        '<div id="chat-stack">' +
        '</div>' +
        '<div id="chat-enter">' +
        '<textarea id="textChat">Shift+Enter - перенос строки...</textarea>' +
        '</div>' +
        '<div id="previMess"></div>' +
        '</div>';
    document.body.appendChild(elChat);

    getId('textChat').addEventListener('keyup', sendMess);
    getId('textChat').addEventListener('focus', function (event) {
        if(event.target.value == 'Shift+Enter - перенос строки...') {
            event.target.value = '';
        }
    });
    getId('textChat').addEventListener('blur', function (event) {
        if(event.target.value.replace(/\r|\n|\s/g, '') == '') {
            event.target.value = 'Shift+Enter - перенос строки...';
        }
    });
    getId('open-chat').addEventListener('click', openChat);
    // var textEnter = window.Scrollbar;
    //     textEnter.init(document.querySelector('#chat-enter'));
    // autoResize(getId('textChat'));

    function openChat(event) {
        // document.body.style.overflow = 'hidden';
        if(!getId('chat').classList.contains('active')) {
            getId('chat').classList.add('active');
            var back = document.createElement('div');
                back.addEventListener('click', function () {
                    getId('chat').classList.remove('active');
                    document.body.removeChild(getId('back'));
                });
                back.setAttribute('id', 'back');
            document.body.appendChild(back);
            messInspect(function() {
                scrollbar.setPosition(0, scrollbar.getSize().content.height);
            });
        } else {
            getId('chat').classList.remove('active');
            document.body.removeChild(getId('back'));
        }
    }

    function messInspect(callback = undefined) {
        callingA('<?=DIR?>lib/general/chat/inspection', false, '&lastMess=' + lastMess, function (text) {
            // Возвращает JSON массив новых сообщений
            del2dContext(getId('chat-stack').getElementsByClassName('scroll-content')[0]);
            getId('previMess').innerHTML = '';
            try {
                var messArr = JSON.parse(text);
            } catch(e)  {
                getId('chat-stack').getElementsByClassName('scroll-content')[0].innerHTML = 'Ошибка: ' + text;
            }
            if(messArr['mess'].length == 0 && lastMess == 0) {
                getId('chat-stack').getElementsByClassName('scroll-content')[0].innerHTML = '<p>В чате нет сообщений</p>';
            } else if(messArr['mess'].length != 0) {
                var messInner = '';
                for (var i = 0; i < messArr['mess'].length; i++) {
                    if(lastWriter == messArr['mess'][i].name && Number(lastMess) + 1 == messArr['mess'][i].id) {
                        var mess = document.createElement('li');
                            mess.className = 'newMess';
                            mess.innerHTML = messArr['mess'][i].mess;
                        var messStack = getId('chat-stack').getElementsByClassName('mess-stack');
                            messStack[messStack.length-1].appendChild(mess);
                            messStack[messStack.length-1].appendChild((document.createElement('br')));
                        lastMess = messArr['mess'][i].id;
                    } else {
                        if(Number(lastMess) + 1 == messArr['mess'][i].id) {
                            var p = document.createElement('p');
                                p.className = 'last';
                                p.innerHTML = '...';
                            getId('chat-stack').getElementsByClassName('scroll-content')[0].appendChild(p);
                        }
                        var ul = document.createElement('ul');
                            ul.className = 'mess-stack';
                            if(messArr['mess'][i].name == name) ul.classList.add('me');
                            ul.innerHTML =
                                '<li class="messIcon" style="background-image:url(\'' + messArr['mess'][i].icon + '\');"></li>' +
                                '<li class="newMess">' +
                                '<p>' + messArr['mess'][i].name + '</p><p>' + messArr['mess'][i].date + '</p>' +
                                messArr['mess'][i].mess + '</li>';
                        getId('chat-stack').getElementsByClassName('scroll-content')[0].appendChild(ul);
                        lastWriter = messArr['mess'][i].name;
                        lastMess = messArr['mess'][i].id;
                    }
                }
                if(callback != undefined) callback();
            }
            if(getId('chat').classList.contains('active')) {
                setTimeout(messInspect, 2000);
            }
        });
    }

    function sendMess(event) {
        if(event.shiftKey)
        {
            if(event.keyCode==13) return;
        }
        else if(event.keyCode == 13) {
            event.preventDefault();
            if(event.target.value.replace(/\r|\n|\s/g, '') != '') {
                var previMess = document.createElement('div');
                    previMess.innerHTML = event.target.value;
                getId('previMess').appendChild((document.createElement('br')));
                getId('previMess').appendChild(previMess);
                callingA('<?=DIR?>lib/general/chat/chatSend', false, '&mess=' + event.target.value, function (text) {
                    if(text != '') {
                    }
                });
                event.target.value = '';
            }
        }
    }

    function getCaret(el) {
        if (el.selectionStart) {
            return el.selectionStart;
        } else if (document.selection) {
            el.focus();

            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }

            var re = el.createTextRange(),
                rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    }

    function getPosInRow(el) {
        var caret = getCaret(el);
        var text = el.value.substr(0, caret).replace(/^(.*[\n\r])*([^\n\r]*)$/, '$2');

        console.log(text);
        return text.length;
    }
}