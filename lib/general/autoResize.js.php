function autoResize(el) {
    textareaResize = function (event) {
        var newText = document.createElement('div');
        var computedStyle = getComputedStyle(el);
            newText.style.cssText = 'width: ' + computedStyle.width + '; font-size: ' + computedStyle.fontSize + '; position: absolute; top: -2000px';
        newText.innerHTML = el.value.replace(/\n/g, "<br />") + '<br />f';
        document.body.appendChild(newText);
        el.style.height = newText.clientHeight + 'px';
        document.body.removeChild(newText);
        getId('chat-enter').scrollIntoView(true);
    };
    el.addEventListener('keyup', textareaResize);
}