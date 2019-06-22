var editor;
window.addEventListener('load', function () {
    editor = ContentTools.EditorApp.get();
    editor.init('*[data-editable]', 'main-content');

    editor.addEventListener('saved', function (ev) {
        new ContentTools.FlashUI('ok');
    });
});